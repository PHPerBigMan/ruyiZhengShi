<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Model\Black;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlackController extends Controller
{
    protected $black;
    public function __construct()
    {
        $this->black = new Black();
    }

    public function index(){
        $data = DB::table('article')->where(['type'=>8])->first();

        $title = 'black';
        return view('Business.black.index',compact('data','title'));
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 黑名单数据列表
     */

    public function BlackList(){
        $BlackData = DB::table('black_user')->where(['is_del'=>0])->get();

        if(count($BlackData) == 0){
            $retData = [];
        }else{
            foreach($BlackData as $k=>$v){
                $retData[$k]['time'] = date('Y-m-d',$v->start_time).'-'.date('Y-m-d',$v->stop_time);
                $retData[$k]['name']  = $v->name;
                $retData[$k]['phone']  = $v->user_phone;
                $retData[$k]['cardNo']  = $v->user_no;
                $retData[$k]['money']  = $v->money;
                $retData[$k]['id']  = $v->id;
                switch ($v->status){
                    case 0:
                        $status = "已上架";
                        break;
                    case 1:
                        $status = "已下架";
                        break;
                    default:
                        $status = "审核中";
                        break;
                }
                $retData[$k]['status']  = $status;
            }
        }
        return response()->json($retData);
    }


    /**
     * @param $id
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 录入黑名单页面
     */

    public function blackAdd($id,$type){
        if($id == 0){
            $data = json_decode("{}");
            $data->name         = "";
            $data->user_no      = "";
            $data->user_phone   = "";
            $data->content  = "";
            $data->money    = "";
            $data->imgs     = [];
            $data->id       = $id;
        }else{
           $data = DB::table('black_user')->where(['id'=>$id])->first();
           if(!empty($data->imgs)){
               $data->imgs = json_decode($data->imgs,true);
           }
        }
        $j = [
            'data'=>$data,
            'type'=>$type
        ];
        return view('Business.black.add',$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 删除
     */

    public function blackDel(Request $request){
        $id = $request->except(['s']);
        foreach($id['id'] as $k=>$v){
            $this->black->where(['id'=>$v])->update([
                'is_del'=>1
            ]);
        }
        $retJson['code'] = 200;
        $retJson['msg']  = "删除成功";

        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 处理图片
     */
    public function blackImg(Request $request){
        $Img = $request->file('file')->store('img','img');

        return response()->json(['data'=>$Img]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存黑名单数据
     */

    public function blackSave(Request $request){
        $BlackData = $request->except(['s']);
        $map['id'] = $BlackData['id'];
        unset($BlackData['id']);
        if(!empty($BlackData['imgs'])){
            $BlackData['imgs'] = json_encode(explode(',','/uploads/'.$BlackData['imgs']));
        }

        if($map['id'] == 0){
            $BlackData['start_time'] = time();
            $BlackData['stop_time']  = strtotime("6 month");
            $BlackData['business_id'] = session('business_admin');
            $s = DB::table('black_user')->insert($BlackData);
        }else{
            if(empty($BlackData['imgs'])){
                unset($BlackData['imgs']);
            }
            $s = DB::table('black_user')->where($map)->update($BlackData);
        }
        $j = returnStatus($s);
        return response()->json($j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 黑名单查询
     */

    public function blackSearch(Request $request){
        $searchData = $request->except(['s']);
        $key = [];
        foreach($searchData as $k=>$v){
            if(!empty($v)){
                $key[$k] = $v;
            }
        }


        $searchResult = DB::table('black_user')
            ->join('business_user','business_user.id','=','black_user.business_id')
            ->where(['black_user.status'=>0])
            ->where($key)
            ->select('black_user.*','business_user.companyName')
            ->get();

        return response()->json($searchResult);
    }
}
