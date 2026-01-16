<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Integrations\tfpdf\tFPDF;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShortLink;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SafeObject;

class InvoiceController extends Controller
{
    private $limit = 100000;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->order_id){
          $order = Order::findOrFail($request->order_id);
          $invoices = $order->invoices()->orderBy('id', 'desc')->paginate(100);
        }else{
          $invoices = Invoice::orderBy('id', 'desc')->paginate(100);
        }
        $seo = [
            'title' => 'Накладные'
        ];
        if ($request->order_id){
          $seo['title'] .= ' заказа '.$request->order_id;
        }
        return view('template.admin.invoices.index', compact('invoices', 'seo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $orders = Order::filtered(new SafeObject(request()->toArray()))->withoutTest()->select('id')->count();
      if ($orders>$this->limit){
        return back()->withErrors([
            'В накладную можно добавить не более '.denum($this->limit, ['%d заказ','%d заказа','%d заказов'])
        ]);
      }
      $seo = [
          'title' => 'Создать накладную'
      ];
      return view('template.admin.invoices.create', compact('orders', 'seo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
          'name' => 'string|required'
      ]);

      $orders = Order::filtered(new SafeObject(request()->toArray()))->withoutTest()->select('id')->pluck('id')->toArray();
      if (count($orders)>$this->limit){
        return back()->withErrors([
            'В накладную можно добавить не более '.denum($this->limit, ['%d заказ','%d заказа','%d заказов'])
        ]);
      }
      $params = $request->toArray();
      unset($params['_token']);
      unset($params['name']);
      unset($params['page']);
      $query_params = [
          'query' => stristr($_SERVER['REQUEST_URI'], '?'),
          'params' => $params
      ];
      $invoice = Invoice::create([
          'name' => $request->name,
          'query' => $query_params
      ]);
      $invoice->orders()->toggle($orders);

      // builder
      if(Order::filtered(new SafeObject(request()->toArray()))->withoutTest()->select('id')->where('data_cart', 'like', '%builder%')->count()){
        $user = auth()->user();
        $pdf = new tFPDF('P', 'pt', [239.94, 297.64]);
        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
        $orders = Order::filtered(new SafeObject(request()->toArray()))->withoutTest()->select('id', 'data_cart')->where('data_cart', 'like', '%builder%')->get();

//        foreach($orders as $order){
//          $cart = $order->data_cart;
//          foreach($cart as $item){
//            if(!isset($item['builder'])){
//              continue;
//            }
//            $pdf->AddPage();
//            $pdf->SetFont('DejaVu','',8);
//            $pdf->SetMargins(10, 10, 10);
//            $pdf->SetAutoPageBreak(true, 20);
//            //$pdf->SetFont('Arial', 'B', 9);
//            $pdf->SetXY(10, 20);
//            $pdf->SetFont('DejaVu','B',8);
//            $pdf->Cell(0, 14, $order->getOrderNumber(), 0, 0, 'L');
//            $pdf->SetFont('DejaVu','',8);
//
//            $pdf->SetFont('DejaVu','',8);
//            $pdf->setY(35);
//            $pdf->SetFillColor(255,255,255);
//            $pdf->MultiCell(0, 14, $item['name'].' ('.$item['model'].' '.$item['qty'].' шт)', 1, 'L', true);
//            $builder_models = '';
//            $builder_text = '';
//            foreach($item['builder'] as $builder_item){
//              $builder_models .= mb_substr($builder_item['model'], 0, 1) . $builder_item['product_id'] . '-' . $builder_item['qty'].', ';
//              $builder_text .= "– {$builder_item['name']} - {$builder_item['qty']}шт,\n";
//            }
//            $pdf->SetFont('DejaVu','',6);
//            $pdf->SetFillColor(255,255,255);
//            $pdf->setY(70);
//            $pdf->MultiCell(0, 14, $builder_text."\n".$builder_models, 1, 'L', true);
//          }
//        }
        $file_name = Str::random(8).'_'.($user->email).'.pdf';
        $directory = '/files/builder/tickets';

        if (!file_exists(public_path() . $directory)) {
          mkdir(public_path() . $directory, 0777, true);
        }
        $result = $pdf->Output('F', public_path() . $directory . '/' . $file_name);

        $invoice->update([
            'options' => [
                'builder' => $directory . '/' . $file_name
            ]
        ]);
      }
      return redirect()->route('admin.invoices.index')->with([
          'success' => 'Накладная успешно создана'
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $invoice = Invoice::findOrFail($id);
      $orders = $invoice->orders()->select('id', 'data_cart')->get();

      $link = route('admin.orders.index', ['invoice_id' => $invoice->id]); // .$invoice->query['query']
      $short_link_obj = ShortLink::select()->where('link', $link)->first();
      if (!$short_link_obj){
        $code = getCode(6);
        $short_link_obj = ShortLink::create([
            'slug' => $code,
            'link' => $link,
            'data' => [
                'invoice_id' => $invoice->id
            ]
        ]);
      }
      $short_link = route('redirect', $short_link_obj->slug);
      // генерируем qr
      $img_path = public_path().'/img/qr';
      $img_name = '/qr_invoice-'.$invoice->id.'.png';
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
          $product = Product::query()->select('volume', 'product_sku_id')->with('product_sku')->where('id', $item['id'])->first();
          $model = $product->product_sku?->name ?? $item['model'];
          if (!isset($cart[$model])){
            $volume = $product->volume;
            if($item['id'] > 1000){
              $item['id'] -= 1000;
            }
            $cart[$model] = [
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'volume' => $volume,
                'model' => 'm'.$item['id'],
                'qty' => $item['qty'],
            ];
          }else{
            $cart[$model]['qty'] += $item['qty'];
          }
        }
      }
      $cart = mergeItemsById($cart, 'id');
      $cart = sortItemsByName($cart, 'id');
      $invoice_name = 'invoice_'.$invoice->id;
      return view('template.admin.tickets.invoice', compact('link', 'short_link', 'linkQrUri', 'cart', 'orders', 'invoice_name'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
