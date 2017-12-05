<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserApply extends Model
{
    protected $table = 'user_apply';
    public function scopeValid($query)
    {
        return $query->where('c_apply_status',8)->whereIn('b_apply_status',[6,7]);
    }

    public function product(){
        return $this->hasOne(Product::class,'id','product_id');
    }
    /**
     * @param $user_id
     * @return \Illuminate\Support\Collection
     * author hongwenyang
     * method description : B端后台获取共享订单
     */



    public function getNoPayList($user_id){
        $data = $this->join('product as p','p.id','=','user_apply.product_id')
            ->join('product_cat as pc','pc.id','=','p.cat_id')
            ->where([
            'user_apply.user_id'=>$user_id,
            'user_apply.order_type'=>1,
        ])->select('user_apply.order_id','p.content','pc.cat_name','p.cat_id','user_apply.create_time','user_apply.id','user_apply.c_apply_status','user_apply.id')
            ->orderByDesc('user_apply.create_time')
            ->get();
        foreach($data as $k=>$v){
            $v->data = json_decode(DB::table('apply_form')->where([
                'user_id'=>$user_id,
                'cat_id'=>$v->cat_id,
                'equipment_type'=>1
            ])->value('need_data'));
            $v->content = json_decode($v->content);
        }

        return $data;
    }
}
