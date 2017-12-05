<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Model\Evaluate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluateController extends Controller
{
    protected $evaluate = "";
    public function __construct()
    {
        $this->evaluate = new Evaluate();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author  hongwenyang
     * method description : 评价列表
     */

    public function index(){
      $data = DB::table('product_cat')->where(['level'=>2])->get();

      $title = 'evaluate';
      return view('Business.evaluate.index',compact('data','title'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取评价列表
     */

   public function evaluateList(Request $request){
       $is_evaluted = $request->except(['s']);
       if(empty($is_evaluted['cat_id'])){
           $data = DB::table('user_apply')
               ->leftJoin('product','product.id','=','user_apply.product_id')
               ->leftJoin('apply_form','apply_form.id','=','user_apply.apply_form_id')
               ->leftJoin('product_cat','product.cat_id','=','product_cat.id')
               ->leftJoin('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
               ->where([
                   'product.business_id'=>session('business_admin'),
                   'user_apply.c_is_evaluate'=>1,
                   'user_apply.b_is_evaluate'=>$is_evaluted['b_is_evaluate']
               ])
               ->select("user_apply.b_is_evaluate","user_apply.id as dataId","user_apply.order_type",'product_cat.cat_name','user_apply.id','apply_basic_form.data as basic_data','product.content','apply_form.data','user_apply.order_type')
               ->get();
       }else{
           $data = DB::table('user_apply')
               ->leftJoin('product','product.id','=','user_apply.product_id')
               ->leftJoin('apply_form','apply_form.id','=','user_apply.apply_form_id')
               ->leftJoin('product_cat','product.cat_id','=','product_cat.id')
               ->leftJoin('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
               ->where([
                   'product.business_id'=>session('business_admin'),
                   'user_apply.c_is_evaluate'=>1,
                   'user_apply.b_is_evaluate'=>$is_evaluted['b_is_evaluate'],
                   'product.cat_id'=>$is_evaluted['cat_id']
               ])
               ->select("user_apply.b_is_evaluate","user_apply.id as dataId","user_apply.order_type",'product_cat.cat_name','user_apply.id','apply_basic_form.data as basic_data','product.content','apply_form.data','user_apply.order_type')
               ->get();
       }

       if(count($data) == 0){
           $retData = [];
       }else{
           foreach ($data as $k=>$v){
               //基本资料
               $UserBasic = json_decode($v->basic_data,true);
               //申请资料
               $ApplyData = json_decode($v->data,true);
               $retData[$k]['name'] = $UserBasic['name'];
               $retData[$k]['phone'] = $UserBasic['phone'];
               $retData[$k]['money'] = $ApplyData['money'];
               $retData[$k]['cat']   = DB::table('product_cat')->where(['id'=>DB::table('product_cat')->where(['id'=>$ApplyData['cat_id']])->value('p_id')])
                       ->value('cat_name').'-'.$v->cat_name;
               $retData[$k]['orderType'] = $v->order_type == 0 ? "个人" : "共享";
               $retData[$k]['id'] = $v->dataId;
               $retData[$k]['b_is_evaluate'] = $v->b_is_evaluate;
           }
       }

       return response()->json($retData);
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 评价页面
     */

   public function evaluateAdd($id,$type){
       $UserData = DB::table('user_apply')->where(['id'=>$id])->select("user_id","product_id")->first();

       if($type == 1){
           $B_Ceva = DB::table('product_evaluate')->where(['user_id'=>$UserData->user_id,'product_id'=>$UserData->product_id,'type'=>1])->first();
           $C_Beva = DB::table('product_evaluate')->where(['user_id'=>$UserData->user_id,'product_id'=>$UserData->product_id,'type'=>0])->first();
       }

      $j = [
          'data'=>$UserData,
          'bEvaluate'=>empty($B_Ceva) ? [] : $B_Ceva,
          'cEvaluate'=>empty($C_Beva) ? [] : $C_Beva,
          'type'=>$type
       ];
      return view('Business.evaluate.add',$j);
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存评价内容
     */

   public function evaluateSave(Request $request){
       $EvaData = $request->except(['s']);
       $EvaData['type'] = 1;
       //保存评价内容
       DB::table('product_evaluate')->insert($EvaData);
       //修改  user_apply 的状态
       DB::table('user_apply')->where(['user_id'=>$EvaData['user_id'],'product_id'=>$EvaData['product_id'],'b_is_evaluate'=>0])->update([
           'b_is_evaluate'=>1
       ]);
       $retJson['code'] = 200;
       $retJson['msg']  = "评价成功";
       return response()->json($retJson);
   }
}
