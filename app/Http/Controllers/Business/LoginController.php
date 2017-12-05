<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index(){
        return view('Business.login');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 后台登录
     */

    public function check(Request $request){
        $input = $request->except(['s','_token']);
        $input['password'] = sha1($input['password']);
        $d = DB::table('business_user')->where(['phone'=>$input['account'],'password'=>$input['password']])->first();
//        $d = DB::table('business_user')->where(['phone'=>$input['account'],'password'=>$input['password']])
//            ->orWhere(['companyCode'=>$input['account'],'password'=>$input['password']])->first();
        if($d){
            Session::put('business_admin',$d->id);
            Session::put('business_name',empty($d->companyName) ? $d->id : $d->companyName);
            return response()->json(['code'=>200,'msg'=>'登录成功']);
        }else{
            return response()->json(['code'=>404,'msg'=>'登录失败']);
        }
    }


    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * author  hongwenyang
     * method description : 退出登录
     */

    public function loginout(){
        Session::forget('business_admin');
        return redirect('/business/login');
    }
}
