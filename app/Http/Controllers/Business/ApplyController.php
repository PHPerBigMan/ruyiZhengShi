<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Model\Apply;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplyController extends Controller
{
    protected $user_apply = "";
    public function __construct()
    {
        $this->user_apply = new Apply();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author  hongwenyang
     * method description : 匹配列表
     */

    public function index(){
        $data = DB::table('product_cat')->where(['level'=>2])->get();
        $title = 'list';
      return view('Business.apply.index',compact('data','title'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取匹配列表
     */

   public function applyList(Request $request){
       $ApplyData = $request->except(['s']);

       //C端申请状态
       $c_apply_status = [4,5,6,7,8];
       switch ($ApplyData['type']){
           case 0:
               //B端状态
               $b_apply_status = [0,2,3,5,6];
               break;
           case 1:
               $b_apply_status = [1];
               break;
           case 2:
               $b_apply_status = [0,2,3,5,6];
               break;
           case 3:
               $b_apply_status = [1];
               break;
           case 4:
               $b_apply_status = [6];
               break;
           default:
               $b_apply_status = [1];
       }

       if($ApplyData['type'] == 0 || $ApplyData['type'] == 1){
           //今日匹配
           if($ApplyData['search'] == 0){
               $data = $this->user_apply->Today($c_apply_status,$b_apply_status,$ApplyData['search']);
           }else{
               $data = $this->user_apply->Today($c_apply_status,$b_apply_status,$ApplyData['search'],$ApplyData['cat_id'],$ApplyData['apply_status']);
           }
       }else{
           //历史匹配 和 已完结
           if($ApplyData['search'] == 0){
               $data = $this->user_apply->History($c_apply_status,$b_apply_status,$ApplyData['search']);

           }else{
               $data = $this->user_apply->History($c_apply_status,$b_apply_status,$ApplyData['search'],$ApplyData['cat_id'],$ApplyData['apply_status'],$ApplyData['create_time']);
           }
       }

       $retData = $this->retApplyData($data);

       return response()->json($retData);
   }


    /**
     * @param $apply
     * @return string
     * author hongwenyang
     * method description : 返回B端申请状态
     */

   public function ApplyStatus($apply){
       switch ($apply){
           case 0 :
               $status = "未匹配";
               break;
           case 1 :
               $status = "已拒绝";
               break;
           case 2:
               $status = "待支付";
               break;
           case 3:
               $status = "支付审核中";
               break;
           case 4:
               $status = "已支付";
               break;
           case 5:
               $status = "拒绝放款";
               break;
           case 6:
               $status = "已放款";
               break;
           default:
               $status = "已完结";
       }

       return $status;
   }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 成功匹配页面
     */
   public function Success($type){
       $CatData = DB::table('product_cat')->where(['level'=>2])->get();

       $data = $this->user_apply->applyList($type);

       switch ($type){
           case 0:
               $view = 'gr_dzf';
               break;
           case 1:
               $view = 'gx_dzf';
               break;
           case 2:
               $view = 'gr_yzf';
               break;
           case 3:
               $view = 'gx_yzf';
               break;
           case 4:
               $view = 'gr_yfk';
               break;
           default:
               $view = 'gx_yfk';
               break;
       }
       $title = 'success';
       return view('Business.success.'.$view,compact('CatData','data','title'));
   }

   public function successList(Request $request){
       $successData = $request->except(['s']);

       switch ($successData['type']){
           case 0:
               $successData['user_apply.order_type'] = 0;
               $PayStatus = [2,3];
               break;
           case 1:
               $successData['user_apply.order_type'] = 1;
               $PayStatus = [2,3];
               break;
           case 2:
               $successData['user_apply.order_type'] = 0;
               $PayStatus = [4];
               break;
           case 3:
               $successData['user_apply.order_type'] = 1;
               $PayStatus = [4];
               break;
           case 4:
               $successData['user_apply.order_type'] = 0;
               $PayStatus = [6,7];
               break;
           default:
               $successData['user_apply.order_type'] = 1;
               $PayStatus = [6,7];
       }

       unset($successData['type']);

       $data = $this->user_apply->Success($successData,$PayStatus);


       $retData = $this->retApplyData($data);

       return response()->json($retData);
   }


    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 返回处理后的数据
     */

   public function retApplyData($data){
       foreach($data as $k=>$v){
           $basic_data = json_decode($v->basic_data,true);
           $apply_data = json_decode($v->data,true);
           $retData[$k]['money']            = $v->order_count;
           $retData[$k]['b_apply_status']   = $this->ApplyStatus($v->b_apply_status);
           if($v->is_company){
               $retData[$k]['name']  = $v->b_apply_status < 3 ? mb_substr($basic_data['companyName'],0,1)."**" : $basic_data['companyName'];
               $retData[$k]['phone'] = "无";
           }else{
               $retData[$k]['name']  = $v->b_apply_status < 3 ? mb_substr($basic_data['name'],0,1)."**" : $basic_data['name'];
               $retData[$k]['phone'] = $v->b_apply_status < 3 ? substr($basic_data['phone'],0,3)."********" : $basic_data['phone'];
           }
           $retData[$k]['cat']   = DB::table('product_cat')->where(['id'=>DB::table('product_cat')->where(['id'=>$apply_data['cat_id']])->value('p_id')])
                   ->value('cat_name').'-'.$v->cat_name;
           $retData[$k]['orderType'] = $v->order_type == 0 ? "个人" : "共享";
           $retData[$k]['create_time'] = date("Y-m-d",$v->create_time);
           $retData[$k]['id'] = $v->order_id;
           $retData[$k]['read'] = "<button onclick='read(".$v->id.")' style='width: 58px;height: 28px;background-color: #009688;color: #fff;border: none;border-radius: 2px;cursor: pointer'>查看</button>";

           switch ($v->b_apply_status){
               case 0:
                   $title = "接单";
                   $type = 0;
                   $width = "58px";
                   break;
               case 1:
                   $title = "已拒单";
                   $type = 1;
                   $width = "58px";
                   break;
               case 2:
                   $title = '支付';
                   $type = 2;
                   $width = "58px";
                   break;
               case 3:
                   $title = "支付审核中";
                   $type = 3;
                   $width = "88px";
                   break;
               case 4:
                   $title = "放款";
                   $type = 4;
                   $width = "88px";
                   break;
               case 5:
                   $title = "已拒绝放款";
                   $type = 5;
                   $width = "88px";
                   break;
               case 6:
                   $title = "已放款";
                   $type = 6;
                   $width = "88px";
                   break;
               case 7:
                   $title = "已完结";
                   $type = 7;
                   $width = "88px";
                   break;
           }
           $retData[$k]['caozuo'] = "<button id='".$v->id."' onclick='pay(".$v->id.",$type,this)' style='width: ".$width.";height: 28px;cursor: pointer;background-color: #009688;color: #fff;border: none;border-radius: 2px'>$title</button><button onclick='cancel(".$v->id.",$type)' style='margin-left:3px;width: 58px;height: 28px;background-color: #FF5722;color: #fff;cursor: pointer;border: none;border-radius: 2px'>取消</button>";
       }

       if(empty($retData)){
           $retData = [];
       }

       return $retData;
   }



   public function OrderCancel(Request $request){
       $map['id'] = $request->input('id');
       DB::table('user_apply')->where($map)->update([
           'b_apply_status'=> $request->input('b_apply_status')
       ]);
       return response()->json(200);
   }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 修改订单状态
     */

   public function changeOrder(Request $request){
       $b_apply_status = $request->except(['s']);
       $s = DB::table('user_apply')->where([
           'id'=>$b_apply_status['id']
       ])->update([
           'b_apply_status'=>$b_apply_status['b_apply_status']
       ]);

       if($s){
           $retJson['code'] = 200;
           $retJson['msg']  = "操作成功";
       }else{
           $retJson['code'] = 304;
           $retJson['msg']  = "操作失败";
       }

       return response()->json($retJson);
   }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 上传支付凭证页面
     */

   public function Yinlian($id,$type){
       //订单数据
       $order_data = DB::table('user_apply as u')
                        ->join('product as p','u.product_id','=','p.id')
                        ->join('business_user as b','b.id','=','p.business_id')
                        ->where([
                            'u.id'=>$id
                        ])->select('b.number','p.content','u.order_id')->first();

       $product = json_decode($order_data->content,true);
       $order_data->area = $product['area'];
       $order_data->accrual = $product['accrual'];
       $order_data->type = $product['type'];

       //收款账户信息
       $Ylian = DB::table('bank')->first();

       //转账信息
       $data = DB::table('yinlian')->where([
           'order_id'=>$order_data->order_id,
           'type'=>$type
       ])->value('img');
       $imgs = json_decode('{}');
       $imgs->imgs = [];
       if(!empty($data)){

           $imgs->imgs = json_decode($data,true);
       }

       $j = [
           'orderData'=>$order_data,
           'yinlian'=>$Ylian,
           'order_id'=>$order_data->order_id,
           'data'=>$imgs,
           'type'=>$type
       ];
       return view('Business.apply.yinlian',$j);
   }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存数据
     */

   public function yinlianSave(Request $request){
       $imgs = $request->input('imgs');
       $data['img'] = json_encode(explode(',','/uploads/'.$imgs));
       $data['order_id'] = $request->input('order_id');
       $data['type'] = $request->input('type');
       $is_have = DB::table('yinlian')->where([
           'order_id'=>$data['order_id'],
           'type'=>1
       ])->first();

       if($is_have != null){
           $s = DB::table('yinlian')->where([
               'order_id'=>$data['order_id'],
               'type'=>$data['type']
           ])->update([
               'img'=>$data['img']
           ]);
       }else{
           if($data['type'] == 0){
               //共享订单支付
               $update['c_apply_status'] = 1;
           }else{
               $update['b_apply_status'] = 3;
           }
           $s = DB::table('yinlian')->insert($data);
           //修改订单的状态
           DB::table('user_apply')->where([
               'order_id'=>$data['order_id'],
           ])->update($update);
       }

       $j = returnStatusBack($s);
       return response()->json($j);
   }


   public function demo(){
   }
}
