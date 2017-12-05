<?php

namespace App\Http\Controllers\Bapi;

use App\Model\Logs;
use App\Model\Operate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class OperateController extends Controller
{
    protected $model;
    public function __construct()
    {
        $this->model = new Operate();
    }

    /**
     * @param Request $request business_id 用户id
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 今日有效订单、本月订单总额
     */
    public function Order(Request $request){
        $businessId     = $request->input('business_id');
        $count          = $this->model->orderCount($businessId);
        $monthMoney     = $this->model->Month($businessId);
        $data['count']  = $count;
        $data['money']  = $monthMoney;
        $retData        = returnData($data);
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 查询黑名单用户
     */

    public function BlackList(Request $request){
        $BlackKey = $request->except(['s']);
        array_filter($BlackKey);

        if(!empty($BlackKey['name']) && !empty($BlackKey['user_no'])){
            $where['name'] = $BlackKey['name'];
            $where['user_no'] = $BlackKey['user_no'];
        }else if(!empty($BlackKey['name']) && empty($BlackKey['user_no'])){
            $where['name'] = $BlackKey['name'];
        }else if(!empty($BlackKey['user_no']) && empty($BlackKey['name'])){
            $where['user_no'] = $BlackKey['user_no'];
        }else{
            $where['user_phone'] = $BlackKey['user_phone'];
        }

        $BlackData= DB::table('black_user')->where($where)->whereIn('status',[0,7])->select('id','name','user_no','money','user_phone','start_time')->orderBy('create_time','desc')->get();
        foreach($BlackData as $k=>$v){
            $BlackData[$k]->start_time = date('Y-m-d H:i',$v->start_time);
        }
        $retData = returnData($BlackData);
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 查看黑名单详情
     */

    public function BlackRead(Request $request){
        $id = $request->input('id');
        //当前id 对应的数据
        $BlackData = DB::table('black_user as u')
            ->join('business_user as b','b.id','=','u.business_id')
            ->where([
                'u.id'=>$id
            ])->select('b.companyName','u.name','u.user_no','u.status','u.money','u.user_phone','u.start_time','u.content','u.status')->first();
        //查找同一个人的所有数据
        $AllData = DB::table('black_user as u')
            ->join('business_user as b','b.id','=','u.business_id')
            ->where([
                'u.name'=>$BlackData->name,
                'u.user_no'=>$BlackData->user_no,
                'u.user_phone'=>$BlackData->user_phone,
            ])->select('b.companyName','u.name','u.create_time','u.title','u.user_no','u.money','u.user_phone','u.start_time','u.content','u.status')->get();
        $return  = json_decode('{}');
        $money = "";
        foreach($AllData as $k=>$v){
            $return->name               = $v->name;
            $return->user_no            = $v->user_no;
            $return->user_phone         = $v->user_phone;
            $return->status             = $BlackData->status;
            $money                      += $v->money;
            $return->money              = $money;
            $return->create_time         = $v->create_time;
            $return->list[$k]['companyName']    = $v->companyName;
            $return->list[$k]['content']        = $v->content;
            $return->list[$k]['start_time']        = date("Y-m-d",$v->start_time);
            $return->list[$k]['title']              = $v->title;

        }

        $retData = returnData($return);
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 我的黑名单
     */

    public function MyBlackList(Request $request){
        $BusinessId = $request->except(['s']);
        $BlackData = DB::table('black_user')->where($BusinessId)->select('id','name','user_no','money','user_phone','start_time','stop_time','status')->get();

        foreach($BlackData as $k=>$v){
            $BlackData[$k]->start_time = date('Y-m-d',$v->start_time);
            $BlackData[$k]->stop_time = date('Y-m-d',$v->stop_time);
        }
        $retData = returnData($BlackData);
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 操作黑名单
     */

    public function BlackChange(Request $request){
        $Black = $request->except(['s']);
        $BlackChange = DB::table('black_user')->where(['id'=>$Black['id']])->update([
            'status'=>$Black['status']
        ]);
        $a = new Logs();
        $a->logs('状态',$Black);
        $retStatus = returnStatus($BlackChange);
        return response()->json($retStatus);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 添加黑名单用户
     */

    public function BlackSave(Request $request){
        $BlackData = $request->except(['s']);
        try {

            $save = $request->file('imgs');
            foreach($save as $k=>$v){
                $imgs[$k] = '/uploads/'.$v->store('img','img');
            }
            $BlackData['imgs'] = json_encode($imgs);
            $BlackData['start_time'] = time();
            $BlackData['stop_time']  = strtotime("6 month");
            $BlackData['status'] = 2;
            $s = DB::table('black_user')->insert($BlackData);
            $j = returnStatus($s);
            return response()->json($j);
        } catch (\Exception $exception) {
            $s = new Logs();
            $s->logs('添加黑名单',$BlackData);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 门店运营 获取评价列表
     */
    public function EvaluateList(Request $request){
        $eData = $request->except(['s']);
        //根据产品的分类对用户的基本信息进行查询
//        $productCat = DB::table;
        $data = DB::table('user_apply')
            ->join('product','user_apply.product_id','=','product.id')
            ->join('product_cat','product.cat_id','=','product_cat.id')
            ->join('apply_basic_form','user_apply.user_id','=','apply_basic_form.user_id')
            ->join('business_user','product.business_id','=','business_user.id')
            ->where([
                ['product.business_id','=',$eData['business_id']],
                ['user_apply.c_apply_status','=','8'],
                ['user_apply.b_is_evaluate','=',$eData['type']],
                ['user_apply.equipment_type','=','0'],
                ['apply_basic_form.type','=','0'],
            ])
            ->select('apply_basic_form.is_company','product.cat_id','user_apply.order_count','user_apply.user_id','user_apply.order_type','apply_basic_form.data','business_user.number','user_apply.id','user_apply.c_apply_status','product.id as product_id','product_cat.cat_name','user_apply.create_time','product.content','business_user.companyName','user_apply.order_id','user_apply.b_is_evaluate as c_is_evaluate','business_user.number')
            ->get();
        $title = ['is_company','user_id','order_type','first_cat_name','phone','order_count','number','company','product_id','order_id','cat_name','create_time','c_is_evaluate','c_apply_status'];
        $j =  productData($data,$title);
        return response()->json($j);
    }
}
