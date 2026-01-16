<?php

namespace App\Jobs;

use App\Exports\UsersExport;
use App\Http\Controllers\Admin\UserController;
use App\Models\ExportFile;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SafeObject;

class ExportUsersJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


  private $request;
  private $page;
  private $user_id;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($request, $page, $user_id)
  {
    $this->request = $request;
    $this->page = $page;
    $this->user_id = $user_id;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    (new UserController())->export_job($this->request, $this->user_id);
//    $export = new UsersExport($this->request);
//    $file_name = 'users_'.now()->format('d-m-Y_H-i-s').'.xlsx';
//    $file_path = 'public/export/users/'.$file_name;
//    $count = User::query()->select('id')->filter(new SafeObject($this->request))->count();
//    $exportFile = ExportFile::create([
//        'name' => $file_name,
//        'path' => $file_path,
//        'type' ,
//        'lines_count' => $count,
//        'exported_by' => $this->user_id,
//    ]);
//    Excel::store($export, $file_path);
  }
}
