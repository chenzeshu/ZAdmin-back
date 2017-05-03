<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    public function __construct()
    {
//        $this->middleware('role:admin|kefu');
    }

    public function download($name, $path)
    {
        //转码
        $path = urldecode($path);
        $name = urldecode($name);

         return response()->download(storage_path('app/'.$path), $name);

    }


}
