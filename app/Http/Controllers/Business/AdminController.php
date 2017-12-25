<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Model\BusinessChild;
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
       $data = BusinessChild::where('p_id',session('business_admin'))->get();

       $title = "子账号";

       return view('Business.admin.child',compact('data','title'));
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存子用户数据
     */

    public function ChildSave(Request $request){
        $data = $request->except(['s']);

        $s = BusinessChild::addChild($data,session('business_admin'));

        return $s;
    }


    /**
     * @param Request $request
     * @return mixed
     * author hongwenyang
     * method description : 修改子账号
     */
    public function ChildEdit(Request $request){
        $data = $request->except(['s']);

        $s=  BusinessChild::where([
            'id'=>$data['id']
        ])->update([
            'name'=>$data['name'],
            'password'=>sha1($data['password'])
        ]);

        return returnStatus($s);
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
