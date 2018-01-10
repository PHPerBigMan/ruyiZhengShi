<?php

namespace App\Http\Controllers\Api;

use App\Model\LianLian;
use App\Model\Logs;
use App\Model\UserApply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{


    // payInfo
    public function pay(Request $request){

        // 查询用户签约信息API地址
        $url = "https://queryapi.lianlianpay.com/bankcardbindlist.htm";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$request->input('payInfo'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        $result = curl_exec($ch);

        $resultArr = json_decode($result);

        if($resultArr['ret_code'] == 0000){
            // 进行数据比对b
        }else{
            // 查询不存在 用户需要支付订单
        }
        /**
         * 以下为测试时打开使用
         */
        //        $s = new Logs();
//        $s->logs_a('连连支付结果',$result);
       /* $getdata = [
            'offset'=>1,
            'oid_partner'=>trim(201711220001178629),
            'pay_type'=>trim("D"),
            'sign_type'=>trim("RSA"),
            'user_id'=> 6,
        ];
        $para = $this->buildRequestPara($getdata);*/
    }

    public function setSign(Request $request){
        $all=  $request->except(['s']);
        $para = $this->buildRequestPara($all);
        $s = new Logs();
        $s->logs('生成Ios的Sign',$para);
        if(empty($para['sign'])){
           $code = 404;
           $msg  = "生成签名错误";
           $data = "";
        }else{
            $code = 200;
            $msg = "生成成功";
            $data = $para['sign'];
        }
        $j = [
            'code'=>$code,
            'msg'=>$msg,
            'sign'=>$data
        ];
        return response($j);
    }
    /**
     * @param Request $request
     * @return Request|mixed
     * author hongwenyang
     * method description : 连连科技银行卡卡bin查询接口
     */

    public function cardBin(Request $request){
        $data = $request->input('card_all_data');
        $url = "https://queryapi.lianlianpay.com/bankcardbin.htm";
        $request = $this->curl($url,$data);
        return $request;
    }


    /**
     * @param Request $request
     * author hongwenyang
     * method description : 连连支付回调
     * info_order:订单类型：1-> C端支付  2-> B端支付
     * no_order:数据库唯一订单号
     */

    public function getInfo(Request $request){
        $notify_data = $request->all();
        $s = new Logs();
        $s->logs($notify_data['no_order'],$request->all());
        if($notify_data['result_pay'] == "SUCCESS"){
           // 支付成功
           if($notify_data['info_order'] == 2){
               // B端支付 修改支付状态  判断是否是共享订单 且获取一次 B端当做C端时是否已支付
               $orderType = UserApply::where('order_id',$notify_data['no_order'])->first();
               if($orderType->order_type == 1){
                   // 是共享订单
                   if($orderType->c_apply_status != 0){
                       // C端已支付 更改B端数据
                       UserApply::where('order_id',$notify_data['no_order'])->update([
                           'b_apply_status'=>4
                       ]);
                   }else{
                       // C端未支付 更改C端数据
                       UserApply::where('order_id',$notify_data['no_order'])->update([
                           'c_apply_status'=>4
                       ]);
                   }
               }else{
                   $s = UserApply::where('order_id',$notify_data['no_order'])->update([
                       'b_apply_status'=>4
                   ]);
               }
           }else{
               // C端支付 修改支付状态
              $s =  UserApply::where('order_id',$notify_data['no_order'])->update([
                   'c_apply_status'=>4
               ]);
           }
       }
//        $s->logs("异步通知","");
        die("{'ret_code':'0000','ret_msg':'交易成功'}");
    }


    public function curl($url,$data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        return $result;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */

    function createLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);
        //file_put_contents("log.txt","转义前:".$arg."\n", FILE_APPEND);
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
        //file_put_contents("log.txt","转义后:".$arg."\n", FILE_APPEND);
//
//        $s = new Logs();
//        $s->logs('对待签名参数数组排序',$arg);
        return $arg;
    }


    /**RSA签名
     * $data签名数据(需要先排序，然后拼接)
     * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
     * 最后的签名，需要用base64编码
     * return Sign签名
     */

    function Rsasign($data,$priKey) {

        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);

        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res,OPENSSL_ALGO_MD5);

        //释放资源
        openssl_free_key($res);

        //base64编码
        $sign = base64_encode($sign);
        //file_put_contents("log.txt","签名原串:".$data."\n", FILE_APPEND);


        return $sign;
    }


    /**
     * 生成要请求给连连支付的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    function buildRequestPara($para_temp) {

        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);


        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;

        $para_sort['sign_type'] = strtoupper(trim("RSA"));
        foreach ($para_sort as $key => $value) {
            $para_sort[$key] = $value;
        }

        return $para_sort;
        //return urldecode(json_encode($para_sort));
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    function paraFilter($para) {

        $para_filter = array();

        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "")continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    function argSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    function buildRequestMysign($para_sort) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

//        $s = new Logs();
//        $s->logs('生成Ios的Sign原始',$prestr);
        $prestr = str_replace("¬ify_url","&notify_url",$prestr);
        $prestr = str_replace("&amp;","&",$prestr);
        $mysign = "";

        $llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALihaP4Qh8WFepFX
J7DV9/L4Scwk60Xzz6Lfz8A7zNIiPszKtgan/To8W8lzBMEXH+mFqSQp6ZHFDFd3
a3i6CNdp5T/dwa8ehnHnXfa3aACtzOu6DHO+SC9kFP/qnQuT6MbNBZJcgrDMd5ym
7io3VZPtF9MSZhRYOtUch1lcwHqvAgMBAAECgYAJqEe5olu3tTeoCosE8Ow7RUl2
6Cd2TT57IoHXaElaZHgsdh33UyontaiHbQC+qNr+eANU5OxWt5vhp1lzwydbJkPn
As5rpZ8u3G2RmbRKvwbi/7HyD2OrrfJd7MexfgABEI3CuVXky1eNGDlx3CbaHiet
cBXxXmyk5KG17Bou4QJBAPSfZ5NuEdZ/MTXoT3SyjDnAVApW3T8qL/YEwmJXhda5
VA2lJbCqNQnt65zWo/XMcRlk7BewuMpaNYeTecQugGsCQQDBN7W8VFPLQJH5HpNN
BcamtYPHPMZ9fo1/iO5vp+3dVcD4OHNPfwIXDH8rqD8K0UGFAJlUqxe32Jvm57vp
0C/NAkB+GCUV0+kR/iJpvUQnzVmG82LeqYQGaUKruCxS8PamhoWTOwvAkxwf8CpB
gEqoCyhJhMJGO+wlMnbhWd2siKPdAkEAjVo9SScEGlkFsZOtvZZxKvr2CicrKxoP
WHMLxIG9IcSfpLhfm5PWKpiI3J58aGSII0453lhAxM3h2D5GGhqlLQJASJxRWJPO
DMDjq5KD/MSyehYdbPooyjqGSSTKe1WooMQAM8HcR0MIcsMngs9j5MAVjUifGTzf
QjPgPJpFpvx4fQ==
-----END RSA PRIVATE KEY-----';
        $mysign = $this->RsaSign($prestr, $llpay_config['RSA_PRIVATE_KEY']);
//        file_put_contents("log.txt","签名:".$mysign."\n", FILE_APPEND);
        return $mysign;
    }


}
