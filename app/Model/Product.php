<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:26
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    protected $table = 'product';
    protected $fillable = ['is_show'];

    public function user_apply(){
       return  $this->belongsTo(UserApply::class,'product_id','id');
    }

    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description :
     * param :  company->公司名称 housekeeper->金融管家  company_phone->公司联系方式
     *          accrual->产品利息 money->金额  product_cycle->借款周期  lending_type->还本付息方式  lending_cycle->放款周期 property_cut->担保品折扣率
     *          other_money->其他费用（array  server:服务费  investigate:调查费 other:其他费用 ）
     *
     *          产品放款范围根据不同的大类进行选择字段名称
     *
     *
     */
    public function saveData($data){
        if(empty($data['id'])){
            $return = $this->insert([
                'content'=>json_encode($data),
                'cat_id'=>json_decode($data['cat_id'])
            ]);
        }else{
            $id = json_decode($data['id']);
            unset($data['id']);
            $return = $this->where(['id'=>$id])->update([
                'content'=>json_encode($data),
            ]);
        }
        if($return){
            $retJson['code'] = 200;
            $retJson['msg']  = "产品数据保存成功";
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = "产品数据保存失败";
        }

        return $retJson;
    }

    public function business()
    {
        return $this->belongsTo(BusinessUser::class);
    }

    public function getContentArrAttribute()
    {
        return json_decode($this->content,true);
    }

    public function getStatusInfoAttribute()
    {
        if ($this->is_show == 2) {
            return '审核中';
        }
        if ($this->is_show == 1) {
            return '上架中';
        }
        return '审核未通过';
    }



}