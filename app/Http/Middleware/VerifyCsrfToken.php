<?php

namespace App\Http\Middleware;

use App\Model\BusinessUser;
use App\Model\User;
use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Symfony\Component\HttpFoundation\Request;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/*',
        'bapi/*',
        'business/*',
        'back/*',
        'pay/*',
    ];

   public function __construct(Application $app, Encrypter $encrypter,Request $request)
   {
       parent::__construct($app, $encrypter);
       // 判断用户是否已经完善资料
       $getInformation = $request->all();
       if(isset($getInformation['user_id'])){
          $isHaveInformation =  User::where('id',$getInformation['user_id'])->value('user_name');
       }
       if(isset($getInformation['business_id'])){
           $isHaveInformation = BusinessUser::where('id',$getInformation['business_id'])->value('companyName');
       }

       /**
        * 判断用户是否已经完善资料
        */
       if(empty($isHaveInformation) || ($isHaveInformation == " ")){
           $arr = [
               '/api/register',
               '/api/getCode',
               '/api/login',
           ];

//          if(!in_array($getInformation['s'],$arr)){
//              $code = 211;
//              $msg = "数据未完善";
//              $j = [
//                  'code'=>$code,
//                  'msg'=>$msg
//              ];
////              die(json_encode($j));
//          }
       }
   }
}
