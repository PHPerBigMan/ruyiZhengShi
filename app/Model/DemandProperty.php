<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:26
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DemandProperty extends Model{
    protected $table = 'demand_property';

    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 保存需求信息
     */

    public function saveData($data){
        foreach($data as $k=>$v){
            $data[$k] = json_encode($v);
        }
        if(empty($data['id'])){
            $return = $this->insert($data);
        }else{
            $id = json_decode($data['id']);
            unset($data['id']);
            $return = $this->where(['id'=>$id])->update($data);
        }
        if($return){
            $retJson['code'] = 200;
            $retJson['msg']  = "需求信息保存成功";
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = "需求信息保存失败";
        }

        return $retJson;
    }
}