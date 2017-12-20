<?php

namespace App\Http\Controllers\Bapi;

use App\Model\ApplyBasic;
use App\Model\BusinessUser;
use App\Model\Logs;
use App\Model\Order;
use App\Model\OrderApplyForm;
use App\Model\Product;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock;

class OrderController extends Controller
{
    protected $model = "";
    public function __construct()
    {
        $this->model = new ApplyBasic();
    }

    /**
     * @param Request $request  type:订单状态  business_id:用户id
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 新匹配、已匹配
     */

    public function Accept(Request $request){
        $OrderType = $request->except(['s']);
        $c_apply_status = [3,4,5];

        $OrderType['b_apply_status'] = [$OrderType['type']];
       if($OrderType['type'] == 4){
           $c_apply_status = [4,7,8];
           $OrderType['b_apply_status'] = [4];
       }else if($OrderType['type'] == 2){
           $c_apply_status = [4,7];
           $OrderType['b_apply_status'] = [2,3,9];
       }else if($OrderType['type'] == 5){
           $OrderType['b_apply_status'] = [5,8];
       }

       if(empty($OrderType['day'])){

           $OrderData = DB::table('user_apply as u')
               ->join('product as p','p.id','=','u.product_id')
               ->join('product_cat as c','c.id','=','p.cat_id')
//               ->join('user as us','us.id','=','u.user_id')
               ->where(['p.business_id'=>$OrderType['business_id']])
               ->whereIn('u.c_apply_status',$c_apply_status)
               ->whereIn('u.b_apply_status',$OrderType['b_apply_status'])
               ->select('u.order_id','c.cat_name','p.content as pData','p.cat_id','c.cat_name','u.user_id','u.order_type','c.p_id','u.create_time','u.order_type','u.b_apply_status')
               ->orderBy('u.create_time','desc')
               ->get();
       }else{
           //处理时间
           $OrderType['day'] = str_replace('年','-',$OrderType['day']);
           $OrderType['day'] = str_replace('月','-',$OrderType['day']);
           $OrderType['day'] = mb_substr($OrderType['day'],0,10);
            //某一天的0点
           $timestamp0 = strtotime($OrderType['day']);
           //某一天的24点
           $timestamp24 = strtotime($OrderType['day']) + 86400;
           $OrderData = DB::table('user_apply as u')
               ->join('product as p','p.id','=','u.product_id')
               ->join('product_cat as c','c.id','=','p.cat_id')
               ->where(['p.business_id'=>$OrderType['business_id']])
               ->whereIn('u.c_apply_status',$c_apply_status)
               ->whereIn('u.b_apply_status',$OrderType['b_apply_status'])
               ->whereBetween('u.create_time',[$timestamp0,$timestamp24])
               ->select('u.order_id','c.cat_name','p.content as pData','p.cat_id','c.cat_name','u.user_id','u.order_type','c.p_id','u.create_time','u.order_type','u.b_apply_status')
               ->orderBy('u.create_time','desc')
               ->get();
       }

       foreach($OrderData as $k=>$v){
           $UserApply = json_decode(DB::table('apply_form')->where([
               'user_id'=>$OrderData[$k]->user_id,
               'cat_id'=>$OrderData[$k]->cat_id,
               'equipment_type'=>$OrderData[$k]->order_type
           ])->value('need_data'),true);
           $OrderData[$k]->lending_cycle = $UserApply['lending_cycle'];
           $OrderData[$k]->accrual = $UserApply['accrual'];
           if($v->order_type){
               // 共享订单
               $OrderData[$k]->user_idcard = BusinessUser::where('id',$v->user_id)->value('idcard');
           }else{
               $OrderData[$k]->user_idcard = User::where('id',$v->user_id)->value('user_idcard');
           }
       }

//        dd($OrderData);
       if($OrderType['type'] == 4){
           $retData = returnData(BusinessOrderData($OrderData,1));
       }else{
           $retData = returnData(BusinessOrderData($OrderData,0));
       }
        return response()->json($retData);
    }

    /**
     * @param Request $request  business_id:用户id cat_id:分类id
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 已放款、已完结
     */

    public function Accepted(Request $request){
        $OrderType = $request->except(['s']);


        $SecId= DB::table('product_cat')->where(['p_id'=>$OrderType['cat_id']])->select('id')->get();
        $Search = [];
        if(!$SecId->isEmpty()){

            foreach($SecId as $k=>$v){
                $Search[$k] = $v->id;
            }
        }
        if(empty($OrderType['day'])){
            $OrderData = DB::table('user_apply as u')
                ->join('product as p','p.id','=','u.product_id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->where(['u.b_apply_status'=>$OrderType['type'],'u.c_apply_status'=>8,'p.business_id'=>$OrderType['business_id']])
                ->whereIn('p.cat_id',$Search)
                ->select('u.order_type','u.c_is_evaluate','u.b_is_evaluate','u.order_id','c.cat_name','p.content as pData','p.cat_id','c.cat_name','u.user_id','u.order_type','c.p_id','u.create_time','u.b_apply_status')
                ->orderBy('u.create_time','desc')
                ->get();
        }else{
            //处理时间
            $OrderType['day'] = str_replace('年','-',$OrderType['day']);
            $OrderType['day'] = str_replace('月','-',$OrderType['day']);
            $OrderType['day'] = mb_substr($OrderType['day'],0,10);
            //某一天的0点
            $timestamp0 = strtotime($OrderType['day']);
            //某一天的24点
            $timestamp24 = strtotime($OrderType['day']) + 86400;
            $OrderData = DB::table('user_apply as u')
                ->join('product as p','p.id','=','u.product_id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->where(['u.b_apply_status'=>$OrderType['type'],'u.c_apply_status'=>8,'p.business_id'=>$OrderType['business_id']])
                ->whereIn('p.cat_id',$Search)
                ->whereBetween('u.create_time',[$timestamp0,$timestamp24])
                ->select('u.order_type','u.c_is_evaluate','u.b_is_evaluate','u.order_id','c.cat_name','p.content as pData','p.cat_id','c.cat_name','u.user_id','u.order_type','c.p_id','u.create_time','u.b_apply_status')
                ->orderBy('u.create_time','desc')
                ->get();
        }




        foreach($OrderData as $k=>$v){
            //这里可能还要修改
            $UserApply = json_decode(DB::table('apply_form')->where([
                'user_id'=>$OrderData[$k]->user_id,
                'cat_id'=>$OrderData[$k]->cat_id,
                'equipment_type'=>$OrderData[$k]->order_type
            ])->value('need_data'),true);
            $OrderData[$k]->lending_cycle = $UserApply['lending_cycle'];
            $OrderData[$k]->accrual = $UserApply['accrual'];

            $UserApplyBasic = json_decode(DB::table('apply_basic_form')->where([
                'user_id'=>$OrderData[$k]->user_id,
                'type'=>$OrderData[$k]->order_type
            ])->value('data'),true);

            $OrderData[$k]->user_idcard = !isset($UserApplyBasic['idCard']) ? "" :$UserApplyBasic['idCard'];

        }

        $retData = returnData(BusinessOrderData($OrderData,1));

        return response()->json($retData);
    }


    /**
     * @param Request $request b_order_status:改变的订单状态 1：已拒绝、取消订单 2：接单 5：拒绝放款 6：已放款   7:已完结 reason:拒绝理由
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description :
     */
    public function OrderChange(Request $request){
        $Orderstatus = $request->except(['s']);
        $b_order_status = [1,2,5,6,7,8];
        if(in_array($Orderstatus['b_apply_status'],$b_order_status)){
            switch ($Orderstatus['b_apply_status']){
                case 1:
                    $update = [
                        'c_apply_status'=>3,
                        'b_apply_status'=>1,
                        'reason'=>$Orderstatus['reason']
                    ];
                    break;
                case 2:
                    $update = [
                        'c_apply_status'=>7,
                        'b_apply_status'=>2,
                    ];
                    break;
                case 5:
                    $update = [
                        'c_apply_status'=>5,
                        'b_apply_status'=>5,
                        'reason'=>$Orderstatus['reason']
                    ];
                    break;
                case 6:
                    $update = [
                        'c_apply_status'=>8,
                        'b_apply_status'=>6,
                    ];
                    break;
                case 7:
                    $update = [
                        'b_apply_status'=>7,
                    ];
                    break;
                default:
                    $update = [
                        'b_apply_status'=>8,
                        'c_apply_status'=>5,
                        'reason'=>$Orderstatus['reason']
                    ];
                    break;
            }
        }
        //如果订单完结 增加用户的50金币
        if($Orderstatus['b_apply_status'] == 7){
            //查询订单类型
            $orderType = UserApply::where([
                'order_id'=>$Orderstatus['order_id']
            ])->value('order_type');
            if($orderType){
                //共享
                BusinessUser::where([
                    'id'=>UserApply::where('order_id',$Orderstatus['order_id'])->value('user_id')
                ])->increment('integral',50);
            }else{
                //个人
                User::where([
                    'id'=>UserApply::where('order_id',$Orderstatus['order_id'])->value('user_id')
                ])->increment('integral',50);

            }
        }
        $OrderUpdate = DB::table('user_apply')->where(['order_id'=>$Orderstatus['order_id']])->update($update);
        $retJson = returnStatus($OrderUpdate);
        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存订单数据
     */

    public function SaveEvaluate(Request $request){
        $EvaluateData               = $request->except('s');
        $retJson                    = SaveEvaluate($EvaluateData,1);
        return response()->json($retJson);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 服务费计算百分比
     */

    public function Server(){
        $Serve  = (float)DB::table('config')->where(['key'=>'server'])->value('value');
        $retJson = returnData($Serve);
        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 门店运营用户评价
     */

    public function GetEvaluate(Request $request){
        $OrderId = $request->except(['s']);
        //获取 订单的产品id
        $product_id = DB::table('user_apply')->where('order_id',$OrderId['order_id'])->value('product_id');

        $EvaData = DB::table('product_evaluate as p')
            ->join('user_apply as u','u.order_id','=','p.order_id')
            ->join('user as s','s.id','=','u.user_id')
            ->select('p.score','p.type','s.user_name','p.desc','p.content')
            ->where([
                'p.product_id'=>$product_id,
                'type'=>0
            ])->get();
//        dd($product_id);
        $retJson = returnData($EvaData);
        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description :
     */

    public function getDesc(Request $request){
        $OrderId = $request->input('order_id');
        //判断订单是否是共享订单

        $isShare = DB::table('user_apply')->where('order_id',$OrderId)->value('order_type');

        if($isShare){
            $EvaData = DB::table('product_evaluate as p')
                ->join('user_apply as u','u.order_id','=','p.order_id')
                ->join('business_user as b','b.id','=','u.user_id')
                ->select('p.score','p.type','p.desc','p.content','b.companyName as user_name')
                ->where([
                    'p.order_id'=>$OrderId,
                    'p.type'=>0,
                ])->first();
        }else{ $EvaData = DB::table('product_evaluate as p')
            ->join('user_apply as u','u.order_id','=','p.order_id')
            ->join('user as s','s.id','=','u.user_id')
            ->select('p.score','p.type','s.user_name','p.desc','p.content')
            ->where([
                'p.order_id'=>$OrderId,
                'p.type'=>0
            ])->first();
        }


        $retJson = returnData($EvaData);
        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : B端首页获取评价详情
     */

    public function getDescB(Request $request){
        $OrderId = $request->input('order_id');
        //判断订单是否是共享订单

        $isShare = DB::table('user_apply')->where('order_id',$OrderId)->value('order_type');

        if($isShare){
            $EvaData = DB::table('product_evaluate as p')
                ->join('user_apply as u','u.order_id','=','p.order_id')
                ->join('business_user as b','b.id','=','u.user_id')
                ->select('p.score','p.type','p.desc','p.content','b.companyName as user_name')
                ->where([
                    'p.order_id'=>$OrderId,
                    'p.type'=>1,
                ])->first();
        }else{ $EvaData = DB::table('product_evaluate as p')
            ->join('user_apply as u','u.order_id','=','p.order_id')
            ->join('user as s','s.id','=','u.user_id')
            ->select('p.score','p.type','s.user_name','p.desc','p.content')
            ->where([
                'p.order_id'=>$OrderId,
                'p.type'=>1
            ])->first();
        }


        $retJson = returnData($EvaData);
        return response()->json($retJson);
    }

    public function OrderData(Request $request){
        $ReasonData = $request->except(['s']);
        $reason = DB::table('user_apply')->where(['order_id'=>$ReasonData['order_id']])->select('reason','product_id','c_apply_status','c_is_evaluate','create_time','user_id')->first();

        $product_data = OrderRead($reason->product_id,$reason->user_id,$ReasonData['order_id']);

        $retData                    = $product_data->content;
        $retData->cat_name          = $product_data->cat_name;
        $retData->company           = $product_data->number;
        $retData->c_apply_status    = $reason->c_apply_status;
        $retData->c_is_evaluate     = $reason->c_is_evaluate;
        $retData->create_time       = date("Y-m-d",$reason->create_time);
        $retData->reason            = $reason->reason;

        $j = [
            'code'=>200,
            'msg'=>"获取成功",
            'data'=>$retData
        ];
        return response()->json($j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : B端首页订单详情，判断是否可查看申请者信息
     */

    public function OrderBasic(Request $request){
        $OrderId = $request->input('order_id');
        $b_apply_status = UserApply::where([
            'order_id'=>$OrderId
        ])->select('b_apply_status','order_type','user_id','product_id')->first();
        $no = [0,1,2,9];
        if(in_array($b_apply_status->b_apply_status,$no)){
            $code = 400;
            $msg = "不能查看";
            $data = 0;
        }else{
            $code = 200;
            if($b_apply_status->order_type == 1){
                //共享订单
                $msg = "共享订单";
                $data = 1;
            }else{
                $cat_id = DB::table('product_cat as pc')->join('product as p','p.cat_id','=','pc.id')->where([
                    'p.id'=>$b_apply_status->product_id
                ])->value('cat_id');
                $arr = [35,36,62,63,64,65,66,67,68,69,71];
                if(in_array($cat_id,$arr)){
                    $data = 2;
                    $msg = "企业贷";
                }else{
                    $data = 3;
                    $msg = "其他";
                }
            }
        }
        $j = [
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data
        ];
        return response()->json($j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 订单详情 =>  查看匹配基础资料
     */

    public function getbasic(Request $request){
        $data = DB::table('user_apply as u')->join('product as p','p.id','=','u.product_id')->where([
            'u.order_id'=>$request->input('order_id')
        ])->first();

        $arr = [35,36,62,63,64,65,66,67,68,69,71];
        if(in_array($data->cat_id,$arr)){
            $is_company = 1;
        }else{
            $is_company = 0;
        }

//        $user = json_decode(DB::table('apply_basic_form')->where([
//            'type'=>$data->order_type,
//            'is_company'=>$is_company
//        ])->value('data'));

        $user = ApplyBasic::where([
            'type'=>$data->order_type,
            'is_company'=>$is_company
        ])->value('data');


        // 判断用户在保存基础资料的时候有没有上传 图片 如果有则提取出来
        $retData = returnData($user);

        return response()->json($retData);
    }


    /**
     * @param Request $request
     * @return array
     * author hongwenyang
     * method description : B端查看资料时 获取C端用户上传的所有图片数据
     */

    public function getImgs(Request $request){
        // 以下为企业贷分类id
        $arr = [35,36,62,63,64,65,66,67,68,69,71];

        $data = DB::table('user_apply as u')->join('product as p','p.id','=','u.product_id')->where([
            'u.order_id'=>$request->input('order_id')
        ])->first();
        if(in_array($data->cat_id,$arr)){
            $is_company = 1;
        }else{
            $is_company = 0;
        }
        // 判断 基础资料里面有没有图片
        $user = ApplyBasic::where([
            'type'=>$data->order_type,
            'is_company'=>$is_company
        ])->value('data');

        $imgs = [];
        // 对图片进行判断

        // 处理基础资料
        // 营业执照图片
        if(isset($user->companyYin) && !empty($user->companyYin)){
            array_push($imgs,$user->companyYin);
        }

        // 行业许可证
        if(isset($user->companyXu) && !empty($user->companyXu)){
            array_push($imgs,$user->companyXu);
        }

        // 处理担保品资料中的图片
        $property = OrderApplyForm::where('order_id',$request->input('order_id'))->value('data');

        // 处理担保品数据
        $property_data = json_decode($property);

        // 房产证正面
        if(isset($property_data->certificateA) && !empty($property_data->certificateA)){
            array_push($imgs,$property_data->certificateA);
        }

        // 房产证背面
        if(isset($property_data->certificateB) && !empty($property_data->certificateB)){
            array_push($imgs,$property_data->certificateB);
        }

        // 汽车登记证
        if(isset($property_data->cardj) && !empty($property_data->cardj)){
            array_push($imgs,$property_data->cardj);
        }

        // 汽车行驶证
        if(isset($property_data->carxs) && !empty($property_data->carxs)){
            array_push($imgs,$property_data->carxs);
        }

        // 汽车驾驶证
        if(isset($property_data->carjs) && !empty($property_data->carjs)){
            array_push($imgs,$property_data->carjs);
        }

        // 应收账凭证  票据凭证
        if(isset($property_data->imgs) && !empty($property_data->imgs)){
            $imgs = json_decode($property_data->imgs,true);
            foreach($imgs as $v){
                array_push($imgs,$v);
            }
        }

        // 汽车驾驶证
        if(isset($property_data->Shangbiao) && !empty($property_data->Shangbiao)){
            array_push($imgs,$property_data->Shangbiao);
        }


        return returnData($imgs);
    }
}
