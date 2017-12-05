<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index(){
        return view('Back/Login/login');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 后台登录
     */

    public function check(Request $request){
        $input = $request->all();
        unset($input['_token']);
        unset($input['s']);
        $input['admin_pwd'] = sha1(sha1($input['admin_pwd']).'1234');
        $d = DB::table('admin')->where($input)->first();

        if($d){
            Session::put('admin_user',$d->admin_name);
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
        Session::forget('admin_user');
        return redirect('/back/login');
    }
}
