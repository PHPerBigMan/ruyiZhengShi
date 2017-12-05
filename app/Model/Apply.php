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

class Apply extends Model{
    protected $table = 'user_apply';


    /**
     * @param $c_apply_status 申请订单C端状态
     * @param $b_apply_status 申请订单B端状态
     * @param $search         查询类型 0：非筛选  1:筛选
     * @param string $keyword_cat_id  分类关键词
     * @param string $keyword_b_apply_status  申请订单B端状态筛选关键词
     * @return mixed
     * author hongwenyang
     * method description : 今日匹配
     */
    public function Today($c_apply_status,$b_apply_status,$search,$keyword_cat_id = "",$keyword_b_apply_status = ""){
        $t = time();
        $MIN = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $MAX = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        //今日匹配

        if($search == 0){
            $data = DB::table('user_apply')
                ->join('product','product.id','=','user_apply.product_id')
                ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
//                ->join('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
                ->join('product_cat','product.cat_id','=','product_cat.id')
                ->whereIn('user_apply.c_apply_status',$c_apply_status)
                ->whereIn('user_apply.b_apply_status',$b_apply_status)
                ->whereBetween('user_apply.create_time',["$MIN","$MAX"])
                ->where('product.business_id',session('business_admin'))
                ->select('user_apply.id','user_apply.order_type','apply_form.data','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
                ->get();

        }else{
            if(!empty($keyword_cat_id)){
                $map['product.cat_id']               = $keyword_cat_id;
            }
            if(!empty($keyword_b_apply_status)){
                $map['user_apply.b_apply_status']    = $keyword_b_apply_status;
            }
            $data = DB::table('user_apply')
                ->join('product','product.id','=','user_apply.product_id')
                ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
                ->join('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
                ->join('product_cat','product.cat_id','=','product_cat.id')
                ->whereIn('user_apply.c_apply_status',$c_apply_status)
                ->whereBetween('user_apply.create_time',["$MIN","$MAX"])
                ->where('product.business_id',session('business_admin'))
                ->where($map)
                ->select('apply_basic_form.is_company','user_apply.id','user_apply.order_type','apply_form.data','apply_basic_form.data as basic_data','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
                ->get();
        }

        return $data;
    }


    /**
     * @param $c_apply_status 申请订单C端状态
     * @param $b_apply_status 申请订单B端状态
     * @param $search         查询类型 0：非筛选  1:筛选
     * @param string $keyword_cat_id  分类关键词
     * @param string $keyword_b_apply_status  申请订单B端状态筛选关键词
     * @param string $keyword_create_time   申请订单时间戳
     * @return mixed
     * author hongwenyang
     * method description : 历史匹配 和 已完结
     */

    public function History($c_apply_status,$b_apply_status,$search,$keyword_cat_id = "",$keyword_b_apply_status = "",$keyword_create_time = ""){

        if($search == 0){
            $upcetCatId = [35,36,62,63,64,67,68,70,65,66,71];

            //历史匹配 已完结
            $data = DB::table('user_apply')
                ->join('product','product.id','=','user_apply.product_id')
                ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
//                ->join('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
                ->join('product_cat','product.cat_id','=','product_cat.id')
                ->whereIn('user_apply.c_apply_status',$c_apply_status)
                ->whereIn('user_apply.b_apply_status',$b_apply_status)
                ->where('product.business_id',session('business_admin'))
//                ->where('apply_basic_form.type','user_apply.order_type')
                ->select('user_apply.user_id','product.cat_id','user_apply.order_id','user_apply.order_count','user_apply.id','user_apply.order_type','apply_form.data','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
                ->get();
        }else{

          if(!empty($keyword_cat_id)){
              $map['product.cat_id'] = $keyword_cat_id;
          }
          if(!empty($keyword_b_apply_status)){
              $map['user_apply.b_apply_status']    = $keyword_b_apply_status;
          }
          if(!empty($keyword_create_time)){
              $MIN = strtotime($keyword_create_time);
              $MAX = $MIN + (24*60*60);
          }
          if(empty($keyword_create_time)){
              $data = DB::table('user_apply')
                  ->join('product','product.id','=','user_apply.product_id')
                  ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
                  ->join('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
                  ->join('product_cat','product.cat_id','=','product_cat.id')
                  ->whereIn('user_apply.c_apply_status',$c_apply_status)
                  ->whereIn('user_apply.b_apply_status',$b_apply_status)
                  ->where('product.business_id',session('business_admin'))
                  ->where($map)
                  ->select('apply_basic_form.is_company','user_apply.order_id','user_apply.order_count','user_apply.id','user_apply.order_type','apply_form.data','apply_basic_form.data as basic_data','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
                  ->get();
          }else if(empty($map)){
              $data = DB::table('user_apply')
                  ->join('product','product.id','=','user_apply.product_id')
                  ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
                  ->join('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
                  ->join('product_cat','product.cat_id','=','product_cat.id')
                  ->whereIn('user_apply.c_apply_status',$c_apply_status)
                  ->whereBetween('user_apply.create_time',["$MIN","$MAX"])
                  ->where('product.business_id',session('business_admin'))
                  ->select('apply_basic_form.is_company','user_apply.order_id','user_apply.order_count','user_apply.id','user_apply.order_type','apply_form.data','apply_basic_form.data as basic_data','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
                  ->get();
          }else{
              $data = DB::table('user_apply')
                  ->join('product','product.id','=','user_apply.product_id')
                  ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
                  ->join('apply_basic_form','apply_basic_form.user_id','=','user_apply.user_id')
                  ->join('product_cat','product.cat_id','=','product_cat.id')
                  ->whereIn('user_apply.c_apply_status',$c_apply_status)
                  ->whereBetween('user_apply.create_time',["$MIN","$MAX"])
                  ->where('product.business_id',session('business_admin'))
                  ->where($map)
                  ->select('apply_basic_form.is_company','user_apply.order_id','user_apply.order_count','user_apply.id','user_apply.order_type','apply_form.data','apply_basic_form.data as basic_data','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
                  ->get();
          }
        }
        $upcetCatId = [35,36,62,63,64,67,68,70,65,66,71];
        foreach($data as $k=>$v){
            //如果产品分类包括在这个数据里面则取企业基本信息
            if(in_array($v->cat_id,$upcetCatId)){
                $is_company = 0;
            }else{
             $is_company = 1;
            }
            $basic_data =  DB::table('apply_basic_form')->where([
                'user_id'=>$v->user_id,
                'type'=>$v->order_type,
                'is_company'=>$is_company
            ])->first();
            $v->basic_data = $basic_data->data;
            $v->is_company = $basic_data->is_company;
        }
        return $data;
    }

    /**
     * @param $keywords
     * @param $PayStatus
     * @return mixed
     * author hongwenyang
     * method description : 成功匹配
     */

    public function Success($keywords,$PayStatus){

        if($keywords['search'] == 0){
            unset($keywords['search']);
        }else{
            unset($keywords['search']);
           $keywords['product.cat_id'] = $keywords['cat_id'];
            unset($keywords['cat_id']);
        }

        $data = DB::table('user_apply')
            ->join('product','product.id','=','user_apply.product_id')
            ->join('apply_form','apply_form.id','=','user_apply.apply_form_id')
            ->join('product_cat','product.cat_id','=','product_cat.id')
            ->where('product.business_id',session('business_admin'))
            ->where($keywords)
            ->whereIn('user_apply.b_apply_status',$PayStatus)
            ->select('user_apply.order_id','user_apply.user_id','user_apply.order_type','user_apply.id','user_apply.order_type','apply_form.data','user_apply.order_count','product.cat_id','user_apply.b_apply_status','product_cat.cat_name','user_apply.create_time')
            ->get();

        foreach($data as $v){
            $v->basic_data = DB::table('apply_basic_form')->where(['user_id'=>$v->user_id,'type'=>$v->order_type])->value('data');
            $v->is_company = DB::table('apply_basic_form')->where(['user_id'=>$v->user_id,'type'=>$v->order_type])->value('is_company');
        }

        return $data;
    }

    /**
     * @param $data
     * @return int
     * author hongwenyang
     * method description : 提交申请信息
     */

    public function SaveUserApply($data,$type = ""){
        if($data['equipment_type'] == 1){
            $data['user_id'] = $data['business_id'];
            unset($data['business_id']);
        }
        // 进行匹配度的计算
        $retStatus = ProductCheck($data,1);
        if($retStatus['code'] == 200 || $type == 1){
            $data['c_apply_status'] = 0;
            $data['b_apply_status'] = 0;
            $data['order_type']     = $data['equipment_type'];
            $data['create_time']    = time();
            $data['apply_form_id']  = DB::table('apply_form')->where(['user_id'=>$data['user_id'],'equipment_type'=>$data['equipment_type'],'cat_id'=>DB::table('product')->where([
                'id'=>$data['product_id']
            ])->value('cat_id')])->value('id');

            //这部分 2017/10/12 添加有可能会有错误
            $applyData = json_decode(DB::table('apply_form')->where(['user_id'=>$data['user_id'],'equipment_type'=>$data['equipment_type'],'cat_id'=>DB::table('product')->where([
                'id'=>$data['product_id']
            ])->value('cat_id')])->value('need_data'),true);

            $data['order_count'] = $applyData['money'];

            //  获取产品对应分类的 大分类
            $catId= DB::table('product_cat')->where([
                'id'=>DB::table('product')->where([
                    'id'=>$data['product_id']
                ])->value('cat_id')
            ])->value('p_id');

            // 订单号开头   房产类为1开头 汽车类为2开头 信用类为3开头 企业贷为4开头  如易贷为5开头
            switch ($catId){
                case 1:
                    //房产类
                    $orderTypeCatId = 1;
                    break;
                case 3:
                    //汽车类
                    $orderTypeCatId = 2;
                    break;
                case 4:
                    // 信用类
                    $orderTypeCatId = 3;
                    break;
                case 10:
                    //企业类
                    $orderTypeCatId = 4;
                    break;
                case 11:
                    //如易贷
                    $orderTypeCatId = 5;
                    break;
            }
            if(isset($data['order_id']) && ($data['order_id'] != "")){
                // 重新匹配下单
                $newOrderId   = $orderTypeCatId.date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $map['order_id'] = $data['order_id'];
                $data['order_id'] = $newOrderId;
                //更改订单状态 重新匹配的订单 不需要支付
                $data['c_apply_status'] = 4;
                $data['b_apply_status'] = 0;
                $data['reason'] = "";
                $s = $this->where($map)->update($data);
                $orderId = $data['order_id'];
            }else{
                // 不是重新匹配下单
                $data['order_id']       = $orderTypeCatId.date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $s = $this->insertGetId($data);
                $orderId = $this->where(['id'=>$s])->value('order_id');
            }

            if($s){
                //申请成功将当前用户申请产品时的 资料存储
                $orderData = DB::table('apply_form')->where(['user_id'=>$data['user_id'],'cat_id'=>DB::table('product')->where([
                    'id'=>$data['product_id']
                ])->value('cat_id')])->select('need_data','data')->first();
                OrderApplyForm::insert([
                    'need_data'=>$orderData->need_data,
                    'data'=>$orderData->data,
                    'order_id'=>$orderId
                ]);

                // 因为ios审核的原因需要将 用户之前填写的信息全部删除 apply_basic_form 和 apply_form
//                ApplyBasic::where('user_id',$data['user_id'])->delete();
//                ApplyForm::where('user_id',$data['user_id'])->delete();
                $code = 200;
                $msg  = "申请成功";
            }else{
                $code = 404;
                $msg  = "申请失败";
            }
            $j = [
                'code'      =>$code,
                'order_id'  =>empty($orderId) ? "" : $orderId,
                'msg'       =>$msg
            ];
        }else{
            $j = [
                'code'  =>$retStatus['code'],
                'data'  =>$retStatus['data'],
                'msg'   =>$retStatus['msg'],
                'match_score'=>$retStatus['match_score']
            ];
        }
        return $j;
    }

    public function applyList($type){
        switch ($type){
            case 0:
                $b_apply_status = [2,3,9];
                $order_type = 0;
                break;
            case 1:
                $b_apply_status = [2];
                $order_type = 1;
                break;
            case 2:
                $b_apply_status = [4];
                $order_type = 0;
                break;
            case 3:
                $b_apply_status = [4];
                $order_type = 1;
                break;
            case 4:
                $b_apply_status = [6,7];
                $order_type = 0;
                break;
            default:
                $b_apply_status = [6,7];
                $order_type = 1;
                break;
        }
        $data = $this
            ->join('product as p','p.id','=','user_apply.product_id')
            ->join('product_cat as c','c.id','=','p.cat_id')
            ->select('user_apply.id','user_apply.order_count','c.cat_name','user_apply.order_id','user_apply.user_id','p.cat_id','user_apply.b_apply_status','user_apply.b_is_evaluate')
            ->where([
                'p.business_id'=>session('business_admin'),
                'user_apply.order_type'=>$order_type
            ])
            ->where('user_apply.c_apply_status','>=',4)
            ->whereIn('user_apply.b_apply_status',$b_apply_status)
            ->paginate(15);

        $cat_arr = [45,46];
        $basicType = 0;
        foreach ($data as $k=>$v){
            if(in_array($v->cat_id,$cat_arr)){
                $basicType = 1;
            }
            $v->data = json_decode(DB::table('apply_basic_form')->where([
                'user_id'=>$v->user_id,
                'is_company'=>$basicType,
                'type'=>$order_type
            ])->value('data'));
            $v->need_data = json_decode(DB::table('apply_form')->where([
                'user_id'=>$v->user_id,
                'equipment_type'=>$order_type,
                'cat_id'=>$v->cat_id
            ])->value('data'));
        }
        return $data;
    }
}