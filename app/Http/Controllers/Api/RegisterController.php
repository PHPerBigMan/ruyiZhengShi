<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:38
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\BusinessChild;
use App\Model\BusinessUser;
use App\Model\Logs;
use App\Model\Product;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Overtrue\EasySms\EasySms;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegisterController extends Controller {



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 用户注册
     * param: phone-手机号  code-验证码 password-密码 ycode-邀请码 type 0-注册 1-找回密码  找回密码时删除  userType 0-C端用户  1-B端用户
     */

    public function register(Request $request){
        $registerData = $request->except('s');
        //注册用户类型
        $userType = $registerData['userType'];
        //类型
        $type = $registerData['type'];
        unset($registerData['userType']);
        unset($registerData['type']);

        if($registerData['code'] == session('code')){
            if($type == 0){
                if($userType == 0){
                    $table = 'user';
                    $message = [4,10];
                }else if($userType == 1){
                    $table = 'business_user';
                    //企业编号
                    $num = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                    $registerData['number'] = $num;
                    $message = [5,11];
                }



                $isRegistered = DB::table($table)->where(['phone'=>$registerData['phone']])->first();
                if($isRegistered){
                    $retJson['code'] = 402;
                    $retJson['msg']  = '用户已注册';
                }else{
                    $registerData['password']   = sha1($registerData['password']);
                    unset($registerData['code']);
                    $user_id = DB::table($table)->insertGetId($registerData);


                    if($userType == 0){
                        $name = 'user_id';
                    }else if($userType == 1){
                        $name = 'business_id';
                    }
                    $request->session()->put($name,$user_id);
                    //生成 二维码
                    $imgName = $user_id.'-'.$userType.'.png';
                    QrCode::format('png')->size(200)->generate(URL.'register/'.$user_id.'/'.$userType,public_path('qrcodes/'.$imgName));
                    DB::table($table)->where([
                        'id'=>$user_id
                    ])->update([
                        'qrcode'=>'/qrcodes/'.$imgName
                    ]);
                    //获取注册成功模板消息
                    $Message = DB::table('article')->whereIn('type',$message)->get();
                    //注册成功发送消息
                    foreach($Message as $k=>$v){
                       if($message[$k] == 4 || ($message[$k] == 5)){
                           $messageType = 2;
                           $img = '/uploads/messageImg/welcome.jpg';
                       }else{
                           $messageType = 0;
                           $img = "";
                       }
                        DB::table('message')->insert([
                            'user_id'=>$user_id,
                            'equipment_type'=>$userType,
                            'title'=>$v->title,
                            'content'=>$v->content,
                            'type'=>$messageType,
                            'img'=>$img
                        ]);
                    }

                    // 增加金币变化数据
                    DB::table('integral_list')->insert([
                        'user_id'=>$user_id,
                        'integral'=>100,
                        'integraling'=>100,
                        'type'=>0,
                        'user_type'=>$userType
                    ]);

                    $retJson['code'] = 200;
                    $retJson['msg']  = '注册成功';
                    $retJson[$name]  = $user_id;
                }
            }else{
                if($userType == 0){
                    $table = 'user';
                    $name = 'user_id';
                }else if($userType == 1){
                    $table = 'business_user';
                    $name = 'business_id';
                }


                $s = DB::table($table)->where(['phone'=>$registerData['phone']])->update([
                    'password'=>sha1($registerData['password'])
                ]);

                $request->session()->put($name,DB::table($table)->where('phone',$registerData['phone'])->value('id'));
                if($s){
                    $retJson['code'] = 200;
                    $retJson['msg']  = '密码修改成功';
                    $retJson[$name]  = DB::table($table)->where('phone',$registerData['phone'])->value('id');
                }else{
                    $retJson['code'] = 404;
                    $retJson['msg']  = '密码没有更改';
                }

            }
        }else{
            $retJson['code']    = 401;
            $retJson['msg']     = "验证码不正确";
        }

        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * author hongwenyang
     * method description : 获取手机验证码
     * param: phone-手机号
     */

    public function code(Request $request){
        $getCodeData = $request->input('phone');

        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun','alidayu'
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                ],
                'aliyun' => [
                    'access_key_id' => 'LTAIqXR1WkCTxhKv',
                    'access_key_secret' => 'pvoa37DlwyCWLNSN8aH026vg8KcHe9',
                    'sign_name' => '如易金服',
                ],
            ],
        ];
        $code = rand(1000,9999);
        $easySms = new EasySms($config);

        try{
            $result = $easySms->send($getCodeData,[
                'content'=>'验证码'.$code.'，请勿泄露于他人，如非本人操作，建议及时修改账号和密码',
                'template' => 'SMS_106465033',
                'data' => [
                    'code' => $code
                ],
            ]);

            if($result['aliyun']['status'] == 'success'){
                session(['code'=>$code]);
                $retJson['code'] = 200;
                $retJson['msg']  = "短信发送成功";
            }else{
                $retJson['code'] = 403;
                $retJson['msg']  = "短信发送失败";
            }
        }catch (\Exception $exception){

                $retJson['code'] = 403;
                $retJson['msg']  = "短信发送异常";
        }

        return response()->json($retJson);
    }


    public function ChangePhone(Request $request){
        $changeData = $request->except('s');

    }




    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 登录接口
     * param:phone-手机号  password-密码 userType- 登录的类型 0：C端用户 1:B端用户
     */

    public function login(Request $request){
        $loginData = $request->except('s');
        $loginData['password'] = sha1($loginData['password']);
        if($loginData['userType'] == 0){
           $table = 'user';
            $isUser = DB::table($table)->where(['phone'=>$loginData['phone']])->first();
        }else{
           $table  = 'business_user';
            $isUser = DB::table($table)->where(['phone'=>$loginData['phone']])->orWhere(['companyCode'=>$loginData['phone']])->first();
            // TODO:: 这里多加一个判断  判断是否是子账号
            if(empty($isUser)){
                $isChild = BusinessChild::where('name',$loginData['phone'])->first();

                if(!empty($isChild)){
                    // 是子账号
                    $isUser = json_decode('{}');
                    $isUser->id = $isChild->p_id;
                    $isUser->password = $isChild->password;
                    $isUser->is_tui = 0;
                }
            }
        }

        if(empty($isUser)){
            $retJson['code']  = 401;
            $retJson['msg']   = "用户未注册";
        }else{
            if($isUser->password == $loginData['password']){
                if($loginData['userType'] == 0){
                    $name = 'user_id';
                    $pre = 'bmf-ruyijingfu-c';
                }else{
                    $name = 'business_id';
                    $pre = 'bmf-ruyijingfu-b';
                }
                $request->session()->put($name,$isUser->id);
                //判断用户是否是推荐注册用户
                if($isUser->is_tui == 1 && $isUser->is_add == 0){
                    //修改推荐用户的 is_add 状态
                    DB::table($table)->where([
                        'id'=>$isUser->id,
                    ])->update([
                        'is_add'=>1
                    ]);

                    //推荐用户 给推荐者增加 5金币
                    DB::table($table)->where([
                        'id'=>$isUser->tuiUserId
                    ])->increment('integral',5);
                    // 增加金币变化数据
                    DB::table('integral_list')->insert([
                        'user_id'=>$isUser->tuiUserId,
                        'integral'=>5,
                        'integraling'=>DB::table($table)->where([
                            'id'=>$isUser->tuiUserId
                        ])->value('integral'),
                        'type'=>1,
                        'user_type'=>$loginData['userType']
                    ]);
                    //增加一次浏览的次数
                    DB::table('user')->where([
                        'id'=>$isUser->tuiUserId
                    ])->increment('view_count');
                }

                // 判断用户是否完善资料
                $HasInformation = DB::table($table)->where('id',$isUser->id)->first();
                if($loginData['userType'] == 0){
                    //判断B端用户是否完善资料
                    $isMoreInformation = empty($HasInformation->companyName)  ? 0 : 1;
                }else{
                    $isMoreInformation = empty($HasInformation->user_name) ? 0 : 1;
                }
                $retJson['code']  = 200;
                $retJson['msg']   = "登录成功";
                $retJson[$name]   = $request->session()->get($name);
            }else{
                $retJson['code']  = 404;
                $retJson['msg']   = "密码有误";
            }
        }

        return response()->json($retJson);
    }

    public function demo(){
        $data = BusinessUser::where('idcard','!=',' ')->pluck('id','idcard');
        foreach ($data as $k=>$v){
            $result[$v] = IdBelonging($k);
//            $s = User::where('id',$v)->update([
//                'belonging'=>$result
//            ]);
        }
        dd($data);
    }
}