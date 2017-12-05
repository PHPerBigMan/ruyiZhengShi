<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntegralController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author  hongwenyang
     * method description : 积分页面
     */

    public function index(){
      $data = DB::table('business_user')->where(['id'=>session('business_admin')])->value('integral');

      $title = 'integral';
      return view('Business.integral.index',compact('data','title'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取积分列表
     */

   public function integralList(){
       $data = DB::table('integral_list')->where(['user_id'=>session('business_admin'),'user_type'=>1])->get();

       foreach ($data as $k=>$v){
           switch ($v->type){
               case 0:
                   $v->name = "注册";
                   $v->integral = "+".$v->integral;
                   break;
               case 1:
                   $v->name = "推送他人注册";
                   $v->integral = "+".$v->integral;
                   break;
               case 2:
                   $v->name = "借款成功";
                   $v->integral = "+".$v->integral;
                   break;
               case 4:
                   $v->name = "还款已经完成";
                   $v->integral = "+".$v->integral;
                   break;
               default:
                   $v->name = "支付消耗";
                   $v->integral = "-".$v->integral;
                   break;
           }
       }
       return response()->json($data);
   }

}
