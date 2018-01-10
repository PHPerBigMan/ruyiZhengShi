<?php

namespace App\Model;

use App\Http\Controllers\Back\JPushController;
use App\Http\Controllers\PushController;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "message";

    /**
     * @param $data
     * @return int
     * author hongwenyang
     * method description : 后台发送推送消息 [系统消息]
     */
    public static function sendJPushMessage($data){
        if($data['type']){
            // B端用户
            $user = BusinessUser::pluck('id');
        }else{
            // C端用户
            $user = User::pluck('id');
        }

        foreach($user as $v){
            Message::create([
                'content'=>$data['content'],
                'title'=>$data['title'],
                'img'=>$data['img'],
                'user_id'=>$v,
                'type'=>2,
                'is_back'=>1,
                'is_read'=>0,
                'equipment_type'=>$data['type']
            ]);
        }

        // 开始发送极光推送消息
        $Jpush = new PushController();

        $Jpush->AllUserMessage($data['type'],"您有一条新消息");

        return 200;
    }
}
