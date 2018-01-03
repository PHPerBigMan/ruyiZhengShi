<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
//    public function product(){
//       return $this->hasOne(Product::class,'product_id','id');
//    }

    /**
     * @param $keyword 关键词
     * @param $where 判断条件
     * @param $whereOr 判断条件
     * @return mixed
     * author hongwenyang
     * method description :  除支付待审核的订单
     */
    public function BackOrder($keyword = "",$where,$whereOr,$whereIn,$startTime,$endTime,$nextTime=""){
        $key = [];
        if(!empty($keyword)){
            if($_GET['type'] == 1){
                $key = [
                    ['order_id','like',$_GET['keyword'].'%']
                ];
            }else if($_GET['type'] == 3){
                $key = [
                    'c.cat_name'=>$_GET['keyword']
                ];
            }else if($_GET['type'] == 2){
                $key = [
                    'p.city'=>$_GET['keyword']
                ];
            }
            $data = DB::table('user_apply as u')
                ->join('product as p','u.product_id','=','p.id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->join('business_user as b','b.id','=','p.business_id')
                ->where($where)->where($key)->orWhere($whereOr)
                ->whereBetween('u.create_time',[$startTime,$endTime])->select('u.*','p.content','b.number','b.companyName','c.cat_name')
                ->whereIn('u.c_apply_status',$whereIn)
                ->orderBy('u.create_time','desc')
                ->paginate(10);
        }else{

            $data = DB::table('user_apply as u')
                ->join('product as p','u.product_id','=','p.id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->join('business_user as b','b.id','=','p.business_id')
                ->whereIn('u.c_apply_status',$whereIn)
                ->where($where)->orWhere($whereOr)->whereBetween('u.create_time',[$startTime,$endTime])->select('u.*','p.content','b.number','b.companyName','c.cat_name')
                ->orderBy('u.create_time','desc')
                ->paginate(10);
        }

        return $data;
    }


    /**
     * @param string $keyword 关键词
     * @param $where
     * @param $whereOr
     * @param $type
     * @return mixed
     * author hongwenyang
     * method description : B,C端支付待审核
     */
    public function OrderYinlian($keyword = "",$where,$whereOr,$type,$search_type = "",$startTime,$endTime,$nextTime=""){
        if(!empty($keyword)){

            if($_GET['type'] == 1){
                $key = [
                    ['order_id','like','%'.$_GET['keyword'].'%']
                ];
            }else if($_GET['type'] == 3){
                $key = [
                    ['c.cat_name','like','%'.$_GET['keyword'].'%']
                ];
            }else if($_GET['type'] == 2){
                $key = [
                    ['p.city','like','%'.$_GET['keyword'].'%']
                ];
            }

            $data = DB::table('user_apply as u')
                ->join('product as p','u.product_id','=','p.id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->join('business_user as b','b.id','=','p.business_id')
                ->where($where)->where($key)->orWhere($whereOr)->whereBetween('u.create_time',[$startTime,$endTime])->select('u.*','p.content','b.number','b.companyName','c.cat_name')
                ->orderBy('u.create_time','desc')->paginate(10);
        }else{

            $data = DB::table('user_apply as u')
                ->join('product as p','u.product_id','=','p.id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->join('business_user as b','b.id','=','p.business_id')
                ->where($where)->orWhere($whereOr)->whereBetween('u.create_time',[$startTime,$endTime])->select('u.*','p.content','b.number','b.companyName','c.cat_name')
                ->orderBy('u.create_time','desc')->paginate(10);
        }
        foreach($data as $k=>$v){
            $imgData = DB::table('yinlian')->where([
                'order_id'=>$v->order_id,
                'type'=>$type
            ])->value('img');

            $img = json_decode($imgData,true);

            $v->img = $img[0];
        }

        $data->appends(array(
            'type' => $type,
            'keyword'=>$keyword,
            'exTime'=>$nextTime,
        ));
        return $data;
    }

    public static function ReadMoreOrder($data){
        $returnData = array();
        // 基本信息
        if($data->order_type){
            // B端用户
            $UserInfo = BusinessUser::where('id',$data->user_id)->first();
            if(!empty($UserInfo)){
                $returnData['name'] = $UserInfo->companyName;
                $returnData['card_no'] = $UserInfo->idcard;
                $returnData['phone'] = $UserInfo->phone;
            }
        }else{
            // C端用户
            $UserInfo = User::findOrFail($data->user_id);
            if(!empty($UserInfo)){
                $returnData['name'] = $UserInfo->user_name;
                $returnData['card_no'] = $UserInfo->user_idcard;
                $returnData['phone'] = $UserInfo->phone;
            }
        }

        return $returnData;
    }
}
