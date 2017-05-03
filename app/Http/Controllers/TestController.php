<?php

namespace App\Http\Controllers;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
//        $path = "public/temp/9EVzcsaJAkZ1AT9M6dB6gzfrN2PRxGog6GfA1vCV.bin";
//
//        $path2 = new File(storage_path('app/'.$path));
//
//        Storage::putFile('public/contracts',$path2);

        $path = new File(storage_path('app/'."public/temp/iTAuA4QWzsMQ7S95omeq6HC2HdVg7FDAL5fgyrTZ.bin"));

        Storage::putFile('public/contracts',$path);
    }
}
