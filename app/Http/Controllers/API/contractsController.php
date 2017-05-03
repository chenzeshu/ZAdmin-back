<?php

namespace App\Http\Controllers\API;

use App\Contract;
use App\Customer;
use App\Repositories\ContractsRepository;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Zend\Stdlib\ArrayObject;

class contractsController extends Controller
{
    protected $repo;
    function __construct(ContractsRepository $repo)
    {
        $this->middleware('permission:create_service')->only('store');
        $this->middleware('permission:edit_service')->only('update');
        $this->middleware('permission:delete_service')->only('destroy');
//        $this->middleware('permission:print_service')->only('upload');
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->repo->showAllContracts();
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
        $this->repo->newContract($request);
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
        $this->repo->updateContract($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);

        $files = unserialize($contract->file);

        $this->removeFiles($files);

        $contract->delete();
    }

    public function search($name)
    {
        $contracts = Contract::where('name', 'like', '%'.$name.'%')->get();
        foreach ($contracts as $contract){
            $contract->pm = unserialize($contract->pm);
            $contract->tm = unserialize($contract->tm);
            $contract->cus = unserialize($contract->cus);
        }
        return $contracts;
    }

    /**
     * 第二版upload，上传总是先到tmp文件夹
     * @param Request $request
     * @return array(返回临时路径，便于真实保存时转移使用)
     */
    public function upload(Request $request)
    {
        $array = $this->repo->upload($request);
        return $array;
    }

    public function removeFiles($files)
    {
        if(empty($files) || !is_array($files)) returns;
        foreach ($files as $file){
            Storage::delete($file['path']);
        }
    }
}
