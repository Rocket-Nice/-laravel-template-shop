<?php

namespace App\Http\Controllers;

use App\Models\PageView;
use App\Models\Partner;
use App\Models\Referer;
use App\Models\ShortLink;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ShortLinkController extends Controller
{
    public function redirect(ShortLink $shortLink){
      $shortLink->increment('views');
      if (isset($shortLink->data['ticket_id'])){
        $ticket = Ticket::find($shortLink->data['ticket_id']);
        if ($ticket->data){
          $data = json_decode($ticket->data, true);
        }else{
          $data = [];
        }
        $data['printed'] = true;
        $ticket->update([
            'data' => json_encode($data)
        ]);

        if (strpos($shortLink->link, 'dlyakojida.ru/admin/orders?order_ids')!==false){
          $shortLink->link = route('admin.orders.index', ['ticket_id' => $shortLink->data['ticket_id']]);
        }
      }
      return redirect($shortLink->link);
    }

  public function partner(Request $request, Partner $partner){
      $visit_params = [
          'partner_id' => $partner->id,
          'date' => now()->format('Y-m-d H:i:s'),
          'ip' => $request->ip(),
          'referer' => $request->header('referer'),
          'userAgent' => $request->header('User-Agent')
      ];
      PageView::create($visit_params);
    $link = $partner->redirect ?? route('page.index');
    return response()->redirectTo($link)->withCookie('partner', $partner->id, 60*24*30);
  }
}
