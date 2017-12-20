<?php

namespace App\Http\Middleware;

use App\Model\Admin;
use App\Model\AdminGroup;
use App\Model\AdminRule;
use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request;
class UserPermession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // 获取当前操作的url
        $action = $request->input(['s']);

        $other_action = array('handle');
        $res = session('admin_user');

        $res = Admin::where('admin_name',$res)->first();

        $map['groupid'] = $res->roleid;

        $rule = AdminGroup::where($map)->first();
//        dd($res);
        $temp_arr = $rule->ruleid;
//        // 开始剔除一些特别的路由 (B,C端用户信息  排除订单管理模块,黑名单)
        $expect  = explode('/',$action);
        if(is_numeric($expect[count($expect)-1]) && ($expect[count($expect)-2] != "order") && ($expect[count($expect)-2] != "black") ){
            $expect = array_slice($expect,1,count($expect)-2);
            if(!empty($expect)){
                $action = '/'.implode('/',$expect);
            }
        }

        $new['identifying'] = $action;
        $rule_id = AdminRule::where($new)->value('ruleid');


        $rule_type = AdminRule::where($new)->value('type');
       
        if($res->roleid != "1"){
            if(is_null($rule_id) || !in_array($rule_id,$temp_arr)){
                if(!in_array($action,$other_action)){
                    if($rule_type == 1){
                        return redirect('/back/error');
                    }else{
                        return response()->json(['status' => '404', 'msg' =>'您没有权限',]);
                    }
                }
            }
        }

        return $next($request);
    }
}
