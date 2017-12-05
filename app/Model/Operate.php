<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Operate extends Model
{
    /**
     * @param $businessId
     * @return mixed
     * author hongwenyang
     * method description : 获取今日有效订单
     */

    public function orderCount($businessId){
        $t = time();
        $MIN = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $MAX = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        //今天有效订单
        $OrderCount = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$businessId,
            ])
            ->whereIn('u.c_apply_status',[4,7,8])
            ->whereIn('u.b_apply_status',[2,3,4,6,7])
            ->whereBetween('u.create_time',[$MIN,$MAX])->count();
        return $OrderCount;
    }

    /**
     * @param $businessId
     * @return int
     * author hongwenyang
     * method description : 本月订单总额
     */

    public function Month($businessId){
        $beginThismonth =   mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth   =   mktime(23,59,59,date('m'),date('t'),date('Y'));
        $Order = DB::table('user_apply as u')
            ->join('product as p','u.product_id','=','p.id')
            ->where([
                'p.business_id'=>$businessId,
            ])
            ->whereIn('u.c_apply_status',[8])
            ->whereIn('u.b_apply_status',[6])
            ->whereBetween('u.create_time',[$beginThismonth,$endThismonth])
            ->select('u.user_id','u.product_id','p.cat_id','u.order_type')
            ->get();
        $money =0;
        foreach($Order as $k=>$v){
            $applyData = json_decode(DB::table('apply_form')->where([
                'user_id'=>$v->user_id,
                'cat_id' =>$v->cat_id,
                'equipment_type'=>$v->order_type
            ])->value('need_data'),true);
            $money += $applyData['money'];
        }
        return $money;
    }
}
