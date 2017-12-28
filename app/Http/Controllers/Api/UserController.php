<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:38
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\BusinessUser;
use App\Model\Config;
use App\Model\Logs;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class UserController extends Controller {
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 我的如易用户信息
     * param:user_id-用户id
     */

    public function UserInfo(Request $request){
        $userData = $request->except('s');

        $data = User::where(['id'=>$userData['user_id']])
            ->first();

        $j = returnData($data);

        return response()->json($j);
    }




    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存用户信息
     */

    public function UserSave(Request $request){
        $SaveData = $request->except('s');

        if(!empty($SaveData['user_pic'])){

            $user_pic = $request->file('user_pic')->store('imgs','img');
            $s = new Logs();
            $s->logs("所有数据",$SaveData);
        }
        unset($SaveData['user_pic']);
        $SaveData['user_pic'] = empty($user_pic) ? DB::table('user')->where(['id'=>$SaveData['user_id']])->value('user_pic'):'/uploads/'.$user_pic;

        $id = $SaveData['user_id'];
        unset($SaveData['user_id']);

        // 如果传了身份证信息则进行查询
        if(!empty($SaveData['user_idcard'])){
            $SaveData['belonging'] = IdBelonging($SaveData['user_idcard']);
        }
        $s = DB::table('user')->where(['id'=>$id])->update($SaveData);

        if($s){
            $retJson['code'] = 200;
            $retJson['msg'] = "数据保存成功";
        }else{
            $retJson['code'] = 404;
            $retJson['msg'] = "数据未更新";
        }

        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 我的申请
     * param:user_id :用户id  type:申请类型  0->待支付  1->待确认 2->待放款 3->放款成功
     */

    public function UserApply(Request $request){

        $userData = $request->except('s');
        if($userData['applicantType'] == 1){
            $userData['user_id'] = $userData['business_id'];
        }

//        $Log = new Logs();
//        $Log->logs('分类id',$userData);
        switch ($userData['type']){
            case 0:
                $apply_status = [0,2,9];
                $b_apply_status = [0];
                break;
            case 1:
                $apply_status = [1,3,4,5];
                $b_apply_status = [0,1,5];
                break;
            case 2:
                $apply_status = [4,5,6,7];
                $b_apply_status = [2,3,4,9,8];
                break;
            default:
                $apply_status = [8];
                $b_apply_status = [6,7];
        }

        $data = DB::table('user_apply')
            ->where([
                ['user_apply.user_id','=',$userData['user_id']],
                ['user_apply.equipment_type','=',$userData['applicantType']],
            ])
            ->whereIn('user_apply.c_apply_status',$apply_status)
            ->whereIn('user_apply.b_apply_status',$b_apply_status)
            ->join('product','user_apply.product_id','=','product.id')
            ->join('product_cat','product.cat_id','=','product_cat.id')
            ->join('business_user','product.business_id','=','business_user.id')
            ->orderBy('user_apply.create_time','desc')
            ->select('product.cat_id','user_apply.order_count','product_cat.cat_name','product.content','user_apply.c_apply_status','user_apply.create_time','user_apply.order_id','business_user.number','user_apply.c_is_evaluate')
            ->get();

        $title = ['cat_id','other_need','audit_time','order_count','area','property','accrual','lending_cycle','other','lending_type','is_home','company','product_id','order_id','cat_name','create_time','c_is_evaluate','c_apply_status'];

        $j = productData($data,$title);

        return response()->json($j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 待评价
     * param:user_id->用户id
     */
    public function Evaluate(Request $request){

        $eData = $request->except('s');
        $data = DB::table('user_apply')
                    ->where([
                        ['user_id','=',$eData['user_id']],
                        ['user_apply.c_apply_status','=','8'],
                        ['user_apply.c_is_evaluate','=','0'],
                        ['user_apply.equipment_type','=','0']
                    ])
                    ->join('product','user_apply.product_id','=','product.id')
                    ->join('product_cat','product.cat_id','=','product_cat.id')
                    ->join('business_user','product.business_id','=','business_user.id')
                    ->select('product.cat_id','user_apply.order_count','business_user.number','user_apply.id','user_apply.c_apply_status','product.id as product_id','product_cat.cat_name','user_apply.create_time','product.content','business_user.companyName','user_apply.order_id','user_apply.c_is_evaluate')
                    ->get();

        $title = ['order_count','area','property','accrual','lending_cycle','is_home','company','product_id','order_id','cat_name','create_time','c_is_evaluate','lending_type','other','c_apply_status'];
        $j =  productData($data,$title);
        return response()->json($j);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 常见问题
     */

    public function QA(Request $request){
        $data = DB::table('question')->select('title','content')->where('type',$request->input('type'))->get();
//        $data = DB::table('question')->select('title','content')->get();

        $j = returnData($data);

        return response()->json($j);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 客服电话
     */

    public function ServicePhone(){
        $data = DB::table('service_phone')->value('phone');

        $j = returnData($data);

        return response()->json($j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 反馈
     * param: content->反馈内容  user_id->用户Id
     */

    public function Feedback(Request $request){
        $FeedbackData = $request->except('s');
        $FeedbackData['customer_type'] = 0;
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 获取不同类型的文章
     * param: type：0->服务协议 1->反馈页面提示 2->关于我们 废弃
     */

    public function Atricle(Request $request){
        $Atricle = $request->except('s');
        $ss = new Logs();
        $ss->logs('服务协议',$Atricle);
        $data = DB::table('article')->where($Atricle)->first();

        $j = returnData($data);

        return response()->json($j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 更改申请状态
     * param: c_apply_status : 2->取消申请 5->申请退款  order_id:订单id
     */

    public function ChangeApply(Request $request){
       $ChangeData = $request->except('s');
       $map['order_id']  = $ChangeData['order_id'];
       unset($ChangeData['order_id']);
       if($ChangeData['c_apply_status'] == 2){
           // 取消订单查看订单是否超过 7天
           $time = UserApply::where($map)->value('create_time');
           if((time() - $time) >= 60*60*24*7){
               // 超过7天的订单不能取消
               $retJson['code'] = 404;
               $retJson['msg']  = '订单时效超过7天不能取消';
           }
       }else{
           $s = DB::table('user_apply')->where($map)->update($ChangeData);
           if($s){
               $retJson['code'] = 200;
               $retJson['msg']  = '状态更改成功';
           }else{
               $retJson['code'] = 404;
               $retJson['msg']  = '状态更改失败';
           }
       }
       return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 保存评价数据
     * param: order_id->完成放款的申请order_id   content->评价内容 score->分数
     */

    public function SaveEvaluate(Request $request){
        $EvaluateData                       = $request->except('s');
        $s = new Logs();
        $s->logs('保存评价数据',$EvaluateData);
        $retJson = SaveEvaluate($EvaluateData,0);
        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 查看用户评价
     */

    public function GetEvaluate(Request $request){
        $OrderId = $request->except(['s']);
        $EvaData = DB::table('product_evaluate')->select('score','content','desc')->where($OrderId)->first();
        $retJson = returnData($EvaData);
        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 账单
     * param:user_id 用户id
     */

    public function PayList(Request $request){

        $PayData = $request->except('s');
        $data = DB::table('pay_list')->where($PayData)->get();
        $weekday = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');

        foreach($data as $k=>$v){
            $week = $weekday[date('w', $v->create_time)];
            $v->week[0] = $week;
            $v->week[1] = date('m-d',$v->create_time);
        }

        $j = returnData($data);
        return response()->json($j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 如意积分
     */

    public function Integral(Request $request){
        $userId = $request->except(['s']);
        $table = 'user';
        $Inlist = [];
        $ruyi = "";
        $syDay = 0;
        if($userId['equipment_type'] == 1){
            $userId['user_id'] = $userId['business_id'];
            $table = 'business_user';

            $ruyi = DB::table('integral_change')->value('need');
            //积分购买
            $Inlist = DB::table('integral_list')->where([
                'user_id'=>$userId['user_id'],
                'user_type'=>$userId['equipment_type'],
                'type'  => 6
            ])->select('integral','type','create_time')->orderBy('create_time','desc')->get();

//            $userId['user_id'] = $userId['business_id'];

            // 查询用户是否购买金币排名保护

            $isBuy = DB::table('business_user')->where('id',$userId['user_id'])->select('is_buy','start_time','stop_time')->first();


            if(isset($isBuy->is_buy) && ($isBuy->is_buy == 1)){
                //购买了 排名保护 进行 剩余天数的计算
                if($isBuy->stop_time > time()){
                    $syDay = ceil(($isBuy->stop_time - time()) / 60 /60/ 24);
                }else{
                    $syDay = 0;
                }
            }

            if(!($Inlist->isEmpty())){
                foreach ($Inlist as $v){
                    switch ($v->type){
                        case 0:
                            $v->degree = 1;
                            $v->typeMsg = "注册成功";
                            break;
                        case 1:
                            $v->degree = 1;
                            $v->typeMsg = "邀请他人注册成功";
                            break;
                        case 2:
                            $v->degree = 1;
                            $v->typeMsg = "借款成功";
                            break;
                        case 3:
                            $v->degree = 1;
                            $v->typeMsg = "还款无违约";
                            break;
                        case 4:
                            $v->degree = 1;
                            $v->typeMsg = "还款完成";
                            break;
                        case 5:
                            $v->degree = 0;
                            $v->typeMsg = "支出消耗";
                            break;
                        default:
                            $v->degree = 0;
                            $v->typeMsg = "购买排名保护";
                            break;
                    }
                    $v->create_time = date("Y-m-d",strtotime($v->create_time));
                }
            }
        }

        //积分总数
        $count = DB::table($table)->where(['id'=>$userId['user_id']])->value('integral');

        //积分获取
        $list = DB::table('integral_list')->where([
            'user_id'=>$userId['user_id'],
            'user_type'=>$userId['equipment_type']
        ])->where('type','<','6')->select('integral','type','create_time')->orderBy('create_time','desc')->get();
//        dd($list);
        if(!($list->isEmpty())){
            foreach ($list as $v){
                switch ($v->type){
                    case 0:
                        $v->degree = 1;
                        $v->typeMsg = "注册成功";
                        break;
                    case 1:
                        $v->degree = 1;
                        $v->typeMsg = "邀请他人注册成功";
                        break;
                    case 2:
                        $v->degree = 1;
                        $v->typeMsg = "借款成功";
                        break;
                    case 3:
                        $v->degree = 1;
                        $v->typeMsg = "还款无违约";
                        break;
                    case 4:
                        $v->degree = 1;
                        $v->typeMsg = "还款完成";
                        break;
                    case 5:
                        $v->degree = 0;
                        $v->typeMsg = "支出消耗";
                        break;
                    default:
                        $v->degree = 0;
                        $v->typeMsg = "购买金币保护";
                        break;
                }
                $v->create_time = date("Y-m-d",strtotime($v->create_time));
            }
        }

        $data = [
            'count' =>$count,
            'list'  =>$list,
            'inList'=>$Inlist,
            'ruyi'  =>$ruyi,
            'syDay' =>$syDay
        ];

        $retData = returnData($data);
        return response()->json($retData);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * author hongwenyang
     * method description : 安卓apk B端
     */
    public function Apk(){
        $data = DB::table('config')->where([
            'key'=>'apkb'
        ])->value('value');

        return redirect($data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * author hongwenyang
     * method description : 安卓apk C端
     */
    public function Apkc(){
        $data = DB::table('config')->where([
            'key'=>'apkc'
        ])->value('value');

        return redirect($data);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : ios进入app后判断是否为最新版本
     */
    public function ApkInformation(Request $request){
        $data = $request->except(['s']);
        if($data['type']){
            //B端
            $key = 'iosB';
        }else{
            //C端
            $key = 'iosC';
        }
        //需要强制更新时跳转的地址
        $GetNewHref = Config::where('key',$key)->value('href');
        $isNew = Config::where([
            'value'=>$data['version'],
            'key'=>$key
        ])->get();
        if($isNew->isEmpty()){
            // 不是最新版本需要进行更新
            $code = 201;
            $msg = "当前版本不是最新";
            $href = $GetNewHref;
        }else{
            // 当前版本最新
            $code = 200;
            $msg = "当前版本为最新版本";
            $href = "";
        }
        $j = [
            'code'=>$code,
            'msg'=>$msg,
            'href'=>$href
        ];

        return response()->json($j);
    }

    public function HasInformation(Request $request){
        $id = $request->except(['s']);
        if($id['type']){
            //B端
            $table = 'business_user';
            $title =  'companyName';
            $user_id = $id['business_id'];
        }else{
            //C端
            $table = 'user';
            $title = 'user_name';
            $user_id = $id['user_id'];
        }

        $data = DB::table($table)->where('id',$user_id)->value($title);
        if(empty($data)){
            $code = 211;
            $msg = "用户暂未完善资料";
        }else{
            $code = 200;
            $msg = "用户已完善资料";
        }

        $j = [
            'code'=>$code,
            'msg'=>$msg
        ];

        return response()->json($j);
    }
}