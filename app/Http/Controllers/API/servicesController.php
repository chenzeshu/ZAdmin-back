<?php

namespace App\Http\Controllers\API;

use App\Contract;
use App\Customer2;
use App\Repositories\ServicesRepository;
use App\Service;
use App\ServiceTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class servicesController extends Controller
{
    protected $repo;

    function __construct(ServicesRepository $repo)
    {
        $this->middleware('permission:create_service')->only('store');
        $this->middleware('permission:edit_service')->only('update');
        $this->middleware('permission:delete_service')->only('destroy');
//        $this->middleware('permission:print_service')->only('createTask');
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->repo->showAllServices();
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
        $this->repo->newService($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $services = Contract::findOrFail($id)->services()->orderBy('id','desc')->with('contract')->get();
        $services = $this->repo->foreachServicesUnserialize($services);
        return $services;
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
        return $this->repo->updateService($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
    }

    public function search($code)
    {
        $services = Service::where('s_id', 'like', '%'.$code.'%')->where('id','>','1')->get();
        $services = $this->repo->foreachServicesUnserialize($services);
        return $services;
    }

    //存入服务单
    public function createTask(Request $request)
    {
        $this->repo->createServiceTaskForNew($request);
    }

//    导出客服任务单.docx
    public function printTask()
    {
        $this->repo->printServiceTask();
        return Redirect::back();
    }

}
