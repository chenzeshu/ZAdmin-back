<?php

namespace App\Http\Controllers\API;

use App\Customer2;
use App\Http\Controllers\Controller;
use App\Repositories\Customer2sRepository;
use Illuminate\Http\Request;

class customer2sController extends Controller
{
    protected $repo;
    function __construct(Customer2sRepository $repo)
    {
        $this->middleware('permission:create_service')->only('store');
        $this->middleware('permission:edit_service')->only('update');
        $this->middleware('permission:delete_service')->only('destroy');
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer2s = Customer2::orderBy('id','desc')->get();
        return $customer2s;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Customer2::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'customer_name' => $request->customer['payload']['name'] ,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Customer2::findOrFail($id)->update([
            'name'=> $request->name,
            'phone'=>$request->phone,
            //全部迁移到第二稿时再做repository
//            'customer_name'=>$request->customer['payload']['name'],  //customer用户单位集成在addman时的逻辑
            'customer_name'=>$request->customer_name,       //customer独立在addcompany时的逻辑
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Customer2::findOrFail($id)->delete();
    }

    public function search($name)
    {
        return Customer2::where('name','like','%'.$name.'%')->get();
    }
}
