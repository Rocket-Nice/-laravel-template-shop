<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\FilterUsers;
use App\Models\Traits\Loggable;
use App\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Loggable, FilterUsers;

  public function settings() {
    return $this->hasMany('App\Models\UserSetting');
  }

  public function coupons(){
    return $this->hasMany('App\Models\Coupone', 'owner_id', 'id');
  }
    public function mailing_list(){
      return $this->belongsToMany(MailingList::class, 'mailing_lists_users', 'user_id', 'mailing_list_id');
    }
    public function dasha_mail_lists(){
      return $this->belongsToMany(MailingList::class, 'dasha_mail_lists_users', 'user_id', 'dasha_mail_list_id');
    }

  public function giftCoupons() {
    return $this->hasMany('App\Models\GiftCoupon');
  }
  public function orders() {
    return $this->hasMany('App\Models\Order');
  }
  public function tgChats() {
    return $this->hasMany('App\Models\TgChat');
  }
  public function tgMessages() {
    return $this->hasMany('App\Models\TgMessage');
  }
  public function product_notifications() {
    return $this->hasMany('App\Models\ProductNotification');
  }

  public function bonus_transactions() {
    return $this->hasMany('App\Models\BonusTransaction');
  }
  public function super_bonus_transactions() {
    return $this->hasMany('App\Models\SuperBonusTransaction');
  }
  public function bonuses() {
    return $this->hasMany('App\Models\Bonus');
  }
  public function super_bonuses() {
    return $this->hasOne('App\Models\SuperBonus');
  }
  public function promocodes() {
    return $this->hasMany('App\Models\Coupone');
  }
  public function comments() {
    return $this->hasMany('App\Models\Comment');
  }
  public function puzzleImages() {
    return $this->hasMany('App\Models\PuzzleImage');
  }

  public function pages(){
    return $this->belongsToMany('App\Models\Page', 'page_user', 'user_id', 'page_id');
  }
  public function partner()
  {
    return $this->hasOne('App\Models\Partner');
  }
  public function raffle_members() {
    return $this->hasMany('App\Models\RaffleMember');
  }

  public function formAnswers()
  {
    return $this->hasMany(CustomFormData::class, 'user_id');
  }
  public function forms()
  {
    return $this->belongsToMany(CustomForm::class, 'custom_form_user', 'user_id', 'form_id')->withPivot(['status', 'created_at'])->withTimestamps();
  }
  public function surveys()
  {
    return $this->belongsToMany(NpsSurvey::class, 'nps_survey_user', 'user_id', 'survey_id')->withPivot(['nps_score', 'comment', 'created_at'])->withTimestamps();
  }
  public function surveysForBonuses()
  {
    return $this->belongsToMany(NpsSurvey::class, 'nps_survey_user', 'user_id', 'survey_id')->wherePivot('created_at', '>=', '2024-10-15 00:00:00')
        ->wherePivot('created_at', '<=', now()->subDays(3)->format('Y-m-d 00:00:00'));
  }
  public function isWaitingProduct($product_id){
    return $this->product_notifications()->where('product_id', $product_id)->where('was_noticed', false)->exists();
  }
  public function getBonuses(){
    if($this->bonuses){
      return $this->bonuses()->sum('amount');
    }else{
      return 0;
    }
  }
  public function getSuperBonuses(){
    if($this->super_bonuses){
      return $this->super_bonuses->amount;
    }else{
      return 0;
    }
  }
  public function addBonuses($amount, $comment = null, $expired_at = null){
    if(!$expired_at){
      $expired_at = now()->addDays(14)->endOfDay();
    }
    $bonuses = $this->bonuses()->where('expired_at', '=', $expired_at)->first();
    if(!$bonuses){
      $bonuses = Bonus::create([
          'user_id' => $this->id,
          'amount' => 0,
          'expired_at' => $expired_at,
      ]);
    }
    $transaction = BonusTransaction::create([
        'bonus_id' => $bonuses->id,
        'user_id' => $this->id,
        'amount' => $amount,
        'comment' => $comment.' (сгорают '.$expired_at->format('d.m.Y').')',
        'created_by' => auth()->check() ? auth()->id() : null
    ]);
    $bonuses->increment('amount', $amount);
    return $bonuses->amount;
  }
  public function addSuperBonuses($amount, $comment = null){
    $bonuses = $this->super_bonuses;
    if(!$bonuses){
      $bonuses = SuperBonus::create([
          'user_id' => $this->id,
          'amount' => 0
      ]);
    }
    SuperBonusTransaction::create([
        'bonus_id' => $bonuses->id,
        'user_id' => $this->id,
        'amount' => $amount,
        'comment' => $comment,
        'created_at' => now(),
    ]);
    $bonuses->increment('amount', $amount);
    return $bonuses->amount;
  }
  public function subBonuses($amount, $comment = null){
    if($amount < 0){
      return false;
    }
    $bonuses = $this->bonuses()
        ->orderByRaw('expired_at IS NULL')
        ->orderBy('expired_at', 'asc')
        ->get();
    if(!$bonuses->count()){
      $bonuses = Bonus::create([
          'user_id' => $this->id,
          'amount' => 0,
      ]);
      return 0;
    }
    if($bonuses->sum('amount') - $amount < 0){
      $amount = $bonuses->sum('amount');
    }

    foreach ($bonuses as $bonus) {
      if ($amount <= 0) {
        break;
      }
      $decrement = min($bonus->amount, $amount);
      $transaction = BonusTransaction::create([
          'bonus_id' => $bonus->id,
          'user_id' => $this->id,
          'amount' => $decrement*-1,
          'comment' => $comment,
          'created_by' => auth()->check() ? auth()->id() : null
      ]);
      $bonus->decrement('amount', $decrement);
      $amount -= $decrement;
    }
    return $bonuses->sum('amount');
  }
  public function subSuperBonuses($amount, $comment = null){
    $bonuses = $this->super_bonuses;
    if(!$bonuses){
      $bonuses = SuperBonus::create([
          'user_id' => $this->id,
          'amount' => 0,
      ]);
      return 0;
    }
    SuperBonusTransaction::create([
        'bonus_id' => $bonuses->id,
        'user_id' => $this->id,
        'amount' => $amount*-1,
        'comment' => $comment,
        'created_at' => now(),
    ]);
    $bonuses->decrement('amount', $amount);
    return $bonuses->amount;
  }
  public function getShortName(){
    $name = $this->first_name ?? $this->getFirstName();
    $last_name = $this->last_name ?? $this->getLastName();
    $last_name = $last_name ? mb_substr($last_name, 0, 1, "UTF-8").'.' : null;
    return $name.' '.$last_name;
  }
  public function getFirstName(){
    $full_name = explode(' ', $this->name);
    return $full_name[1] ?? null;
  }
  public function getLastName(){
    $full_name = explode(' ', $this->name);
    return $full_name[0];
  }
  public function getMiddleName(){
    $full_name = explode(' ', $this->name);
    return $full_name[2] ?? null;
  }
  public function canLeaveReview($product_id){
      $product = Product::find($product_id);

      $ids = [$product_id];
      if(isset($product->product_options['productSize'])){
        foreach($product->product_options['productSize'] as $option){
          $ids[] = $option['product'];
        }
      }
      $comments = $this->comments()
          ->where('commentable_type', 'App\Models\Product')
          ->where('commentable_id', $product_id)
          ->exists();
      if($comments){
        return false;
      }
      $order = $this->orders()->whereHas('items', function(Builder $builder) use ($ids) {
        $builder->whereIn('product_id', $ids);
      })->where('confirm', 1)->exists();

      return $order;
  }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'img',
        'last_name',
        'first_name',
        'middle_name',
        'phone',
        'email',
        'password',
        'is_new',
        'birthday',
        'options',
      'is_subscribed_to_marketing'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'datetime:Y-m-d',
        'options' => 'array',
    ];

  public function sendPasswordResetNotification($token)
  {
    $this->notify(new ResetPasswordNotification($token));
  }
}
