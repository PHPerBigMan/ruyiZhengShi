<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 个人中心 页面
     */

    public function index(){
        $AdminData = DB::table('business_user')
//            ->join('business_cat','business_cat.id','=','business_user.type')
            ->select('business_user.*')
            ->where(['business_user.id'=>session('business_admin')])->first();
        $PayList = [];
        if(!empty($AdminData)){
            $PayList = DB::table('pay_list')->where(['user_id'=>$AdminData->id])->get();
        }

        $j = [
            'data'=>$AdminData,
            'payList'=>$PayList,
            'title'=>'userInfo'
        ];

        return view('Business.admin.index',$j);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 完善弹窗页面
     */

    public function Add(){
        $BusinessCat = DB::table('business_cat')->get();
        $AdminData = DB::table('business_user')->where(['id'=>session('business_admin')])->first();
        $j = [
            'data'=>$BusinessCat,
            'businessData'=>$AdminData
        ];
        return view('Business.admin.add',$j);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 判断用户时候已经完善资料
     */

    public function isSave(){
        $isSave = DB::table('business_user')->where(['id'=>session('business_admin')])->first();
        if(empty($isSave->companyName)){
            $retJson['code'] = 200;
            $retJson['msg']  = '可完善';
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = '已完善';
        }

        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存用户提交的完善信息
     */

    public function adminSave(Request $request){
        $SaveData = $request->except(['s']);

        $code = session('code');
        if(!empty($SaveData['code']) && $SaveData['code'] != $code){
            $retJson['code'] = 403;
            $retJson['msg']  = '验证码错误';
        }else{
            unset($SaveData['code']);
            $s = DB::table('business_user')->where(['id'=>session('business_admin')])->update($SaveData);

            if($s){
                $retJson['code'] = 200;
                $retJson['msg']  = "保存成功";
            }else{
                $retJson['code'] = 404;
                $retJson['msg']  = "保存失败";
            }
        }

        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 处理保存图片
     */

    public function adminImg(Request $request){
        $ImgUrl = '/uploads/'.$request->file('file')->store('img','img');
        $j = [
            'code'=>200,
            'msg'=>'图片处理成功',
            'data'=>$ImgUrl
        ];
        return response()->json($j);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 账户余额数据
     */

    public function PayList(){
        $ListData = DB::table('pay_list')
        ->join('business_user','pay_list.user_id','=','business_user.id')
        ->where(['user_id'=>session('business_admin'),'customer_type'=>1])
        ->select('pay_list.*','business_user.money as list_money')
            ->get();

        foreach($ListData as $k=>$v){
            $ListData[$k]->create_time = date('Y-m-d',$v->create_time);
        }
        return response()->json($ListData);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 子账户列表
     */

    public function ChildList(){
        $ChildData = DB::table('business_child')->where(['p_id'=>session('business_admin')])->get();

        foreach($ChildData as $k=>$v){
            $ChildData[$k]->method = DB::table('method_list')->whereIn('id',explode(',',$v->method))->select('desc')->get();
            foreach($ChildData[$k]->method as $k1=>$v1){
                $Method[$k1] = $v1->desc;
            }
            $ChildData[$k]->method = implode(',',$Method);
        }
        return response()->json($ChildData);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 新增、编辑子账号页面
     */

    public function ChildShow($id){
        if($id == 0){
            $retData = [];
        }else{
            $retData = DB::table('business_child')->where(['id'=>$id])->first();
            $retData->method = explode(',',$retData->method);
        }
        $MethodData = DB::table('method_list')->get();

        $j = [
            'data'   =>$retData,
            'method' =>$MethodData,
            'id'     =>$id
        ];
        return view('Business.admin.childAdd',$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存子用户数据
     */

    public function ChildSave(Request $request){
        $SaveData = $request->except(['s']);
        $SaveData['method'] = $SaveData['method_id'];
        unset($SaveData['method_id']);
        $map['id'] = $SaveData['id'];
        unset($SaveData['id']);
        $SaveData['p_id'] = session('business_admin');
        if($map['id'] == 0){
            $is_Have = DB::table('business_child')->where(['p_id'=>$SaveData['p_id'],'name'=>$SaveData['name']])->get();
            if($is_Have){
                $retJson['code'] = 403;
                $retJson['msg']  = '子账号已存在';
                return response()->json($retJson);
            }else if(count($is_Have) > 3){
                $retJson['code'] = 404;
                $retJson['msg']  = "您的子账户数量超过上限";
            }else{
                $s = DB::table('business_child')->insert($SaveData);
            }
        }else{

            $s = DB::table('business_child')->where($map)->update($SaveData);
        }
        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = "操作成功";
        }
        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 删除
     */

    public function ChildDel(Request $request){
        $ChildId = $request->except(['s']);
        $s = DB::table('business_child')->where($ChildId)->delete();
        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = '删除成功';
        }
        return response()->json($retJson);
    }
}
