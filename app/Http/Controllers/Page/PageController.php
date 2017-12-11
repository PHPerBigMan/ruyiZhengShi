<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Model\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Overtrue\EasySms\EasySms;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PageController extends Controller
{
    //
    public function index($user_id,$type){
        $at = 6;
        if($type == 1){
            $at = 7;
        }
        $article = Article::where([
            'type'=>$at
        ])->first();
        return view('pages.yanzheng',compact('user_id','type','article'));
    }


    /**
     * @param Request $request
     * author hongwenyang
     * method description : 获取手机验证码
     * param: phone-手机号
     */

    public function code(Request $request)
    {
        if(Request::ajax()){
            $getCodeData = Request::input('phone');
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
        }else{
            $retJson['code'] = 405;
            $retJson['msg']  = "非法提交";
        }
        return response()->json($retJson);
    }

    public function codetest(){
        return response()->json(['code'=>200]);
    }


    public function register(Request $request){
        $registerData = Request::except(['s','_token']);

        //注册用户类型
        $userType = $registerData['userType'];
        //类型
        unset($registerData['userType']);
        unset($registerData['type']);

        if($registerData['code'] == session('code')){
            if($userType == 0){
                $table = 'user';
                $message = [4,10];
            }else if($userType == 1){
                $table = 'business_user';
                $num = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $registerData['number'] = $num;
                $message = [5,11];
            }

            $isRegistered = DB::table($table)->where(['phone'=>$registerData['phone']])->first();
            if($isRegistered){
                $retJson['code'] = 402;
                $retJson['msg']  = '该手机号已注册';
            }else{
                $registerData['password']   = sha1($registerData['password']);
                unset($registerData['code']);
                $registerData['is_tui'] = 1;
                $registerData['tuiUserId'] = $registerData['tuiUserId'];
                $user_id = DB::table($table)->insertGetId($registerData);
                if($userType == 0){
                    $name = 'user_id';
                }else if($userType == 1){
                    $name = 'business_id';

                }

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
            $retJson['code']    = 401;
            $retJson['msg']     = "验证码不正确";
        }

        return response()->json($retJson);
    }


    public function dowonloadpage(){

        return view('pages.dowonloadpage');
    }

    public function clientpage(){
        $datac = DB::table('config')->where([
            'key'=>'apkc'
        ])->value('value');

        return view('pages.clientpage',compact('datac'));
    }
}


