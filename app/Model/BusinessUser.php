<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:26
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BusinessUser extends Model{
    protected $table = 'business_user';

    public function getCreateTimeAttribute($value){
        return date('YmdHis',strtotime($value));
    }


    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 保存 企业用户信息
     */

    public function SaveDataModel($data){
        if(!empty($data['pic'])){
            //保存营业执照图片
            $data['pic'] = '/uploads/'.Storage::disk('yyzz')->put('yyzz', $data['pic']);
        }
        $map['id'] = $data['user_id'];
        unset($data['user_id']);
        $s = $this->where($map)->update($data);

        return $this->returnMsg($s);
    }


    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 修改金融管家联系方式
     * param :companyHousePhone  联系方式
     */

    public function SaveHouseKeeper($data){
        if($data['code'] != session('code')){
            $retJson['code'] = 404;
            $retJson['msg']  = "验证码错误";
        }else{
            $map['id'] = $data['user_id'];
            unset($data['user_id']);
            $s = $this->where($map)->update([
                'companyHousePhone'=>$data['companyHousePhone']
            ]);
            if($s){
                $retJson['code'] =  200;
                $retJson['msg']  = "操作成功";
            }else{
                $retJson['code'] =  404;
                $retJson['msg']  = "操作失败";
            }

        }
        return $retJson;
    }


    /**
     * @param $ManagementData
     * @return array
     * author hongwenyang
     * method description : 获取B端用户的经营数据
     */

    public function DataList($ManagementData){
        $t = time();
        $MIN = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $MAX = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        //今日有效订单
        $TManagement = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$ManagementData['business_id'],
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->count();
        //今日订单总额
        $TManagementCount = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$ManagementData['business_id'],
            ])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('u.order_count');
        //今日共享订单
        $TManagementToday =  DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$ManagementData['business_id'],
                'u.order_type'=>1
            ])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->count();


        //历史有效订单
        $Management = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$ManagementData['business_id'],
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->count();
        //历史订单总额
        $ManagementCount = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$ManagementData['business_id'],
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('u.order_count');
        //历史共享订单
        $ManagementToday =  DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$ManagementData['business_id'],
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->count();
        $retData = [
            'todayList'=>$TManagement,
            'todayTotal'=>$TManagementCount,
            'todayShare'=>$TManagementToday,
            'historyList'=>$Management,
            'historyTotal'=>$ManagementCount,
            'historyShare'=>$ManagementToday
        ];

        return $retData;
    }


    /**
     * @param $id
     * @return array
     * author hongwenyang
     * method description : 财务对账
     */
    public function MoneyList($id){

        //今日
        $t = time();
        $MIN = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $MAX = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        //今日消费 (作为B端支付的服务费和共享费用)
        //今日收入
        $TodaySum = $this->TSumMoney($MIN,$MAX,$id);

        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        //本月消费  本月收入
        $MonthSum = $this->MSum($beginThismonth,$endThismonth,$id);

        //历史消费  历史收入
        $HistorySum = $this->HSum($id);

        $count = $this->Mlist($id);

        $return = [
            'today'=>$TodaySum,
            'month'=>$MonthSum,
            'history'=>$HistorySum,
            'monthList'=>$count
        ];

        return $return;
    }


    protected function returnMsg($s){
        if($s){
            $retJson['code'] =  200;
            $retJson['msg']  = "操作成功";
        }else{
            $retJson['code'] =  404;
            $retJson['msg']  = "操作失败";
        }

        return $retJson;
    }

    /**
     * @param $MIN 起始时间戳
     * @param $MAX 结束时间戳
     * @param $id
     * @return array
     * author hongwenyang
     * method description : 计算消费和收入
     * pay:消费
     * count:支出
     */

    protected function TSumMoney($MIN = "",$MAX = "",$id){
        //B端服务费
        $data_b = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>0
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->sum('b_serve');
        //C端服务费
        $data_c = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->sum('c_serve');

        //共享服务费
        $data_d = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->sum('share');

        $Count = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
            ])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('u.order_count');
        $count = $data_c + $data_b + $data_d;
        $return = [
            'pay'=>$count,
            'count'=>$Count,
        ];

        return $return;
    }

    /**
     * @param string $MIN
     * @param string $MAX
     * @param $id
     * @return array
     * author hongwenyang
     * method description : 本月列表
     */
    protected function MSum($MIN = "",$MAX = "",$id){
        //B端服务费
        $data_b = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>0
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->sum('b_serve');
        //C端服务费
        $data_c = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->sum('c_serve');

        //共享服务费
        $data_d = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->sum('share');

        $Count = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
            ])
            ->whereBetween('u.create_time',[$MIN,$MAX])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('u.order_count');
        $count = $data_c + $data_b + $data_d;
        $return = [
            'pay'=>$count,
            'count'=>$Count,
        ];
        return $return;
    }

    /**
     * @param $id
     * @return array
     * author hongwenyang
     * method description : 每个月列表
     */

    protected function Mlist($id){
        $data = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->select('u.order_type','u.b_serve','u.c_serve','u.share','u.create_time','u.id')
            ->get();
        $pay = [];
        if(!($data->isEmpty())){

            $month = [
                '01',
                '02',
                '03',
                '04',
                '05',
                '06',
                '07',
                '08',
                '09',
                '10',
                '11',
                '12',
            ];
            foreach($data as $k=>$v){
                foreach ($month as $k1=>$v1){
                    if(date('m',$v->create_time) == $v1){
                        $new[$k1][$k] = $v;
                        $new[$k1][$k]->month = $v1;
                    }
                }
            }
            $pay = [];
            sort($new);
            foreach ($new as $k=>$v){
                foreach($v as $k1=>$v1){
                    if(!empty($v1->b_serve)){
                        $pay[$k]['month'] = date('m',$v1->create_time);
                        if($v1->order_type == 0){
                            $pay[$k]['payList'][$k1]['money'] = $v1->b_serve;
                            $pay[$k]['payList'][$k1]['create_time'] = date('H:i',$v1->create_time);
                            $pay[$k]['payList'][$k1]['title'] = "服务费支出";
                            $pay[$k]['payList'][$k1]['id'] = $v1->id;
                        }
                        if($v1->order_type == 1){
                            $pay[$k]['payList'][$k1]['money'] = $v1->c_serve;
                            $pay[$k]['payList'][$k1]['create_time'] = date('H:i',$v1->create_time);
                            $pay[$k]['payList'][$k1]['title'] = "服务费支出";
                            $pay[$k]['payList'][$k1]['id'] = $v1->id;
                            //这里还要排查
                            if(!empty($v1->share)){
                                $pay[$k]['payList'][$k1+1]['money'] = $v1->c_serve;
                                $pay[$k]['payList'][$k1+1]['create_time'] = date('H:i',$v1->create_time);
                                $pay[$k]['payList'][$k1+1]['title'] = "共享条件支出";
                            }
                        }
                        sort($pay[$k]['payList']);
                    }
                }
            }
        }
        return $pay;
    }

    /**
     * @param $id
     * @return array
     * author hongwenyang
     * method description : 历史列表
     */

    protected function HSum($id){

        $data_b = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>0
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('b_serve');
        $data_c = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('c_serve');

        //共享服务费
        $data_d = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
                'u.order_type'=>1
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('share');

        $Count = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$id,
            ])
            ->whereIn('u.b_apply_status',[4,6,7])
            ->sum('u.order_count');
        $count = $data_c + $data_b + $data_d;
        $return = [
            'pay'=>$count,
            'count'=>$Count,
        ];
        return $return;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * author hongwenyang
     * method description : 获取已上传产品的用户信息
     */
    public static function getUserProduct($keyword = ""){
        $data           = array();
        $returnData     = array();

        $data = BusinessUser::join('product as p','p.business_id','=','business_user.id')
            ->where('business_user.companyName','like',"%$keyword%")
            ->groupBy('business_id')->pluck('business_id');

        $returnData = BusinessUser::whereIn('id',$data)->select('id','companyName','number')->paginate(15);

        return $returnData;
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     * author hongwenyang
     * method description :B端用户已上传产品分类列表
     */

    public static function getUserProductCat($id){
        $cat            = array();
        $catId          = array();

        $catId  =  Product::where(['business_id'=>$id,'is_del'=>0])->pluck('cat_id');

        $cat = ProductCat::whereIn('id',$catId)->where('is_del',0)->paginate(15);

        if($catId->isNotEmpty()){
            foreach($cat as $v){
                $v->count = Product::where(['business_id'=>$id,'cat_id'=>$v->id,'is_del'=>0])->count();
            }
        }
        return $cat;
    }

    /**
     * @param $id
     * @param $cat_id
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     * author hongwenyang
     * method description : 获取对应用户对应分类的产品详情
     */
    public static  function CatProduct($id,$cat_id){
        $data = array();
        $data = Product::where(['business_id'=>$id,'cat_id'=>$cat_id,'is_del'=>0])->paginate(15);

        if(!$data->isEmpty()){
            foreach($data as $v){
                $v->content = json_decode($v->content);
            }
        }

        return $data;
    }
}