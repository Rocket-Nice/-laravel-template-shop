<?php

namespace App\Observers;

use App\Jobs\AddEmailToDashamailJob;
use App\Jobs\RemoveEmailFromDashamailJob;
use App\Models\User;

class UserObserver
{
  public function created(User $user): void
  {
    if ($user->is_subscribed_to_marketing) {
      AddEmailToDashamailJob::dispatch($user->email);
    }
  }

  public function updated(User $user): void
  {
    if ($user->email && $user->isDirty('is_subscribed_to_marketing')) {
      if ($user->is_subscribed_to_marketing) {
        AddEmailToDashamailJob::dispatch($user->email);
      } else {
        RemoveEmailFromDashamailJob::dispatch($user->email);
      }
    }

    if ($user->email && $user->isDirty('email')) {
      // Обновление email – удалим старый, добавим новый (если подписан)
      $originalEmail = $user->getOriginal('email');

      if ($originalEmail && $originalEmail !== $user->email) {
        RemoveEmailFromDashamailJob::dispatch($originalEmail);
        if ($user->is_subscribed_to_marketing) {
          AddEmailToDashamailJob::dispatch($user->email);
        }
      }
    }
  }

  public function deleted(User $user): void
  {
    RemoveEmailFromDashamailJob::dispatch($user->email);
  }
}
