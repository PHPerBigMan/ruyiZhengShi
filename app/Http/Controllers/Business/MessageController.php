<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description :
     */

    public function index(){
        $data = DB::table('business_message')->get();
        $title = 'message';
        return view('Business.message.index',compact('data','title'));
    }

    /**
     * @param $id
     * author hongwenyang
     * method description : 消息详情
     */

    public function read($id){
        $data = DB::table('business_message')->where([
            'id'=>$id
        ])->first();
        echo $data->content;
    }
}
