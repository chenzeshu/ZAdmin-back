<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 14:06
 */

namespace App\Repositories;


use App\Contract;
use App\Customer2;

class Customer2sRepository
{
    public function newCus2($request)
    {
        Customer2::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'customer_name' => $request->customer['name'] ,
        ]);
    }

    public function updateCus2($request, $id)
    {
        Customer2::findOrFail($id)->update([
            'name'=> $request->name,
            'phone'=>$request->phone,
            //for第一稿
            'customer_name'=>$request->customer['name'],
        ]);
    }
    //第一稿的search逻辑
    public function search($contract_id, $name)
    {
        if ($name == "all"){
            $cus2 = Contract::findOrFail($contract_id)
                ->customer()->first()
                ->customer2s()->select('id', 'name')->limit(10)->get();
        }else{
            $cus2= Contract::findOrFail($contract_id)
                ->customer()->first()
                ->customer2s()->where('name', 'like',"%".$name."%")->select('id', 'name')->limit(10)->get();
        }
        return $cus2;
    }
}