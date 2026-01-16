<?php

namespace App\Jobs;

use App\Models\MailingList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachUsersToMailingListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $mailingListId;
  protected $userIds;

  public function __construct($mailingListId, $userIds)
  {
    $this->mailingListId = $mailingListId;
    $this->userIds = $userIds;
  }


  /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $mailingList = MailingList::find($this->mailingListId);

      if ($mailingList) {
        $mailingList->users()->syncWithoutDetaching($this->userIds);
      }
    }
}
