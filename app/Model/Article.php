<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'article';

    public function getTypeAttribute($value){
        switch ($value){
            case 0:
                $title= "服务协议";
                break;
            case 1:
                $title = "";
                break;
            case 2:
                $title = "客户端如易平台介绍";
                break;
            case 3:
                $title = "声明";
                break;
            case 4:
                $title = "客户端注册成功消息";
                break;
            case 5:
                $title = "商户端注册成功消息";
                break;
            case 6:
                $title = "客户端注册协议";
                break;
            case 7:
                $title = "商户端注册协议";
                break;
            case 8:
                $title = "黑名单使用申明";
                break;
            case 9:
                $title = "如易金币声明";
                break;
            case 10:
                $title = "客户端使用提醒";
                break;
            case 11:
                $title = "商户端使用提醒";
                break;
            case 12:
                $title = "商户端如易平台介绍";
                break;
            case 13:
                $title = "排名保护说明";
                break;
        }
        return $title;
    }

//    public function getContentAttribute($value){
////        return mb_substr($value,0,80);
//    }
}
