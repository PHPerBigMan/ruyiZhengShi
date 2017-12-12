<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:38
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Apply;
use App\Model\ApplyBasic;
use App\Model\ApplyForm;
use App\Model\BusinessUser;
use App\Model\DemandProperty;
use App\Model\IntegralList;
use App\Model\LianLian;
use App\Model\Logs;
use App\Model\OrderApplyForm;
use App\Model\Product;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplyController extends Controller {
    protected $model= "";
    protected $apply= "";
    protected $userApply= "";
    public function __construct()
    {
        $this->model = new ApplyBasic();
        $this->apply = new ApplyForm();
        $this->userApply = new Apply();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存匹配申请资料  (
     * 根据 request获取或者保存数据
     */

    public function apply(Request $request){
        $ApplyData = $request->except(['s']);
//        $s = new Logs();
//        $s->logs('查看基础信息',$ApplyData);
        //保存基础资料  （除企业贷)）
        $s = new Logs();
        if($ApplyData['request'] == 1){
            if(empty($ApplyData['name'])){
                $retJson = $this->model->SearchApplyData($ApplyData,1);
            }else{
                $retJson =  $this->model->applyBasic($ApplyData);
            }
        }else if($ApplyData['request'] == 2){

           if(empty($ApplyData['lending_type'])){
               $retJson = $this->model->SearchApplyData($ApplyData,2);
           }else{
               $s->logs("需求品资料保存",$ApplyData);
               $retJson =  $this->apply->applyData($ApplyData);
           }

        }else{
            if(empty($ApplyData['area'])){
                $retJson = $this->model->SearchApplyData($ApplyData,3);
            }else{
//                dd(1);
                $retJson = $this->apply->apply($ApplyData,0);
            }
        }

        return response()->json($retJson);
    }


    public function CompanyApply(Request $request){
        $ApplyData = $request->except(['s']);

        //保存企业贷企业用户基础s资料
        if($ApplyData['request'] == 1){
            if(empty($ApplyData['companyName'])){
                $retJson = $this->model->SearchCompanyData($ApplyData,1);
            }else{
                $retJson =  $this->model->applCompanyBasic($ApplyData);
            }
        }else if($ApplyData['request'] == 2){
            if(empty($ApplyData['money'])){
                $retJson = $this->model->SearchApplyData($ApplyData,2);
            }else{
                $retJson =  $this->apply->applyData($ApplyData);
            }

        }else{

            if(empty($ApplyData['area'])){
                $retJson = $this->model->SearchApplyData($ApplyData,3);
            }else{

                $retJson = $this->apply->apply($ApplyData,0);
            }
        }

        return response()->json($retJson);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 对匹配后的产品数据根据字段进行排序
     */

    public function Sort(Request $request){
        $SortData = $request->except(['s']);

        $retData = $this->apply->apply($SortData,1);

        return response()->json($retData);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 申请详情
     * param: id-申请数据id
     */

    public function applyData(Request $request){
        $ApplyId = $request->except(['s']);
        $ApplyData = DB::table('apply_form')
            ->join('user_apply','apply_form.user_id','=','user_apply.user_id')
            ->where("apply_form.id","=",$ApplyId['id'])
            ->select('apply_form.data','apply_form.id','user_apply.c_apply_status')
            ->first();
        $ApplyData->data = json_decode($ApplyData->data,true);
        $ApplyData->data['c_apply_status'] = $ApplyData->c_apply_status;
        $ApplyData->data['id'] = $ApplyData->id;
        unset($ApplyData->c_apply_status);
        unset($ApplyData->id);
        if($ApplyData){
            $retJson['code'] = 200;
            $retJson['data'] = $ApplyData->data;
        }

        return response()->json($retJson);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 提交申请
     */

    public function SaveApplyData(Request $request){
        $SaveData = $request->except(['s']);
//        $a = new Logs();
//        $a->logs('提交申请',$SaveData);
        $s = $this->userApply->SaveUserApply($SaveData);

        $retJson['code'] = $s['code'];
        $retJson['msg']  = $s['msg'];
        if($s['code'] == 200){
            $retJson['data'] = $s['order_id'];
        }
        if($s['code'] == 400){
            $retJson['data']        = $s['data'];
            $retJson['match_score'] = $s['match_score'];
        }
        if($s['code'] == 404){
            $retJson['data'] = "";
        }

        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 产品匹配分数不达标后继续提交申请
     */

    public function AbnormalSave(Request $request){
        $data = $request->except(['s']);
        $s = $this->userApply->SaveUserApply($data,1);
        $retJson['code'] = $s['code'];
        $retJson['msg']  = $s['msg'];
        $retJson['data']  = $s['order_id'];
        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取用户积分
     */

    public function GetIntegral(Request $request){

        $UserId = $request->except(['s']);
//        dd($UserId);
        $table = 'user';
        if(isset($UserId['equipment_type']) && $UserId['equipment_type'] == 1){
            $table = 'business_user';
            $map['id']= $UserId['business_id'];
        }else{
            $map['id'] = $UserId['user_id'];
        }
        $Integral = DB::table($table)->where($map)->value('integral');
//        dd($Integral);
        $retJson = returnIntegral($Integral);
        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 返回订单信息
     */

    public function ReutrnOrderInform(Request $request){
        $Order = $request->except(['s']);

        $orderData = DB::table('user_apply')
                        ->join('product','product.id','=','user_apply.product_id')
                        ->join('product_cat','product_cat.id','=','product.cat_id')
                        ->join('business_user','business_user.id','=','product.business_id')
                        ->join('apply_form','apply_form.user_id','=','user_apply.user_id')
                        ->select('user_apply.order_id','product.content','product_cat.cat_name','business_user.number','apply_form.need_data')
                        ->where([
                            'user_apply.order_id'=>$Order['order_id'],
                            'apply_form.equipment_type'=>$Order['applicantType']
                        ])->first();
//        dd($Order['order_id']);
        //获取用户下单时的数据
        $orderForm = OrderApplyForm::where('order_id',$Order['order_id'])->first();
        $need_data = json_decode($orderForm->need_data,true);
        $content1   = json_decode($orderForm->data,true);
        $content2   = json_decode($orderData->content,true);
        $content   = array_merge($content1,$need_data);
        $use_title = ['lending_type','property','accrual','lending_cycle','is_home','is_home','company','matching','score','id','count','rate','order_id','other','other_need','audit_time'];
//        dd($content);
        foreach ($content as $k=>$v){
            if(!in_array($k,$use_title)){
                unset($content[$k]);
            }
        }

        // 服务费率(金币抵用前)
        $rate               = DB::table('rate')->value('rate');

        // 获取金币数据

        $serverMoney        = $need_data['money'] * $rate * 10000;
        if(isset($Order['Icon'])){
            //如果存在金币 修改服务费用
            // 如果存在的话 跳转到下单页面后就扣除对应的金币数额
            // 在 user_apply 中记录金币的使用数量
            UserApply::where('order_id',$Order['order_id'])->update([
                'isIcon'=>$Order['Icon']
            ]);

            if($serverMoney > $Order['Icon']){
                $serverMoney -= $Order['Icon'];
            }else{
                $serverMoney = 0;
            }
        }
        $retData            = $content;
        $retData['CountMoney']   = $serverMoney;

        // 服务费暂时改为 0   hongwenyang
//        $retData['CountMoney']   = 0;
        $retData['CountMoney'] = 0.01; // TODO 先把所有订单的金额改成 0.01元
        $retData['cat_name']   = $orderData->cat_name;
        $retData['company']   = $orderData->number;
        $retData['order_id']   = $Order['order_id'];
        //获取收款银行的信息
        if($Order['paytype'] != ""){
            $Bank = DB::table('bank')->where([
                'type'=>$Order['paytype']
            ])->select('bank','name','account')->first();
            $retJson['bank']  = $Bank;
            if($Order['paytype'] == 2){
                $retJson['bank']->account = $Bank->account;
            }else{
                $retJson['bank']->account = URL.$Bank->account;
            }
        }
        $retJson['data']  = $retData;
        $retJson['code']  = 200;
        $retJson['msg']   = "获取数据成功";

        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取拒单理由
     */

    public function Reason(Request $request){
        $ReasonData = $request->except(['s']);
        $reason = DB::table('user_apply')->where(['order_id'=>$ReasonData['order_id']])->select('reason','product_id','c_apply_status','c_is_evaluate')->first();
        $product_data = read($reason->product_id);

        $retData            = $product_data->content;
        $retData->cat_name  = $product_data->cat_name;
        $retData->company    = $product_data->number;
        $retData->c_apply_status    = $reason->c_apply_status;
        $retData->c_is_evaluate    = $reason->c_is_evaluate;
        $retData->reason    = $reason->reason;

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
     * method description :  银联
     */
    public function YinlianPay(Request $request){
        $OrderId = $request->except(['s']);
        //处理上传凭证图片

        $save = $request->file('imgs');
        foreach($save as $k=>$v){
            $imgs[$k] = '/uploads/'.$v->store('img','img');
        }


        unset($OrderId['imgs']);
        unset($OrderId['Icon']);
        $OrderId['img'] = json_encode($imgs);

        //如果 C端用户未支付 修改 c_apply_status  如果  C端用户已支付 修改 b_apply_status
        if(DB::table('user_apply')->where(['order_id'=>$OrderId['order_id']])->value('c_apply_status') == 0){
            $update['c_apply_status'] = 1;
        }else{
            $update['b_apply_status'] = 3;
        }
//        unset($OrderId['type']);
        //保存数据

        $s = DB::table('yinlian')->insert($OrderId);

        // 记录b端服务费
        $orderCount = DB::table('user_apply')->where('order_id',$OrderId['order_id'])->value('order_count');
        $rate               = DB::table('rate')->value('rate');
//        $rate               = DB::table('rate')->where([
//            'type'=>$Order['paytype']
//        ])->value('rate');
        $serverMoney        = $orderCount * $rate * 10000;
        //是否使用金币抵扣服务费
        $Icon = UserApply::where('order_id',$OrderId['order_id'])->value('isIcon');
        if($Icon){
            $serverMoney = $serverMoney - $Icon;
            $update['isIcon'] = $Icon;
            // 获取用户id
            if(!$OrderId['type']){
                $user_id = UserApply::where('order_id',$OrderId['order_id'])->value('user_id');
                // 修改user 表的用户金币
                DB::table('user')->where('id',$user_id)->decrement('integral',$Icon);
                // 增加金币使用列表
                $user_type = 0;
                $type = 5;
                $integraling = User::where('id',$user_id)->value('integral');
            }else{
                $user_id = Product::where('id',UserApply::where('order_id',$OrderId['order_id'])->value('product_id'))->value('business_id');
                // 修改 business_user 表中的用户金币
                DB::table('business_user')->where('id',$user_id)->decrement('integral',$Icon);
                $user_type = 1;
                $type = 5;
                $integraling = BusinessUser::where('id',$user_id)->value('integral');
            }

            IntegralList::insert([
                'user_id'=>$user_id,
                'integral'=>$Icon,
                'integraling'=>$integraling,
                'type'=>$type,
                'user_type'=>$user_type
            ]);
        }
        if($OrderId['type'] == 0){
            // C端服务费
            $update['c_serve'] = $serverMoney;

        }else{
            // B端服务费
            $update['b_serve'] = $serverMoney;
        }

        //修改订单状态
        DB::table('user_apply')->where(['order_id'=>$OrderId['order_id']])->update($update);

        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * author hongwenyang
     * method description :
     */

    public function isShen(Request $request){
        $type = $request->input('type');
        if(isset($type)){
            $code = 200;
            // 如果审核过  显示 审核通过
            $msg  = "c端审核通过" ;
        }else{
            $code = 200;
            // 如果审核过  显示 审核通过
            $msg  = "b端审核通过" ;
        }

        $j = [
            'code'=>$code,
            'msg'=>$msg
        ];

        return response($j);
    }

    /**
     * author hongwenyang
     * method description : 连连科技
     */

    public function Lian(){
        $data = LianLian::first();

        $retData = [
            'code'=>200,
            'msg'=>"获取成功",
            'private'=>$data->private
        ];

        return response()->json($retData);
    }


}