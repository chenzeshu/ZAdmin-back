<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//得到用户姓名等信息，同时刚好用于登陆时的access_token验证
Route::middleware('auth:api')->get('/v1/user', function (Request $request) {
    return $request->user();
});

Route::get('api/v1/services/printTask','servicesController@printTask');

Route::middleware('auth:api')->get('/v1/test', 'TestController@index');

Route::group(['prefix' => 'v1', 'namespace'=>'API','middleware'=>'auth:api'], function (){
    //模糊搜索
    Route::get('customers/search/{name}','customersController@search');//客户
    Route::get('contracts/search/{name}','contractsController@search');//合同
    Route::get('customer2s/search/{name}','customer2sController@search');//联系人
    Route::get('services/search/{code}','servicesController@search');//服务单

    //addman搜索
    Route::get('addman/users/{name}','UsersController@search');//员工
    Route::get('addman/cus2s/{name}','customer2sController@search');//客户联系人
    Route::get('addman/cus/{name}','customersController@search');//客户单位

    //导出服务单
    Route::post('services/createTask','servicesController@createTask');
//    Route::get('services/printTask','servicesController@printTask'); 由于头部问题，放到了web.php中

    //文件上传
    Route::post('contracts/upload', 'contractsController@upload');  //合同上传
    Route::post('contracts/unUpload', 'contractsController@unUpload');  //合同删除
    //文件下载
        //在web.php中

    Route::resource('customers', 'customersController');
    Route::resource('customer2s', 'customer2sController');
    Route::resource('contracts', 'contractsController');
    Route::resource('services', 'servicesController');
    Route::resource('users','UsersController');
    Route::resource('roles','RolesController');
    Route::resource('perms','PermissionsController');
});