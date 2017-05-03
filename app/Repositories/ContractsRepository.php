<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 15:09
 */

namespace App\Repositories;

use App\Contract;
use App\Customer;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ContractsRepository
{
    public function newContract($request)
    {
        $id = $request->parentId;
//        $id = $request->customer_id;  //等contract专门做了客户单位维护再改

        //拿到最后的一条数据，以此为凭据制造下个编号；(所以部署时需要至少填充一条无效数据)
        $info = Contract::orderBy('id','desc')->limit(1)->pluck('contract_id');
        $number = substr($info[0], 0, 8);

        //todo 生成编号
        $date = date('Ymd');

        if(!$request->contract_id){
            if ($number == $date){   //自动生成contract_id
                $contract_id = $info[0]+1; //假如今天是同一天
            }else{
                $contract_id = $date.'001'; //假如今天是新的一天
            }
        }else{//手工填写
            $contract_id = $request->contract_id;
        }

        //todo 将确定保存的临时文件转移到contracts目录下
        $files = $request->file;
        //todo 转移并删除临时文件
        $files = $this->moveFiles($files);

        $files = serialize($files);
        $pm = serialize($request->pm);
        $tm = serialize($request->tm);

        //todo 数据持久化
        $cus = Customer::findOrFail($id);
        $cus->contracts()->create([
            'customer_name'=>$cus->name,
            'contract_id' => $contract_id,
            'type' => $request->type,
            'contract_type'=>$request->contract_type,
            'name' => $request->name,
            'pm' => $pm,
            'tm' => $tm,
            'sum' => $request->sum,
            'time1'=>strtotime($request->time1),
            'time2'=>strtotime($request->time2),
            'time3'=>strtotime($request->time3),
            'main_unit'=>$request->main_unit,
            'desc'=>$request->desc,
            'file'=>$files,
        ]);
    }

    public function updateContract($request, $id)
    {
        $contract = Contract::findOrFail($id);

        //todo 对比新旧files，做增删
        $new_files = $request->file;
        $old_files = unserialize($contract->file);

        if($new_files !== $old_files){
            //todo 优化“空”的情况
            $kong = $this->isKong($old_files, $new_files);
            $removes = $kong[0];
            $adds = $kong[1];

            //todo 删除确定被移除的文件
            if(!empty($removes)){
                foreach ($removes as $remove){
                    Storage::delete($remove['path']);
                }
            }

            //todo 将新增文件从temp文件夹转移到contracts文件夹中
            if(!empty($adds)){
                $adds = $this->moveFiles($adds);
                $new_files = $this->updateAdds($adds, $new_files);
            }
        }

        //todo 数组序列化
        $files = serialize($new_files);
        $pm = serialize($request->pm);
        $tm = serialize($request->tm);

        if($request->contract_id){
            //todo 手工id
            $contract->update([
                'customer_id'=>$request->customer_id,
                'customer_name'=>$request->customer_name,
                'contract_id' => $request->contract_id,
                'name' => $request->name,
                'type' => $request->type,
                'contract_type'=>$request->contract_type,
                'pm' => $pm,
                'tm' => $tm,
                'sum' => $request->sum,
                'time1'=>strtotime($request->time1),
                'time2'=>strtotime($request->time2),
                'time3'=>strtotime($request->time3),
                'main_unit'=>$request->main_unit,
                'desc'=>$request->desc,
                'file'=>$files
            ]);
        }else{
            //todo 自动id
            $contract->update([
                'customer_id'=>$request->customer_id,
                'customer_name'=>$request->customer_name,
                'name' => $request->name,
                'type' => $request->type,
                'contract_type'=>$request->contract_type,
                'pm' => $pm,
                'tm' => $tm,
                'sum' => $request->sum,
                'time1'=>strtotime($request->time1),
                'time2'=>strtotime($request->time2),
                'time3'=>strtotime($request->time3),
                'main_unit'=>$request->main_unit,
                'desc'=>$request->desc,
                'file'=>$files
            ]);
        }

    }
    //用于contractsall组件
    public function showAllContracts()
    {
        $contracts = Contract::orderBy('id','desc')->get();
        foreach ($contracts as $contract){
            $contract->pm = unserialize($contract->pm);
            $contract->tm = unserialize($contract->tm);
            $contract->file = unserialize($contract->file);
        }
        return $contracts;
    }

    /**
     * 上传附件第二版本，先上传至临时文件夹（本文件夹每天清空一次）
     * @param $request
     * @return array
     */
    public function upload($request)
    {
        if($request->hasFile('contractsFiles')){
            $files = $request->contractsFiles;
            $file = $files[0];
            $name = $file->getClientOriginalName();
            $tempPath = Storage::putFile('public/temp', $file);
            $array = [
                'name' => $name,
                'path' => $tempPath
            ];
            return $array;
        }
    }

    /**
     * 转移文件并返回新路径
     * @param $files
     * @return array|void
     */
    public function moveFiles($files)
    {
        if(!is_array($files))
            return;

        foreach ($files as $k => $file){
            $temp_path = $file['path'];

            $path = new File(storage_path('app/' .$file['path']));

            $files[$k]['path'] = Storage::putFile('public/contracts', $path);

            Storage::delete($temp_path);  //删除临时文件
        }

        return $files;
    }

    /**
     * $adds是$new_files相对于$old_files，新增的数组。
     * 将$adds中更新的路径，更新给$new_files中对应的数组（否则新增数组还是temp的路径）
     * @param $adds
     * @param $new_files
     * @return array|void
     */
    public function updateAdds($adds, $new_files)
    {
        if(!is_array($adds) || !is_array($new_files))
            return;
        //todo 第一步：得到两个数组更新元素的$keys的映射
        $keys = [];
        foreach ($adds as $m => $add){
            $add_name = array_get($adds, $m.'.name');
            foreach ($new_files as $n => $new_file){
                if($add_name == array_get($new_files, $n.'.name')){
                    $keys[] = [$m, $n];
                }
            }
        }
        //todo 第二步，更替路径
        foreach ($keys as $key){
            $new_files[$key[1]] = $adds[$key[0]];
        }

        return $new_files;
    }

    public function isKong($old_files, $new_files)
    {
        if(!empty($old_files)){
            if(!empty($new_files)){
                $removes = myGetMulDiff($old_files, $new_files);
                $adds = myGetMulDiff($new_files, $old_files);
            }else{
                $removes = $old_files;
                $adds = [];
            }
        }else{
            if(!empty($new_files)){
                $removes = [];
                $adds = $new_files;
            }else{
                $removes = [];
                $adds = [];
            }
        }
        return [
            $removes, $adds
        ];
    }
}