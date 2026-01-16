<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShortLink;
use App\Models\Ticket;
use App\Services\PDFMerger\PDFMerger;
use Dompdf\Dompdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request){
      $user = auth()->user();
      $tickets = Ticket::select()->where('file_path', '!=', null)->whereDoesntHave('parent');
      if ($request->shipping){
        $shipping = $request->shipping;
        $tickets->where('delivery_code', $shipping);
      }
      if(!$request->all_managers){
        $tickets->where('file_name', 'like', '%'.$user->email.'.pdf');
      }
      $tickets = $tickets->orderBy('id', 'desc')->paginate(50);
      //dd($tickets);

      foreach($tickets as $ticket){
        $ticket_data = json_decode($ticket->data, true);
        $ticket->data = $ticket_data;
        $file_path = public_path().$ticket->file_path;
        if (file_exists($file_path)) {
          $count_pages = 0;
          $file = stat($file_path);
          $fp = fopen($file_path, 'r');
          if ($fp) {
            while(!feof($fp)) {
              $line = fgets($fp,255);

              if (preg_match('|/Count [0-9]+|', $line, $matches)){

                preg_match('|[0-9]+|', $matches[0], $matches2);
                if ($count_pages < $matches2[0]) {
                  $count_pages = trim($matches2[0]);
                }
              }
            }
            fclose($fp);


          }
          if(strpos($ticket->file_name, $user->email)){
            $author = 1;
          }else{
            $author = 0;
          }
          $ticket->is_author = $author;
          $ticket->size = get_size($file['size']);
          if($ticket->orders()->select()->whereHas('items', function(Builder $query){
            $query->whereIn('product_id', [259,260,309,310]);
          })->count()&&!isset($ticket->data['builder_file'])){
            $ticket->file_creating = true;
          }else{
            $ticket->file_creating = false;
          }
          $ticket->ctime = $file['ctime'];
          $extra_pages = $ticket_data['extra_pages'] ?? 0;
          if ($count_pages){
            $ticket->count_pages = ($count_pages ?? 0) - $extra_pages;
          }
        }
      }
      $seo = [
          'title' => 'Этикетки ШК'
      ];
      if ($request->a == 'cdek') {
        $seo['title'] = 'Этикетки ШК СДЭК';
      }

      $doubles = DB::select('SELECT `order_id`, COUNT(`order_id`) AS `count` FROM `order_ticket` GROUP BY `order_id` HAVING  `count` > 1');
      $errors = [];
      if (!empty($doubles)){
        $errors = ['В базе данных найдены дубли'];
      }
      return view('template.admin.tickets.index', compact('seo', 'tickets'))->withErrors($errors);
    }

    public function batchUpdate(Request $request){
      $request->validate([
          'ticket_ids' => ['required', 'array'],
          'action' => ['required', 'string']
      ]);
      $action = $request->action;
      $ticket_ids = $request->ticket_ids;
      $tickets = Ticket::select('id', 'data')->whereIn('id', $ticket_ids)->get();

      foreach ($tickets as $ticket) {
        $ticket_data = json_decode($ticket->data, true);
        if ($action == 'print_true') {
          $ticket_data['printed'] = true;
        } elseif ($action == 'print_false') {
          $ticket_data['printed'] = false;
        } elseif ($action == 'group') {
          return redirect()->route('admin.tickets.ticketsGroups', ['ticket_ids' => $ticket_ids]);
        }
        $ticket->update([
            'data' => json_encode($ticket_data)
        ]);
      }
      return back()->with([
          'status' => 'Статус файлов успешно обновлен'
      ]);
    }
    public function ticket_comment(Request $request, Ticket $ticket){
      if (!empty($ticket->data)) {
        $data = json_decode($ticket->data, true);
      } else {
        $data = [];
      }
      $data['comment'] = $request->comment;
      $ticket->update([
          'data' => json_encode($data)
      ]);
      return back()->with([
          'status' => 'Комментарий добавлен'
      ]);
    }

    public function ticket_split(Ticket $ticket){
      $tickets = $ticket->tickets;
      foreach($tickets as $key => $t){
        if($t->data){
          $tickets[$key]->data = json_decode($t->data, true);
        }
      }
      return view('admin.tickets.split', compact('tickets'));
    }

    public function ticketsGroups(Request $request){
      $request->validate(['ticket_ids' => 'required']);
      $tickets = Ticket::whereIn('id', $request->ticket_ids)->limit(50)->get();
      $pdf_files = [];
      $pdf = new PDFMerger();
      $i = 1;

      $shipping = $tickets[0]->delivery_code;
      $items_count = 0;
      $big_ticket = Ticket::create([
          'file_name' => time(),
          'file_path' => null,
          'items_count' => 0,
          'delivery_code' => $shipping
      ]);
      foreach($tickets as $key => $ticket){
        $ticket_data = json_decode($ticket->data, true);
        if($ticket->tickets()->count()||$ticket->parent||(isset($ticket_data['printed'])&&$ticket_data['printed'])||$shipping!=$ticket->delivery_code){
          if($ticket->tickets()->count()){
            echo $ticket->id.' count'.$ticket->tickets()->count().'<br/>';
          }elseif($ticket->parent){
            echo $ticket->id.' parent'.$ticket->parent->id.'<br/>';
          }elseif(isset($ticket_data['printed'])&&$ticket_data['printed']){
            echo $ticket->id.' printed<br/>';
          }elseif($shipping!=$ticket->delivery_code){
            echo $ticket->id.' - '.$shipping.' / '.$ticket->delivery_code.'<br/>';
          }


          continue;
        }
        $order_ticket = public_path().$ticket->file_path;
        if (file_exists($order_ticket)) {
          $pdf_files[] = $order_ticket;
          $pdf->addPDF($order_ticket, 'all');
        }else{
          continue;
        }
        $items_count += $ticket->items_count;
//        $order_ticket_cart = public_path().'/files/boxberry/temp/'.$order->id.'_cart.pdf';
//        if (file_exists($order_ticket_cart)) {
//          $extra_pages++;
//          $pdf_files[] = $order_ticket_cart;
//          $pdf->addPDF($order_ticket_cart, 'all');
//        }
        $ticket->update([
            'ticket_id' => $big_ticket->id
        ]);
        if ($i % 20 == 0 || $i == $tickets->count()){

          $file_path = '/files/big_tickets/'.$shipping.'';
          if (!file_exists(public_path().$file_path)) {
            mkdir(public_path().$file_path, 0777, true);
          }
          $file_name = time().'_'.auth()->user()->email.'.pdf';
          $pdf->merge('file', public_path().$file_path.'/'.$file_name);
          $big_ticket->update([
              'file_name' => $file_name,
              'file_path' => $file_path.'/'.$file_name,
              'items_count' => $items_count,
          ]);
          break;
        }
        $i++;
      }
      if (!$big_ticket->file_path){
        $big_ticket->delete();
        dd($i);
        return back()->with([
            'warning' => 'файлы не объеденены '.$i
        ]);
      }
      return back()->with([
          'status' => 'Объединили '.$big_ticket->tickets->count().' файлов'
      ]);
    }
    private function getCode(){
      $code = Str::random(5);
      if(ShortLink::where('slug', $code)->count()){
        $this->getCode();
      }
      return $code;
    }
    public function invoice(Ticket $ticket ){
      if ($ticket->tickets()->count()){
        $orders = null;
        foreach($ticket->tickets as $child_ticket){
          $t_orders = $child_ticket->orders()->select('id', 'data_cart')->get();
          if (!$orders) {
            $orders = $t_orders;
          }else{
            foreach($t_orders as $t_order){
              $orders->push($t_order);
            }
          }
        }
      }else{
        $orders = $ticket->orders()->select('id', 'data_cart')->get();
      }

      $orders_ids = $orders->pluck('id')->toArray();
      $link = route('admin.orders.index', [
          'ticket_id' => $ticket->id
      ]);
      $short_link_obj = ShortLink::select()->where('link', $link)->first();
      if (!$short_link_obj){
        $code = $this->getCode();
        $short_link_obj = ShortLink::create([
            'slug' => $code,
            'link' => $link,
            'data' => [
                'ticket_id' => $ticket->id
            ]
        ]);
      }
      $short_link = route('redirect', $short_link_obj->slug);
      // генерируем qr
      $img_path = public_path().'/img/qr';
      $img_name = '/qr_ticket-'.$ticket->id.'.png';
      if (!file_exists($img_path.$img_name)) {
        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($short_link)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        if (!file_exists($img_path)) {
          mkdir($img_path, 0777, true);
        }
        $result->saveToFile($img_path.$img_name);
        $linkQrUri = $result->getDataUri();
      }else{
        $linkQrUri = '/img/qr'.$img_name;
      }
      $cart = [];
      foreach($orders as $order){
        $order_cart = $order->data_cart;
        foreach($order_cart as $item){
          $product = Product::query()->where('id', $item['id'])->select('price')->first();
          if (isset($item['raffle'])&&!$product->price){
            continue;
          }
          if (!isset($cart[$item['model']])){
            $volume = Product::query()->select('volume')->where('id', $item['id'])->first()?->volume;
            if($item['id'] > 1000){
              $item['id'] -= 1000;
            }
            $cart[$item['model']] = [
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'volume' => $volume,
                'model' => 'm'.$item['id'],
                'qty' => $item['qty'],
            ];
          }else{
            $cart[$item['model']]['qty'] += $item['qty'];
          }
        }
      }
      $cart = mergeItemsById($cart);
      $cart = sortItemsByName($cart, 'id');
      $invoice_name = 'ticket'.$ticket->id;
      return view('template.admin.tickets.invoice', compact('link', 'short_link', 'linkQrUri', 'cart', 'orders', 'invoice_name'));
    }
    public function invoice_orders($orders_ids){
      $orders = Order::select('id', 'data_cart')->whereIn('id', $orders_ids)->get();

      $link = route('admin.orders.index', [
          'ticket_id' => $orders_ids
      ]);
      $short_link_obj = ShortLink::select()->where('link', $link)->first();
      if (!$short_link_obj){
        $code = $this->getCode();
        $short_link_obj = ShortLink::create([
            'slug' => $code,
            'link' => $link,
            'data' => [
                'ticket_id' => $ticket->id ?? null
            ]
        ]);
      }
      $short_link = route('redirect', $short_link_obj->slug);
      // генерируем qr
      $img_path = public_path().'/img/qr';
      $img_name = '/qr_ticket-'.$ticket->id.'.png';
      if (!file_exists($img_path.$img_name)) {
        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($short_link)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        if (!file_exists($img_path)) {
          mkdir($img_path, 0777, true);
        }
        $result->saveToFile($img_path.$img_name);
        $linkQrUri = $result->getDataUri();
      }else{
        $linkQrUri = '/img/qr'.$img_name;
      }
      $cart = [];
      foreach($orders as $order){
        $order_cart = $order->data_cart;
        foreach($order_cart as $item){
          $product = Product::query()->where('id', $item['id'])->select('price')->first();
          if (isset($item['raffle'])&&!$product->price){
            continue;
          }
          if (!isset($cart[$item['model']])){
            $volume = Product::query()->select('volume')->where('id', $item['id'])->first()->volume;
            $cart[$item['model']] = [
                'name' => $item['name'],
                'model' => mb_substr($item['model'], 0, 1).$item['id'],
                'volume' => $volume,
                'qty' => $item['qty'],
            ];
          }else{
            $cart[$item['model']]['qty'] += $item['qty'];
          }
        }
      }
      return view('template.admin.tickets.invoice', compact('link', 'short_link', 'linkQrUri', 'cart', 'orders'));
    }
}
