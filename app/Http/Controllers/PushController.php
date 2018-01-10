<?php

namespace App\Http\Controllers;

use App\Model\Logs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JPush\Client as JpushClient;

class PushController extends Controller
{

    /**
     * @param $alias
     * @param $message
     * author hongwenyang
     * method description : 推送给单独的用户  C端用户
     */
    public function pushMessage($type,$alias,$message){
        if($type == 1){
            $config = config('app.JPushC');
        }else{
            $config = config('app.JPushB');
        }
        $client = new JpushClient($config['appKey'],$config['masterSecret']);

        try{

            $response = $client->push()
                ->setPlatform(array('ios', 'android'))
                ->addAlias("$alias")
                ->iosNotification($message, array(
                    'sound' => 'sound.caf',
                    'category' => 'jiguang',
                    'extras' => array(
                        'key' => 'value',
                        'jiguang'
                    ),
                ))
                ->addAndroidNotification($message,"如易金服")
                ->options(array(
                    'apns_production' => true,

                ))
                ->send();

        }catch (\Exception $exception){
            $log = new Logs();
            $log->logs("发送极光推送消息",[$alias,$message]);
        }
    }

    public function pushMessageTest($type,$alias,$message){
        if($type == 1){
            $config = config('app.JPushC');
        }else{
            $config = config('app.JPushB');
        }
        $client = new JpushClient($config['appKey'],$config['masterSecret']);

        try{

            $response = $client->push()
                ->setPlatform(array('ios', 'android'))
                 ->addAlias("$alias")
                ->iosNotification($message, array(
                    'sound' => 'sound.caf',
                    'category' => 'jiguang',
                    'extras' => array(
                        'key' => 'value',
                        'jiguang'
                    ),
                ))
                ->addAndroidNotification($message,"如易金服")
                ->options(array(
                    'apns_production' => true,

                ))
                ->send();
            dd($response);
        }catch (\Exception $exception){
            $log = new Logs();
            $log->logs("发送极光推送消息",[$alias,$message]);
        }
    }
    /**
     * @param $message
     * author hongwenyang
     * method description : 推送给所有的用户
     */
    public function AllUserMessage($type,$message){
        if($type == 1){
            $config = config('app.JPushC');
        }else{
            $config = config('app.JPushB');
        }
        $client = new JpushClient($config['appKey'],$config['masterSecret']);
        try{
            $client->push()
                ->setPlatform('all')
                ->addAllAudience()
                ->setNotificationAlert($message)
                ->send();
        }catch (\Exception $exception){
            $log = new Logs();
            $log->logs("发送极光推送消息",[$message]);
        }
    }

    public function send(){
        $this->pushMessageTest(1,6,"测试一下IOS!");
//        $this->AllUserMessage(1,"消息测试！");
    }


    public function sendMessage($type,$id,$message){
        $this->pushMessage($type,$id,$message);
    }
}
