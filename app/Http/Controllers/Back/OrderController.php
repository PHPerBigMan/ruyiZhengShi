<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushController;
use App\Model\BusinessUser;
use App\Model\Logs;
use App\Model\Order;
use App\Model\OrderApplyForm;
use App\Model\Product;
use App\Model\User;
use App\Model\UserApply;
use App\Model\Yinlian;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    protected $model = "";
    protected $push,$log;
    public function __construct()
    {
        $this->model = new Order();
        $this->push = new PushController();
        $this->log = new Logs();
    }

    /**
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 订单列表
     */

    public function orderList($type){
        $whereOr = [];
        $whereIn = [];
        $where = [];
        if($type == 0){
            //C端待审核订单
            $where = [
                'c_apply_status'=>1,
            ];
            $title = "orderShare";
        }else if($type == 1){
            //B端待审核订单
            $where = [
                'b_apply_status'=>3,
            ];
            $title = "orderBasic";
        }else if($type == 2){
            //审核已通过订单
            $title = "orderPassed";
            $where = [
                'c_apply_status'=>4,
                'b_apply_status'=>4
            ];
        }else if($type == 3){
            //审核未通过订单  这里还要修改
            $title = "orderNoPassed";
            $where = [
                'c_apply_status'=>9,
            ];
            $whereOr = [
                'b_apply_status'=>9
            ];
        }else if($type == 4){
            // 所有订单
            $title = "orderAll";
            $whereIn = [0,1,2,3,4,5,6,7,8,9,10];
        }else if($type == 5){
            //C端用户取消订单
            $title = "ordercancel";
            $whereIn = [2];
        }else if($type == 6){
            //B端用户取消订单
            $title = "orderbcancel";
            $where = [
                'b_apply_status'=>1,
            ];
        }else if($type == 7){
            // 申请退款的订单和退款成功订单
            $title = "ordertui";
            $whereIn = [4,5];
        }else if($type == 8){
            // 已成交订单
            $title = "orderDone";
            $whereIn = [8];
        }
        $time=  isset($_GET['exTime']) ? $_GET['exTime'] :"";

        // 下一页数据携带时间参数
        $nextTime = $time;
        if(!empty($time)){
            $time = explode(' - ',$time);
            $startTime = strtotime($time[0]);
            $endTime   = strtotime($time[1]);
        }else{
            //如果没有时间  则查询一年内的
            $startTime = time() - (60*60*24*365);
            $endTime = time();
        }

        if($type != 0 && $type != 1){
            if(isset($_GET['keyword']) && $_GET['keyword'] != ""){
                $data = $this->model->BackOrder($_GET['keyword'],$where,$whereOr,$whereIn,$startTime,$endTime,$nextTime);
            }else{
                $data = $this->model->BackOrder("",$where,$whereOr,$whereIn,$startTime,$endTime,$nextTime);

            }
        }else{
            if(isset($_GET['keyword']) && $_GET['keyword'] != ""){

                $data = $this->model->OrderYinlian($_GET['keyword'],$where,$whereOr,$type,$_GET['type'],$startTime,$endTime,$nextTime);
            }else{
                $data = $this->model->OrderYinlian("",$where,$whereOr,$type,"",$startTime,$endTime,$nextTime);
            }

        }

        if(!empty($data)){
            $data = Yinlian::getPayImg($data);

        }
        foreach($data as $k=>$v){
            $pNumber = json_decode($v->content);
            $v->pNumber = $pNumber->pNumber;
            if($v->order_type == 1){
                // B端用户
                $v->userName = BusinessUser::where('id',$v->user_id)->value('companyName');
            }else{
                // C端用户
                $v->userName = User::where('id',$v->user_id)->value('user_name');
            }
        }

//        dd($data);
        $j = [
            'type'                  =>$type,
            'data'                  =>$data,
            'Pagetitle'             =>$title,
            'time'                  =>$nextTime,
            'keyword'               =>isset($_GET['keyword']) ? $_GET['keyword'] : "",
            'searchType'            =>isset($_GET['type']) ? $_GET['type'] :""
        ];

        return view('Back.order.list',$j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 修改订单状态
     */

    public function orderChange(Request $request){
        $data = $request->except(['s']);
        $isUse = 0;
        $UserBackMethod = 0;
        // TODO:: 取消订单退还金币
        // 获取订单的类型
        $orderType = UserApply::where('id',$data['id'])->select('order_type','IsIcon','BIsIcon','user_id','product_id')->first();
        if($data['apply_type'] == 0){
            //修改C端用户支付状态
            if($data['type'] == 1){
                //通过
                $status = 4;
            }else if($data['type'] == 0){
                //未通过
                $status = 9;
                // 查询订单C端支付时是否使用金币
                if($orderType->order_type){
                    // 共享订单 此时的C端用户为B端用户
                    if($orderType->IsIcon){
                        // 使用了金币
                        $isUse = 1;
                    }
                }else{
                    // 此时为普通订单
                    if($orderType->IsIcon){
                        // 使用了金币
                        $isUse = 2;
                    }
                }
                $UserBackMethod = 1;
            }else if($data['type'] == 2){
                //退款成功
                $status = 5;
            }else if($data['type'] == 3){
                //取消退款
                $status = 4;
            }
            $update = [
                'c_apply_status'=>$status
            ];
        }else{
            //修改B端用户支付状态
            if($data['type'] == 1){
                //通过
                $status = 4;
                $cstatus = 7;
            }else{
                //未通过
                $status = 9;
                $cstatus = 4;
                // 驳回B端的支付 同时退还B,C两端 的金币
                if($orderType->order_type){
                    // 共享订单 此时的C端用户为B端用户
                    if($orderType->IsIcon){
                        // 使用了金币
                        $isUse = 1;
                    }
                }else{
                    // 此时为普通订单
                    if($orderType->IsIcon){
                        // 使用了金币
                        $isUse = 2;
                    }
                }

                $UserBackMethod = 1;
            }
            $update = [
                'b_apply_status'=>$status,
                'c_apply_status'=>$cstatus
            ];
        }
        $s = DB::table('user_apply')->where([
            'id'=>$data['id']
        ])->update($update);

        if($s){
            try{
                if($data['apply_type'] == 0){
                    // 发送极光消息
                    $type = 1;
                    if($orderType->order_type){
                        // 共享订单 此时的C端用户为B端
                        $type = 2;
                    }
                    $this->push->sendMessage($type,$orderType->user_id,"您的订单支付审核已有结果，请进入app查看详情");
                }else{
                    // B端支付时 获取上传产品对应的B端用户
                    $user_id = Product::where('id',UserApply::where('id',$data['id'])->value('product_id'))->value('business_id');
                    $this->push->sendMessage(2,$user_id,"您的订单支付审核已有结果，请进入app查看详情");
                }
            }catch (\Exception $exception){
                $this->log->logs("发送极光推送消息异常--总后台修改订单状态 (Back/OrderController line:244)",$data);
            }

        }
        /**
         * 支付审核不通过 退款用户抵扣的金币
         */
        if($UserBackMethod){
            UserApply::BackIcon($isUse,$orderType,$data['id']);
        }

        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }

    /**
     * @param Request $request
     * @return mixed
     * 取消订单
     */
    public function orderCancel(Request $request){
        $id = $request->input('id');
        $s =  UserApply::where('id',$id)->update([
            'c_apply_status'=> 10,
        ]);
        return returnStatus($s);
    }

    public function checkOrderStatus(Request $request){
        $id = $request->input('id');
        $isPay  = UserApply::where('id',$id)->value('c_apply_status');
        $status = [3,4,7,8];
        if(in_array($isPay,$status)){
            $code = 403;
        }else{
            $code = 200;
        }
        return response()->json(['code'=>$code]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 查询订单详细数据
     */
    public function readMore($id){

        $ApplyUser = array();
        // 查询订单数据
        $data = UserApply::findOrFail($id);

        // 查询借款人信息 填写的信息
        $ApplyUser = OrderApplyForm::where('order_id',$data->order_id)->first();

        // 用户基本信息
        $userInfo = Order::ReadMoreOrder($data);

        // 产品信息
        $product = Product::findOrFail($data->product_id);

        if(!empty($product)){
            $product->content = json_decode($product->content);
        }
        return view('Back.order.readMore',compact('data','ApplyUser','userInfo','product'));
    }
}
