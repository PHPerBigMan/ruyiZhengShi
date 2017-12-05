<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Model\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    protected $model = "";
    public function __construct()
    {
        $this->model = new Order();
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
            //已成交订单
            $title = "orderDone";
            $where = [
                'c_apply_status'=>8,
                'b_apply_status'=>7,
            ];
            $whereOr = [
//                'b_apply_status'=>7
            ];
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
        foreach($data as $k=>$v){
            $pNumber = json_decode($v->content);
            $v->pNumber = $pNumber->pNumber;
        }
//        dd($type);
        $j = [
            'type'                  =>$type,
            'data'                  =>$data,
            'Pagetitle'             =>$title,
            'time'                  =>$nextTime,
            'keyword'               =>isset($_GET['keyword']) ? $_GET['keyword'] : "",
            'searchType'            =>isset($_GET['type']) ? $_GET['type'] :""
//            'selectType'=>
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
        if($data['apply_type'] == 0){
            //修改C端用户支付状态
            if($data['type'] == 1){
                //通过
                $status = 4;
            }else if($data['type'] == 0){
                //未通过
                $status = 9;
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
            $s = DB::table('user_apply')->where([
                'id'=>$data['id']
            ])->update($update);
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
            }
            $update = [
                'b_apply_status'=>$status,
                'c_apply_status'=>$cstatus
            ];
            $s = DB::table('user_apply')->where([
                'id'=>$data['id']
            ])->update($update);
        }


        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }

}
