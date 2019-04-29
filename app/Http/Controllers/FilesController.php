<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{

  public function downloadFile(Request $request)
  {
     $file = File::find($request->file_id);
     return Storage::disk('space')->download($file->name);
  }

}
