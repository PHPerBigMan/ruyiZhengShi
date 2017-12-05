<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function index(){
        $data = DB::table('feedback')->where(['user_id'=>session('business_admin'),'customer_type'=>1])->paginate(10);

        $title = 'feedback';
        return view('Business.feedback.index',compact('data','title'));
    }


    public function FeedbackSave(Request $request){
        $FeedbackData = $request->except('s');
        $FeedbackData['user_id']        = session('business_admin');
        $FeedbackData['customer_type']  = 1;
        $s = DB::table('feedback')->insert($FeedbackData);
        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = '反馈提交成功';
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = '反馈提交失败';
        }

        return response()->json($retJson);
    }


}
