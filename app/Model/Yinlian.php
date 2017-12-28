<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Yinlian extends Model
{
    protected $table = 'yinlian';


    public static function getPayImg($data){
        foreach($data as $k=>$v){
            $v->CPayimg = json_decode(Yinlian::where(['order_id'=>$v->order_id,'type'=>0])->value('img'),true);
            $v->BPayimg = json_decode(Yinlian::where(['order_id'=>$v->order_id,'type'=>1])->value('img'),true);
        }
        return $data;
    }
}
