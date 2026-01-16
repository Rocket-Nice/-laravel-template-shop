<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductNotification;
use App\Models\PuzzleImage;
use App\Services\PuzzleService\Client;
use App\Services\PuzzleService\Entities\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class HomeController extends Controller
{
    public function storeProductReview(Request $request, Product $product){

      $request->validate([
          'rating' => 'nullable|numeric|min:1|max:5',
          'review_text' => 'required|string|min:300'
      ], [
          'review_text.min' => 'Отзыв не должен быть короче 300 символов'
      ]);
      $user = auth()->user();
      if (!$user->canLeaveReview($product->id)) {
        return back()->withErrors([
            'Вы можете оставить только один отзыв о тех продуктах, которые вы купили'
        ]);
      }

      $text = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $request->review_text));;
      $rating = $request->rating ?? 0;

      $review_exists = $user->comments()
          ->where('commentable_type', 'App\Models\Product')
          ->where('commentable_id', $product->id)
          ->where('text', $text)
          ->exists();
      if($review_exists){
        return back()->withErrors([
            'Вы уже оставили этот отзыв. Он будет опубликован после модерации'
        ]);
      }


      $commentImages = [];
      if(request()->post('files')){
        $uploadedImages = $request->session()->get('uploaded_images', []);
        $files = request()->post('files');
        $thumb_path = storage_path('app/public/product_reviews/thumbs');
        if (!file_exists($thumb_path)) {
          mkdir($thumb_path, 0777, true);
        }
        foreach($files as $file){
          if(in_array($file, $uploadedImages)){
            $file_path = storage_path('app/public/product_reviews/'.$file);
            if (file_exists($file_path)) {
              $image = Image::make($file_path)->fit(100, 100);
              $image->save($thumb_path.'/'.$file);
              $commentImages[] = [
                  'thumb' => $thumb_path.'/'.$file,
                  'image' => $file_path,
                  'hidden' => false
              ];
            }
          }
        }
      }
      $product->comments()->create([
          'user_id'    => $user->id,
          'rating'     => $rating,
          'rQuality'   => $rating,
          'rAroma'     => $rating,
          'rStructure' => $rating,
          'rEffect'    => $rating,
          'rShipping'  => $rating,
          'text'       => $text,
          'hidden'     => true,
          'files'     => $commentImages,
      ]);

      $file = request()->file('user_image');

      if($file){
        $image = Image::make($file)->fit(100, 100);

        // Generate a filename
        $filename = time() . '-'. $user->id . '.' . $file->getClientOriginalExtension();

        // Store the image in the public disk (or any disk you prefer)
        $dir = storage_path('app/public/users');
        if (!file_exists($dir)) {
          mkdir($dir, 0777, true);
        }

        $tempPath = $dir . '/' . $filename;
        $image->save($tempPath);
        $user->update([
            'img' => $tempPath
        ]);
      }
      if(!$product->comments()->where('user_id', $user->id)->exists()){

      }
      $request->session()->forget('uploaded_images');
      return back()->with([
          'success' => 'Спасибо! Ваш отзыв отправлен на модерацию. Ваши бонусы будут начислены после модерации в течение 7 дней'
      ]);
    }

    public function discounts(){
      $seo = [
          'title' => 'Мои скидки'
      ];
      return view('template.cabinet.orders.index', compact( 'seo',));
    }

    public function product_notification(Request $request)
    {
      $request->validate([
          'product' => 'required|exists:products,slug',
          'action' => 'nullable'
      ]);
      $result = false;
      if($request->post('action')=='set'){
        $result = $this->createNotification($request->post('product'));
      }elseif($request->post('action')=='remove'){
        $result = $this->removeNotification($request->post('product'));
      }
      if(!$result){
        abort(403);
      }
      return $result;
    }

    public function createNotification($slug){
      $user = auth()->user();
      $product = Product::query()->where('slug', $slug)->first();
      if(!$user || !$product || $product->type_id != 1){
        return false;
      }
      $notification = $user->product_notifications->where('product_id', $product->id)->first();
      if(!$notification){
        ProductNotification::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
      }
      $message = 'Вы получите уведомление на почту, когда товар появится в наличии';
      return ['success' => true, 'message' => $message];
    }

    public function removeNotification($slug){
      $user = auth()->user();
      $product = Product::query()->where('slug', $slug)->first();
      if(!$user || !$product){
        return false;
      }
      $notification = $user->product_notifications->where('product_id', $product->id)->first();
      if($notification){
        $notification->delete();
      }
      $message = 'Вы отписались от уведомления о наличии';
      return ['success' => true, 'message' => $message];
    }

    public function puzzle()
    {
      if(!auth()->user()->hasPermissionTo('Доступ к админпанели')){
        abort(404);
      }
      $user = auth()->user();
      $puzzleImage = $user->puzzleImages()->where('is_correct', true)->first();
      $prize = null;
      if($puzzleImage && $puzzleImage->member_id){
        $puzzleClient = new Client();
        $puzzleClient->getToken();
        $prize = $puzzleClient->prizeByParticipant($puzzleImage->member_id);
        if(!is_array($prize)){
          $prize = null;
        }
      }
      $seo = [
          'title' => 'Собери пазл'
      ];
      return view('template.cabinet.puzzle', compact( 'seo', 'prize', 'puzzleImage'));
    }

    public function puzzle_upload(Request $request)
    {
      $request->validate([
          'files.*' => 'required|string', // Добавить heic в правила валидации
      ]);
      $user = auth()->user();
      $image_path = storage_path('app/public/puzzles');
      $thumb_path = storage_path('app/public/puzzles/thumbs');
      if (!file_exists($image_path)) {
        mkdir($image_path, 0777, true);
      }
      if (!file_exists($thumb_path)) {
        mkdir($thumb_path, 0777, true);
      }
      if($request->post('files')){
        $uploadedImages = $request->session()->get('uploaded_images', []);
        $files = $request->post('files');
        $file = $files[0];
        if(in_array($file, $uploadedImages)){
          $file_path = storage_path('app/public/product_reviews/'.$file);
          if (file_exists($file_path)) {
            $image = Image::make($file_path);
            if ($image->width() > $image->height()) {
              $image->rotate(-90);
            }
            $image->save($image_path.'/'.$file);
            $image->fit(100, 100);
            $image->save($thumb_path.'/'.$file);
            // сохраняем
            $puzzleImage = PuzzleImage::create([
                'user_id' => $user->id,
                'image_path' => $image_path.'/'.$file,
                'thumb_path' => $thumb_path.'/'.$file,
            ]);
            // unlink($file_path);

            // проверяем изображение
            $fileEntity = new File();
            $fileEntity->setImage($image_path.'/'.$file);
            $fileEntity->setLmId($user->id);
            $fileEntity->setEmail($user->email);
            $fileEntity->setFio(trim($user->last_name.' '.$user->first_name));

            $puzzleClient = new Client();
            $puzzleClient->getToken();
            $result = $puzzleClient->solvePuzzle($fileEntity);
            $params = [];
            $message = 'Ответ не получен';
            Log::debug(print_r($result, true));
            if(is_array($result)&&isset($result['result'])){
              if((int)$result['result'] == 3){
                $message = 'Пазл не найден';
              }elseif((int)$result['result'] == 2){
                $message = 'Это изображение уже было загружено';
              }elseif((int)$result['result'] == 1){
                $message = 'Пазл собран.';
                if(isset($result['prize']['name'])){
                  $message .= "\n\nВаш номер: ".$result['prize']['order'].". Ваш подарок: ".$result['prize']['name']."\n\n‼️Изучите условия получение призов‼️";
                }
                $params['is_correct'] = true;
              }else{
                $message = 'Пазл не собран';
              }
            }else{
              Log::debug('Ошибка проверки пазла');
              Log::debug(print_r($fileEntity->toArray(), true));
              Log::debug(print_r($result, true));
            }
            if(isset($result['member_id'])){
              $params['member_id'] = $result['member_id'];
            }
            $params['result_message'] = $message;
            $params['result'] = $result;
            $puzzleImage->update($params);
          }
        }
      }
      return [
          'message' => $message ?? 'Что-то пошло не так',
          'result' => $request->toArray()
      ];
    }
  public function documentAccept(Request $request, $page_id) {
    $request->validate([
        'accepted' => 'required',
    ]);
    $page = Page::findOrFail($page_id);
    $page->users()->attach(auth()->id());
    return back()->with([
        'success' => 'Вы приняли условия «'.$page->title.'»'
    ]);
  }
}
