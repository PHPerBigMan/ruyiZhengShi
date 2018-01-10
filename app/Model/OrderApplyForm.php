<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderApplyForm extends Model
{
    protected $table = 'order_apply_form';

//    public function getNeedDataAttribute($value){
//        if(!empty($value)){
//            return json_decode($value);
//        }
//        return "";
//    }
//
//    public function getDataAttribute($value){
//        if(!empty($value)){
//            return json_decode($value);
//        }
//        return "";
//    }
}
