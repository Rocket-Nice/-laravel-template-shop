<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductNotification;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = ProductNotification::query();
        if (request()->keyword) {
          $keyword = trim(request()->keyword);
          $notifications->whereHas('user', function (Builder $builder) use ($keyword) {
            $builder->where(DB::raw('lower(name)'), 'like', '%'.$keyword.'%');
            $builder->orWhere(DB::raw('lower(email)'), 'like', '%'.$keyword.'%');
            $builder->orWhere(DB::raw('lower(phone)'), 'like', '%'.$keyword.'%');
          });
        }
        if (request()->product) {
          $notifications->whereIn('product_id', request()->product);
        }
        $notifications = $notifications->orderByDesc('id')->paginate(200);

      $products = Product::select('id', 'name', 'sku')->whereIn('type_id', [1])->get();
        return view('template.admin.product_notifications.index', compact('notifications', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductNotification  $productNotification
     * @return \Illuminate\Http\Response
     */
    public function show(ProductNotification $productNotification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductNotification  $productNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductNotification $productNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductNotification  $productNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductNotification $productNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductNotification  $productNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductNotification $productNotification)
    {
        //
    }
}
