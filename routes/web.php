<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (\Illuminate\Http\Request $request) {
    $code = $request->code;
    \App\Test::create([
        'code'=>$code
    ]);
    return \Illuminate\Support\Facades\Redirect::back();
});

Route::get('/code', function (){
   return \App\Test::orderBy('id', "desc")->first()->code;
});


Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/services/printTask','API\servicesController@printTask')->middleware('throttle:5,1');
Route::get('/download/{name}/path/{path}', 'API\DownloadController@download')->middleware('throttle:5,2');


Route::get('test','TestController@index');