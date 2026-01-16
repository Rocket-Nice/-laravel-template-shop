<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExportFileController extends Controller
{
    public function index()
    {
      $export_files = ExportFile::query()
          ->with('creator');
      if(!auth()->user()->hasRole('admin')){
        $export_files->where('exported_by', auth()->id());
      }
      $export_files = $export_files->orderBy('created_at', 'desc')
          ->paginate(30);
      $jobsCount = DB::table('jobs')->where('queue', 'like', 'export_%')->exists();
      $seo = [
          'title' => 'Страницы сайта'
      ];
      return view('template.admin.export.index', compact('export_files', 'seo', 'jobsCount'));
    }

  public function export_size(){
    $export_files = ExportFile::query()
        ->where('size', null)
        ->get();
    foreach($export_files as $export_file){
      if(!Storage::exists($export_file->path)){
        $created_at = Carbon::parse($export_file->created_at);
        if (now()->diffInMinutes($created_at) > 60) {
          $export_file->delete();
        }
        continue;
      }
      $export_file->update([
          'size' => Storage::size($export_file->path)
      ]);
    }
  }
}
