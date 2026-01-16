<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
  private $promotion_flag = false;
  private $puzzles_flag = false;
  private $gold_ticket = false;
  private $promotion_categories = [1, 11, 27, 28, 30];
  private $cart;

  public function __construct()
  {
    $this->cart = Cart::instance('cart');
    $this->promotion_flag = getSettings('promo_1+1=3');
    $this->puzzles_flag = getSettings('puzzlesStatus');
    $this->gold_ticket = getSettings('goldTicket');
    $this->promotion_categories = Category::query()->pluck('id')->toArray();
  }


  public function init()
  {
    return response()->json($this->resultSuccess());
  }
  public function clear()
  {
    $this->cart->destroy();
    return redirect()->route('order.index');
  }

  public function update(Request $request){
    $request->validate([
        'cart_data' => ['required']
    ]);
    $cart = $this->cart;
    $data = $request->cart_data;

    if (isset($data[0]['id'])){ // если переданы данные обновленной корзины
      foreach($data as $qty_item){
        $id = $qty_item['option'] ?? $qty_item['id'];
        $quantity = $qty_item['qty'];
        $product = $this->getProduct($id);

        if (isset($product->product_options['productSize']) && !empty($product->product_options['productSize'])) {
          return response()->json($this->resultError('Необходимо выбрать размер у товара «'.$product->name.'»'));
        }elseif(!$product->price){
          return response()->json($this->resultError('Данный товар нельзя положить в корзину'));
        }
        $this->updateCart($product, (int)$quantity);
      }
      $this->checkPrices();
      if(getSettings('promo_1+1=3')){
        $this->promotion();
        // $this->gift();
      }else{
        $this->gift();
      }
      if($this->puzzles_flag){
        $this->puzzles();
      }
      return response()->json($this->resultSuccess('Данные в корзине успешно обновлены'));
    }else{
      $id = $data['option'] ?? $data['id'];
      $quantity = $data['qty'] ?? 1;
      $product = $this->getProduct($id);
//      if(auth()->id()==1){
//        Log::debug(print_r($product->toArray(), true));
//        Log::debug('$id'.print_r($id, true));
//      }
      if (isset($product->product_options['productSize']) && !empty($product->product_options['productSize'])) {
        return response()->json($this->resultError('Необходимо выбрать размер у товара «'.$product->name.'»'));
      }elseif(!$product->price){
        return response()->json($this->resultError('Данный товар нельзя положить в корзину'));
      }

      $res = $this->updateCart($product, $quantity, $data['type'] ?? 'add');
      return $res;
    }

  }


  public function remove(Request $request){
    $cart = $this->cart;
    $row_id = $request->row_id;
    if(!$row_id){
      return false;
    }

    $item = Cart::search(function ($cartItem, $rowId) use ($row_id) {
      return $rowId === $row_id;
    })->first();

    if ($item) {
      // Элемент с данным rowId найден в корзине
      $message = 'Товар «'.($item->name ?? '').'» удален из корзины';
      $cart->remove($row_id);
      if(getSettings('promo_1+1=3')){
        $product_id = $item->options->product_id;
        $discountedItem = $cart->content()->filter(function ($cartItem) use ($product_id) {
          return $cartItem->options->product_id == $product_id && $cartItem->price <= 1;
        })->first();
        if($discountedItem){
          $cart->remove($discountedItem->rowId);
        }
      }

    }else{
      $message = 'Товар не найден в корзине';
      return response()->json($this->resultError($message));
    }

    $this->checkPrices();
    if(getSettings('promo_1+1=3')){
      $this->promotion();
      // $this->gift();
    }else{
      $this->gift();
    }

    if($this->puzzles_flag){
      $this->puzzles();
    }

//    $received_id = $request->product_id;
//
//    $current_cart = $cart->content();
//
//    $product = Product::find($received_id);
//    if (!$product){
//      $message = 'Товар с id "'.$received_id.'" не найден';
//      return [
//          'success' => false,
//          'cart' => $cart->count(),
//          'total' => $this->cart->subtotal(0, '.', ''),
//          'message' => $message
//      ];
//    }
//
//    $exist_item = $current_cart->where('id', '=', $product->sku)->first();
//    if($exist_item) {
//      $cart->remove($exist_item->rowId);
//    }
//    $this->checkPrices();
//    $message = 'Товар «'.$product->name.'» удален из корзины';

    return response()->json($this->resultSuccess($message));
  }

  private function updateCart(Product $product, $quantity = 1, $type = 'add'){
    $cart = $this->cart;
    $current_cart = $cart->content();
    $name = $product->name;

    $sku = $product->sku;
    $exist_cart_item = $current_cart->filter(function ($item) use ($sku) {
      return $item->id == $sku && !$item->options->gift;
    })->first();
//    $exist_cart_item = $current_cart
//        ->where('id', '=', $product->sku)
//        ->where(function($query){
//          $query->where('options->gift', false);
//          $query->orWhere('options->gift', null);
//        })
//        ->first();
    if ($quantity<1&&$exist_cart_item){
      $cart->remove($exist_cart_item->rowId);
      $message = 'Товар «'.$product->name.'» удален из корзины';
    }elseif($exist_cart_item){
      if ($type == 'add'){
        $quantity = $exist_cart_item->qty + $quantity;
      }
      if($product->quantity > 0 && $exist_cart_item->qty + $quantity < $exist_cart_item->qty && $product->quantity < $quantity){
        $quantity = $product->quantity;
      }
      if (!$this->checkProductAvailability($product, $quantity)){
        $message = '«'.$product->name.'» отсутствует в достаточном количестве';
        return $this->resultError($message);
      }
      if($exist_cart_item->qty > $quantity){
        $message = "Товар «{$product->name}» убран из корзины<br/>В корзине: <span class=\"cormorantInfant\">$quantity</span> шт.";
      }else{
        $message = "Товар «{$product->name}» добавлен в корзину";
        if($product->only_pickup){
          $message .= "<br/><span class=\"text-myRed\">Доступен только для самовывоза г. Волгоград</span>";
        }

        $message .= "<br/>В корзине: <span class=\"cormorantInfant\">$quantity</span> шт.";
      }
      $cart->update($exist_cart_item->rowId, ['qty' => $quantity]);

    }elseif($quantity<1){
      return $this->resultError("Товара «{$product->name}» нет в вашей корзине");
    }else{
      if (!$this->checkProductAvailability($product)){
        $message = '«'.$product->name.'» отсутствует в достаточном количестве';
        return $this->resultError($message);
      }
      $params = [
          'id' => $product->sku,
          'name' => $name,
          'qty' => $quantity,
          'price' => $product->getPrice(),
          'options' => [
              'sku' => $product->sku,
              'old_price' => $product->old_price,
              'short_code' => $product->short_code,
              'product_id' => $product->id,
              'category_id' => $product->category_id,
              'image' => $product->image,
              'puzzles_count' => $product->puzzles_count ?? 0,
              'puzzles' => $product->puzzles ?? false,
              'subtitle' => $product->subtitle,
          ]
      ];
      if (in_array($product->id, [20,21,22])){
        return false; // старые подарочные сертификаты
      }
      if ($product->preorder){
        $params['options']['preorder'] = true;
      }
      $cart->add($params);
      $message = 'Товар «'.$product->name.'» добавлен в корзину';
      if($product->only_pickup){
        $message .= "<br/><span class=\"text-myRed\">Доступен только для самовывоза г. Волгоград</span>";
      }
    }
    $this->checkPrices();
    $promo_alert = null;
//    if(getSettings('promo_1+1=3')){
//      $this->promotion();
//      if(in_array($product->category_id, $this->promotion_categories)){
//        $category = $product->category_id;
//        $categoryItems = $cart->content()->filter(function ($item) {
//          return !($item->options->gift ?? false);
//        });
//        if($categoryItems->sum('qty') == 2){
//          $promo_alert = 'Добавьте третий товар в подарок';
//        }
//      }
//    }
    if(getSettings('promo_1+1=3')){
      $this->promotion();
      if(in_array($product->category_id, $this->promotion_categories)){
        $category = $product->category_id;
        $categoryItems = $cart->content()->filter(function ($item) use ($category) {
          return $item->options->category_id == $category;
        });
        if($categoryItems->sum('qty') == 2){
          $promo_alert = 'Добавьте третий товар из данной категории в подарок';
        }
      }
//      if(in_array($product->category_id, $this->promotion_categories)){
//        $category = $product->category_id;
//        $categoryItems = $cart->content()->filter(function ($item) {
//          return !($item->options->gift ?? false);
//        });
//        if($categoryItems->sum('qty') == 2){
//          $promo_alert = 'Добавьте третий товар в подарок';
//        }
//      }
//      $this->gift();
    }else{
      $this->gift();
    }
    if($this->puzzles_flag){
      $this->puzzles();
    }

    return $this->resultSuccess($message, $promo_alert);
  }

  // обновляем цены в корзине
  private function checkPrices(){
    $cart = $this->cart;
    foreach($cart->content() as $item){
      $item_id = $item->id;
      if(!getSettings('promo_1+1=3') && substr($item_id, -strlen('_discounted')) == '_discounted'){
        $cart->remove($item->rowId);
      }
      if ($item->options->gift||substr($item_id, -strlen('_discounted')) == '_discounted'||substr($item_id, -strlen('gift')) == 'gift') {
        continue;
      }
      $product = Product::select('id', 'price')->where('id', $item->options['product_id'])->first();
      if (!$product){
        $cart->remove($item->rowId);
      }else{
        $price = $product->getPrice();
        if ($price != $item->price){
          $cart->update($item->rowId, ['price' => $price]);
        }
      }
    }
  }

  private function checkProductAvailability($product, $quantity = 1) {
    // Проверяем статус и количество товара в целом
    if ($product->status > 0 && $product->quantity > 0 && $product->quantity >= $quantity) {
      return true;
    }

//    // Перебираем ключи массивов data_status и data_quantity и проверяем их значения
//    foreach ($product->data_status as $statusKey => $statusValue) {
//      // Удаляем 'status' из statusKey и формируем quantityKey
//      $suffix = str_replace('status', '', $statusKey);
//      $quantityKey = 'quantity' . $suffix;
//
//      if (isset($product->data_quantity[$quantityKey])) {
//        $isStatusPositive = $statusValue > 0;
//        $isQuantityPositive = $product->data_quantity[$quantityKey] > 0;
//
//        if ($isStatusPositive && $isQuantityPositive) {
//          return true;
//        }
//      }
//    }

    // Если не найдено ни одного положительного статуса и количества, возвращаем false
    return false;
  }

  public function puzzles(){
    $cart = $this->cart;
    $puzzles_count = $cart->content()->reduce(function ($carry, $item) {
      if (is_numeric($item->options->puzzles_count) && $item->options->puzzles_count > 0 && $item->options->puzzles) {
        return $carry + ($item->qty * $item->options->puzzles_count);
      }
      return $carry;
    }, 0);
    $giftInCart = $cart->content()->filter(function ($item) {
      return $item->options->product_id == 1185 && $item->options->gift;
    })->first();

    if($puzzles_count){
      if(!$giftInCart) {
        $product = $this->getProduct(1185);
        $params = [
            'id' => $product->sku,
            'name' => $product->name,
            'qty' => $puzzles_count,
            'price' => 0,
            'options' => [
                'gift' => true,
                'sku' => $product->sku,
                'short_code' => $product->short_code,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'image' => $product->image,
                'subtitle' => $product->subtitle,
            ]
        ];
        $cart->add($params);
      }else{
        $cart->update($giftInCart->rowId, [
            'qty' => $puzzles_count
        ]);
      }
    }elseif($giftInCart){
      $cart->remove($giftInCart->rowId);
    }
  }
  public function gift(){
    if(getSettings('promo20')||getSettings('promo30')){
      return false;
    }
    $startDate = Carbon::create(2024, 1, 31, 0, 0, 0);
    $finishDate = Carbon::create(2024, 12, 31, 23, 59, 59);
    if(now()->lte($startDate)||now()->gte($finishDate)) {
      return false;
    }
    if($this->gold_ticket) {
      return false;
    }
//    if(!auth()->check()||auth()->id()!=1) {
//      return false;
//    }
//    $summ = 7000;
//    $gift_id = 1128;
//    $summ = 10000;
//    $gift_id = 1132; // зеркало карманное
    $summ = 4500;
    $gift_id = 1163;
    $cart = $this->cart;

    $total = $cart->content()->reduce(function ($carry, $item) use ($gift_id) {
      // Проверяем, не равен ли $gift_id
      if ($item->options->product_id != $gift_id || !$item->options->gift) {
        // Добавляем стоимость текущего товара к общей сумме
        return $carry + ($item->qty * $item->price);
      }
      return $carry;
    }, 0);
    $giftInCart = $cart->content()->filter(function ($item) use ($gift_id) {
      return $item->options->product_id == $gift_id && $item->options->gift == true;
    })->first();
    if($total >= $summ && !$giftInCart){
      $product = $this->getProduct($gift_id);
      if($product->quantity <= 0){
        return false;
      }
      $params = [
          'id' => $product->sku,
          'name' => $product->name,
          'qty' => 1,
          'price' => 1,
          'options' => [
              'gift' => true,
              'sku' => $product->sku,
              'short_code' => $product->short_code,
              'product_id' => $product->id,
              'category_id' => $product->category_id,
              'image' => $product->image,
              'subtitle' => $product->subtitle,
          ]
      ];
      $cart->add($params);
    }elseif($total < $summ && $giftInCart){
      $cart->remove($giftInCart->rowId);
    }
    return false;
    $cartItems = $cart->content()->filter(function ($item) {
      return $item->options->product_id != 1134;
    });
    $giftInCart = $cart->content()->filter(function ($item) {
      return $item->options->product_id == 1134;
    })->first();
    if($cartItems->count() && !$giftInCart){
      $product = $this->getProduct(1134);
      $params = [
          'id' => $product->sku,
          'name' => $product->name,
          'qty' => 1,
          'price' => 1,
          'options' => [
              'gift' => true,
              'sku' => $product->sku,
              'short_code' => $product->short_code,
              'product_id' => $product->id,
              'category_id' => $product->category_id,
              'image' => $product->image,
              'subtitle' => $product->subtitle,
          ]
      ];
      $cart->add($params);
    }elseif(!$cartItems->count() && $giftInCart){
      $cart->remove($giftInCart->rowId);
    }
  }

//  public function promotion() {
//    $cart = $this->cart;
//    $discountPrice = 1; // Цена со скидкой
//
//    // Фильтрация товаров, исключая подарки
//    $items = $cart->content()->filter(function ($item) {
//      return !($item->options->gift ?? false);
//    });
//
//    // Подсчет общего количества товаров и количества товаров со скидкой
//    $totalQuantity = $items->sum('qty');
//    $discountedQuantity = intdiv($totalQuantity, 3);
//    $totalDiscountedQuantity = $items->where('price', $discountPrice)->sum('qty');
//
//    // Удаление всех существующих скидок для пересчета
//    $this->removeAllDiscounts($cart, $items);
//
//    // Обновление списка товаров после удаления всех скидок
//    $items = $cart->content()->filter(function ($item) {
//      return !($item->options->gift ?? false);
//    });
//
//    // Получение самых дорогих товаров для скидки
//    $mostExpensiveProducts = $this->getMostExpensiveProducts($items, $discountedQuantity);
//
//    // Применение скидок
//    $this->applyDiscounts($cart, $mostExpensiveProducts, $discountPrice);
//  }

  private function removeAllDiscounts($cart, $items) {
    foreach ($items->where('price', 1) as $item) {
      if (substr($item->id, -strlen('_discounted')) === '_discounted') {
        $originalItemId = str_replace('_discounted', '', $item->id);
        $originalItem = $cart->search(function ($cartItem) use ($originalItemId) {
          return $cartItem->id === $originalItemId;
        })->first();

        if ($originalItem) {
          $cart->update($originalItem->rowId, ['qty' => $originalItem->qty + $item->qty]);
        } else {
          $itemOptions = $item->options->toArray();
          unset($itemOptions['old_price']);
          $cart->add([
              'id' => $originalItemId,
              'name' => $item->name,
              'qty' => $item->qty,
              'price' => $item->options->old_price,
              'options' => $itemOptions
          ]);
        }
        $cart->remove($item->rowId);
      }
    }
  }

  private function getMostExpensiveProducts($items, $discountedQuantity) {
    $sortedItems = $items->sortByDesc('price');
    $mostExpensiveProducts = collect();

    foreach ($sortedItems as $product) {
      for ($i = 0; $i < $product->qty; $i++) {
        $mostExpensiveProducts->push($product);
        if ($mostExpensiveProducts->count() >= $discountedQuantity) {
          return $mostExpensiveProducts->take($discountedQuantity);
        }
      }
    }

    return $mostExpensiveProducts->take($discountedQuantity);
  }

  private function applyDiscounts($cart, $mostExpensiveProducts, $discountPrice) {
    foreach ($mostExpensiveProducts as $product) {
      $existingItem = $cart->content()->where('id', $product->id)->first();
      if ($existingItem) {
        $newQty = $existingItem->qty > 1 ? $existingItem->qty - 1 : 0;
        if ($newQty > 0) {
          $cart->update($existingItem->rowId, ['qty' => $newQty]);
        } else {
          $cart->remove($existingItem->rowId);
        }

        $discountedItemId = $product->id . '_discounted';
        $existingGift = $cart->content()->where('id', $discountedItemId)->first();

        if ($existingGift) {
          $cart->update($existingGift->rowId, ['qty' => $existingGift->qty + 1]);
        } else {
          $discountedItemOptions = $product->options->toArray();
          $discountedItemOptions['old_price'] = $product->price;
          $cart->add([
              'id' => $discountedItemId,
              'name' => $product->name,
              'qty' => 1,
              'price' => $discountPrice,
              'options' => $discountedItemOptions
          ]);
        }
      }
    }
  }

//  // промо по категориям
  public function promotion(){
    $cart = $this->cart;
    $categories = $this->promotion_categories; // Категории, участвующие в акции
    $discountPrice = 1; // Цена со скидкой

    foreach ($categories as $category) {
      // Получаем все товары в корзине для текущей категории
      $categoryItems = $cart->content()->filter(function ($item) use ($category) {
        return $item->options->category_id == $category;
      });

      // Вычисляем общее количество товаров в категории
      $totalQuantityInCategoryWithoutDiscount = $cart->content()->reduce(function ($carry, $item) {
        if ($item->price > 1) {
          return $carry + $item->qty;
        }
        return $carry;
      }, 0);
      $totalQuantityInCategory = $categoryItems->sum('qty');

      // Вычисляем количество товаров, которые должны быть со скидкой
      $discountedQuantity = intdiv($totalQuantityInCategory, 3);

      // удаляем лишние скидки, если они оставлись после удаления товара из корзины
      $totalDiscountedQuantityInCategory = $categoryItems->where('price', '<=', 1)->sum('qty');
      if ($totalDiscountedQuantityInCategory>$discountedQuantity){
        for($i=0;$i<$totalDiscountedQuantityInCategory-$discountedQuantity;$i++){
          $over_gift = $categoryItems->where('price', '<=', 1)->first();
          if ($totalQuantityInCategoryWithoutDiscount > 0 && $over_gift->qty>=1){
            $cart->update($over_gift->rowId, [
                'qty' => $over_gift->qty - 1
            ]);
            $categoryItems = $cart->content()->filter(function ($item) use ($category) {
              return $item->options->category_id == $category;
            });
            $totalQuantityInCategory = $categoryItems->sum('qty');
            $discountedQuantity = intdiv($totalQuantityInCategory, 3);
          }else{
            $cart->remove($over_gift->rowId);
            break;
          }
        }
      }
      $categoryItems = $cart->content()->filter(function ($item) use ($category) {
        return $item->options->category_id == $category;
      });

      // Сортируем товары по убыванию цены
      foreach ($categoryItems->where('price', '<=', 1) as $item) {
        $item_id = $item->id;
        if (substr($item_id, -strlen('_discounted')) == '_discounted'&&$item->options->old_price) {
          $originalItemId = str_replace('_discounted', '', $item->id);
          $originalItem = $cart->search(function ($cartItem, $rowId) use ($originalItemId) {
            return $cartItem->id === $originalItemId;
          })->first();
          if($originalItem) {
            // echo 'update + '.$exist_item->id.' = '.($exist_item->qty + 1).'<br/>';
            $cart->update($originalItem->rowId, ['qty' => $originalItem->qty + $item->qty]);
          }else{
            // echo 'add '.$item->options->original_id.'<br/>';
            $itemOptions = $item->options->toArray();
            unset($itemOptions['old_price']);
            $product_id = $item->id;
//            if (substr($product_id, -strlen('_discounted')) == '_discounted') {
//              $product_id = $product_id. '_discounted';
//            }
//            if (substr($product_id, -strlen('_discounted')) != '_discounted') {
//              $product_id = $product_id. '_discounted';
//            }
            $originalItemParams = [
                'id' => $originalItemId,
                'name' => $item->name,
                'qty' => $item->qty,
                'price' => $item->options->old_price,
                'options' => $itemOptions
            ];
            $cart->add($originalItemParams);
          }
          // удаляем дисконт
          $cart->remove($item->rowId);
        }
      }

      $categoryItems = $cart->content()->filter(function ($item) use ($category) {
        return $item->options->category_id == $category;
      });
      $sortedItems = $categoryItems->sortByDesc('price');

      // next
      $mostExpensiveProducts = collect();
      foreach ($sortedItems as $product) {
        $quantity = $product->qty;
        while ($quantity > 0) {
          $mostExpensiveProducts->push($product);
          $quantity--;
        }

        if ($mostExpensiveProducts->count() >= $discountedQuantity) {
          break;
        }
      }
      // echo $cart_count.', '.$gifts;
      $mostExpensiveProducts = $mostExpensiveProducts->take($discountedQuantity);
      //dd($cart->content(), $mostExpensiveProducts);
      // добавляем скидки
      foreach($mostExpensiveProducts as $product){
        $exist_item = $cart->content()->where('id', '=', $product->id)->first();
        if ($exist_item){
          // удаляем обычный
          // echo 'start '.$exist_item->qty.'<br/>';
          if($exist_item->qty>1) {
            // echo 'update - '.$product->id.' = '.($exist_item->qty - 1).'<br/>';
            $cart->update($exist_item->rowId, [
                'qty' => $exist_item->qty - 1
            ]);
          }else{
            // echo 'remove '.$product->id.'<br/>';
            $cart->remove($exist_item->rowId);
          }
          // добавляем подарок
          $exist_gitf = $cart->content()->where('id', '=', $product->id.'_discounted')->first();

          if ($exist_gitf){
            // echo 'update + '.$exist_gitf->id.' = '.($exist_gitf->qty + 1).'<br/>';
            $cart->update($exist_gitf->rowId, [
                'qty' => $exist_gitf->qty + 1
            ]);
          }else{
            $discountedItemOptions = $product->options->toArray();
            $discountedItemOptions['old_price'] = $product->price;
            $product_id = $product->id;
            if (substr($product_id, -strlen('_discounted')) != '_discounted') {
              $product_id = $product_id. '_discounted';
            }
            $discountedItem = [
                'id' => $product_id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $discountPrice,
                'options' => $discountedItemOptions
            ];
            $cart->add($discountedItem);
            // echo 'add '.$product->id.'_maygift'.'<br/>';
          }

        }
      }
    }

  }


  private function getProduct($id)
  {
    $product = Product::select(
        'id',
        'name',
        'sku',
        'old_price',
        'price',
        'type_id',
        'category_id',
        'quantity',
        'data_status',
        'data_quantity',
        'status',
        'slug',
        'style_page->cardImage->image->200 as image',
        'style_page->subtitle as subtitle',
        'product_options',
        'product_id',
        'category_id',
        'preorder',
        'options->puzzles_count as puzzles_count',
        'options->puzzles as puzzles',
        'options->only_pickup as only_pickup'
    )
        ->where('hidden', false)
        ->where('id', $id)
        ->first();
    return $product;
  }
  private function resultSuccess($message = '', $promo_alert = null){
    $res = [
        'cart' => $this->cart->content(),
        'cartCount' => $this->cart->count(),
        'total' => $this->cart->subtotal(0, '.', ''),
    ];
    if($promo_alert){
      $res['promo_alert'] = $promo_alert;
    }
    if($message){
      $res['message'] = [
          'text' => $message,
          'type' => 'success',
      ];
    }
    return $res;
  }
  private function resultError($message = ''){
    $res = [
        'cart' => $this->cart->content(),
        'cartCount' => $this->cart->count(),
        'total' => $this->cart->subtotal(0, '.', ''),
    ];
    if($message){
      $res['message'] = [
          'text' => $message,
          'type' => 'error',
      ];
    }
    return $res;
  }
}
