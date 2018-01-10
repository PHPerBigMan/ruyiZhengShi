<?php

namespace App\Http\Controllers\Back;

use App\Model\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JPushController extends Controller
{
    public function index(){
        $data = Message::where('is_back',1)->groupBy('create_time')->get();

        $Pagetitle = "JPush";

        return view('Back.message.Index',compact('data','Pagetitle'));
    }

    public function JPushSend(){
        return view('Back.message.Send');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 后台发送推送消息 [系统消息]
     */
    public function send(Request $request){
        $post = $request->except(['s']);

        $s = Message::sendJPushMessage($post);

        return response()->json($s);
    }
}
