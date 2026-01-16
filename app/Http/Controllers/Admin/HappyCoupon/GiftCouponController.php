<?php

namespace App\Http\Controllers\Admin\HappyCoupon;

use App\Http\Controllers\Controller;
use App\Models\GiftCoupon;
use App\Models\Partner;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftCouponController extends Controller
{
    public function index(Request $request){
      $keyword = $request->keyword;
      $gift = $request->prize;

      $hpDate = (new GiftCoupon)->getDate();
      $giftCoupones = GiftCoupon::select(
          'gift_coupons.id',
          'gift_coupons.code',
          'gift_coupons.order_id',
          'gift_coupons.created_at',
          'prizes.name AS prize',
          'prize_id',
          'users.id AS user_id',
          'users.name',
          'users.email',
          'users.phone',
          'orders.status',
          'orders.partner_id',
          'orders.slug'
      )
          ->leftJoin('users', 'users.id', '=', 'user_id')
          ->leftJoin('orders', 'orders.id', '=', 'order_id')
          ->leftJoin('prizes', 'prizes.id', '=', 'prize_id')
          ->whereNotIn('orders.status', ['test', 'refund', 'cancelled'])
          ->where('gift_coupons.created_at', '>', $hpDate->subYears(10)->format('Y-m-d H:i:s'));
      if (request()->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
        $giftCoupones->where('gift_coupons.created_at', '>', $date_from);
      }
      if (request()->date_until) {
        $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
        $giftCoupones->where('gift_coupons.created_at', '<', $date_until);
      }

      if ($request->order_id) {
        $order_id = trim($request->order_id);
        $giftCoupones->where('gift_coupons.order_id', $order_id);
      }elseif($keyword){
        $keyword = trim($request->keyword);
        $giftCoupones->where(function ($query) use ($keyword) {
          $keyword = mb_strtolower($keyword);
          $query->where(DB::raw('lower(users.name)'), 'like', '%'.$keyword.'%')
              ->orWhere(DB::raw('lower(users.email)'), 'like', '%'.$keyword.'%')
              ->orWhere(DB::raw('lower(users.phone)'), 'like', '%'.$keyword.'%')
              ->orWhere(DB::raw('lower(gift_coupons.code)'), 'like', '%'.$keyword.'%');
          return $query;
        });;
      }
      if($request->partners){
        $giftCoupones->whereIn('orders.partner_id', $request->partners);
      }
      if($gift){
        $giftCoupones->where('prize_id', '=', $gift);
      }else{
        $giftCoupones->where('prize_id', '!=', null);
      }
      $giftCoupones = $giftCoupones->orderBy('created_at', 'desc')->paginate(100);

      $prizes = Prize::select('id', 'name')->where('id', '>', 127)->orderBy('name')->get();
      $partners = Partner::select('id', 'name')->orderBy('created_at', 'desc')->get();
      $seo = [
          'title' => 'Все купоны с подарками'
      ];
      return view('template.admin.happy_coupon.gift_coupones.index', compact('seo', 'prizes', 'giftCoupones', 'partners'));
    }
}
