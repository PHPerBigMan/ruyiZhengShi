<?php

namespace App\Http\Controllers\Bapi;

use App\Model\BusinessUser;
use App\Model\IntegralChange;
use App\Model\Logs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class UserController extends Controller
{
    protected $model = '';
    public function __construct()
    {
        $this->model = new BusinessUser();
    }

    /**
     * @param Request $request  business_id 用户Id
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description :  获取用户基本信息
     */
    public function UserInfo(Request $request){
        $userData = $request->except('s');
        $data = BusinessUser::where(['id'=>$userData['business_id']])
            ->first();
//        dd($data);
        if(empty($data->imgs)){
            $data->imgs = [];
        }else{
            $data->imgs = json_decode($data->imgs,true);
        }
        $j = returnData($data);
        return response()->json($j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 待评价
     */

    public function Evaluate(Request $request){
        $eData = $request->except('s');
        $data = DB::table('user_apply')
            ->where([
                ['user_id','=',$eData['business_id']],
                ['user_apply.c_apply_status','=','8'],
                ['user_apply.c_is_evaluate','=',$eData['type']],
                ['user_apply.equipment_type','=','1']
            ])
            ->join('product','user_apply.product_id','=','product.id')
            ->join('product_cat','product.cat_id','=','product_cat.id')
            ->join('business_user','product.business_id','=','business_user.id')
            ->select('product.cat_id','user_apply.order_count','user_apply.id','user_apply.c_apply_status','product.id as product_id','product_cat.cat_name','user_apply.create_time','product.content','business_user.companyName','user_apply.order_id','user_apply.c_is_evaluate','business_user.number')
            ->get();

        $title = ['area','property','accrual','lending_cycle','is_home','company','product_id','order_id','cat_name','create_time','c_is_evaluate','lending_type','other','c_apply_status'];

//        dd($data);
        $j =  productData($data,$title);
        return response()->json($j);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存企业信息
     */
    public function SaveInfo(Request $request){
        $SaveData = $request->except(['s']);

        $map['id'] = $SaveData['business_id'];
        unset($SaveData['business_id']);
        if(!empty($SaveData['picA'])){
            $SaveData['pic']  = '/uploads/'.$request->file('picA')->store('img','img');

            unset($SaveData['picA']);
        }
        //在企业有金融资质的情况下
        if(isset($SaveData['imgs'])){
            $save = $request->file('imgs');
            foreach($save as $k=>$v){
                $imgs[$k] = '/uploads/'.$v->store('img','img');
            }
            $SaveData['imgs'] = json_encode($imgs);
        }

        if(empty(DB::table('business_user')->where($map)->value('number'))){
            $SaveData['number'] = 'R'.$map['id'];
        }
        // 如果状态为0   状态改为审核中
        if(!BusinessUser::where($map)->value('is_pass')){
            $SaveData['is_pass'] = 2;
        }

        $s = DB::table('business_user')->where($map)->update($SaveData);

        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = "保存成功";
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = "数据没有改变";
        }

        return response()->json($retJson);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 经营数据
     */

    public function ManagementList(Request $request){
        $ManagementData = $request->except(['s']);

        $data = $this->model->DataList($ManagementData);

        $retData = returnData($data);

        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 财务对账
     */

    public function MoneyList(Request $request){
        $MoneyData = $request->input('business_id');
        $data = $this->model->MoneyList($MoneyData);

        $retData = returnData($data);
        return response()->json($retData);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 账号安全 修改密码  匹配原密码
     */

    public function PasswordCheck(Request $request){
        $bData = $request->except(['s']);
        $data  = DB::table('business_user')->where([
            'id'=>$bData['business_id'],
            'password'=>sha1($bData['password'])
        ])->first();

        if($bData['type'] == 0){
            if($data){
                $retJson['code'] = 200;
                $retJson['msg'] = "原密码输入正确";
            }else{
                $retJson['code'] = 403;
                $retJson['msg'] = "密码不正确";
            }
        }else{
            $s = DB::table('business_user')->where([
                'id'=>$bData['business_id']
            ])->update([
                'password'=>sha1($bData['password'])
            ]);
            if($s){
                $retJson['code'] = 200;
                $retJson['msg']  = "密码修改成功";
            }else{
                $retJson['code'] = 403;
                $retJson['msg']  = "新旧密码一致";
            }
        }
        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 购买积分保护
     */
    public function Protect(Request $request){
        $pData = $request->except(['s']);
//        dd(1);
        $UserIntegral = DB::table('business_user')->where([
            'id'=>$pData['business_id']
        ])->value('integral');
        $needintegral  = DB::table('integral_change')->value('need') * $pData['num'];
        // 查看保护名额的库存
        $stock = IntegralChange::where('id',1)->value('stock');
        if($stock <= 0){
            $retJson['msg']  = "保护名额库存不足";
            $retJson['code'] = 403;
        }
        else if($UserIntegral< $needintegral){
            $retJson['msg']  = "剩余金币不足";
            $retJson['code'] = 403;
        }else{
            // 获取排名保护所需积分

            $s = DB::table('integral_list')->insert([
                'user_id'       =>$pData['business_id'],
                'integral'      =>$needintegral,
                'integraling'   =>$UserIntegral - $needintegral,
                'type'          =>6,
                'user_type'     =>1
            ]);
            if($s){
                //减少对应的积分
                BusinessUser::where([
                    'id'                =>$pData['business_id'],
                ])->decrement('integral',$needintegral);

                // 如果没有进行排名保护的购买
                $isBuy = BusinessUser::where('id',$pData['business_id'])->value('is_buy');
                if(!$isBuy){
                    // 之前没有进行排名保护的购买  或者排名保护已经失效
                    BusinessUser::where('id',$pData['business_id'])->update([
                        'is_buy'            =>1,
                        'start_time'        =>time(),
                        'stop_time'         =>time() + $pData['num'] * (60*60*24)
                    ]);
                }else {
                    // 排名保护没有失效 进行时间的更换
                    BusinessUser::where('id',$pData['business_id'])->increment('stop_time',$pData['num'] * (60*60*24));
                }

                // 修改保护库存数据
                IntegralChange::where('id',1)->decrement('stock');
                $retJson['msg']  = "购买成功";
                $retJson['code'] = 200;
            }else{
                $retJson['msg']  = "购买失败";
                $retJson['code'] = 404;
            }
        }

        return response()->json($retJson);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存头像
     */

    public function SaveHeadImg(Request $request){
        $Img = $request->file('head_img')->store('img','img');

        $s = DB::table('business_user')->where([
            'id'=>$request->input('business_id')
        ])->update([
            'head_img'=>'/uploads/'.$Img
        ]);

        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }


    public function PingTai(){
        $data = DB::table('article')->where([
            'type'=>2
        ])->first();

        $retData = returnData($data);

        return response()->json($retData);
    }
}

