<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BusinessChild extends Model
{
    protected $table = "business_child";

    public static  function addChild($data,$user_id){
        $AllCount = BusinessChild::where('p_id',$user_id)->count();
        if($AllCount >= 3){
            $code = 404;
            $msg = "子账号个数超过上限";
        }else{
            $isHave = BusinessChild::where('name',$data['name'])->value('id');
            if($isHave){
                $code = 404;
                $msg = "该账号已存在";
            }else{
                BusinessChild::insert([
                    'p_id'=>$user_id,
                    'name'=>$data['name'],
                    'password'=>sha1($data['password'])
                ]);
                $code = 200;
                $msg  = "添加成功";
            }
        }
        return response()->json(['code'=>$code,'msg'=>$msg]);
    }
}
