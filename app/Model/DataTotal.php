<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataTotal extends Model
{
    protected $beginToday,$endToday,$beginNowMonth,$endNowMonth,$beginYear,$endYear;
    public function __construct()
    {
        $this->beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $this->endToday   =   mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        // 本月
        $this->beginNowMonth = mktime(0,0,0,date('m'),1,date('Y'));
        $this->endNowMonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        // 今年
        $this->beginYear = mktime(0,0,0,1,1,date("Y",time()));
        $this->endYear   = mktime(23,59,59,12,31,date("Y",time()));
    }

    /**
     * @param $CatFirstId
     * @param string $totalType
     * @return array
     * author hongwenyang
     * method description : 根据 产品分类统计不同时间节点的数据
     */
    public  function GetTotal($CatFirstId,$totalType = "today"){
        $data = array();

        switch ($totalType){
            case "today":
                // 今日时间戳
                $begin = $this->beginToday;
                $end = $this->endToday;
                break;
            case "month":
                // 本月时间戳
                $begin = $this->beginNowMonth;
                $end = $this->endNowMonth;
                break;

        }
        // 根据一级分类查找对应的二级分类
        $SecondCatId = ProductCat::where('p_id',$CatFirstId)->select('id')->get()->toArray();
        foreach($SecondCatId as $k=>$v){
            // 根据不同分类查找数据
            $data[$k] = UserApply::join('product as p','p.id','user_apply.product_id')
                        ->where('p.cat_id',$v['id'])->whereBetween('user_apply.create_time',[$begin,$end])
                        ->valid()
                        ->sum('user_apply.order_count');
        }

        return $data ;
    }


    /**
     * @param $CatFirstId
     * @param string $totalType
     * @return array
     * author hongwenyang
     * method description : 根据月度统计数据
     */
    public  function GetTotalAll($CatFirstId,$totalType = "Allmonth",$type = 1){
        $data = array();
        $allTime = array();
        // 月度统计 获取每个月的时间戳
        $year = date('Y');
        // 所有月份
        $AllMonth = [1,2,3,4,5,6,7,8,9,10,11,12];
        foreach ($AllMonth as $k=>$v){
            $month = $year."-".$v;
            // 起始时间
            $allTime[$k]['begin'] = strtotime($month);
            // 结束时间
            $allTime[$k]['end'] = mktime(23, 59, 59, date('m', strtotime($month))+1, 00);

            $NewAllMonth[$k] = $v.'月';
        }
        // 查询数据
        if($type){
            foreach ($allTime as $k=>$v){
                $data[$k] = UserApply::whereBetween('user_apply.create_time',[$v['begin'],$v['end']])
                    ->valid()->sum('order_count');
            }
        }else{
            foreach ($allTime as $k=>$v){
                $data[$k] = UserApply::whereBetween('user_apply.create_time',[$v['begin'],$v['end']])
                    ->join('product as p','p.id','user_apply.product_id')
                    ->valid()->where('province',$totalType)->sum('order_count');
            }
        }
        $j = [
            'title'=>$NewAllMonth,
            'data'=>$data
        ];
        return $j;
    }

    /**
     * @return array
     * author hongwenyang
     * method description : 年度统计
     */
    public function GetTotalYear(){
        $data = UserApply::whereBetween('user_apply.create_time',[$this->beginYear,$this->endYear])
            ->valid()->sum('order_count');

        $data = [
            0=>$data
        ];
        return $data;
    }


    /**
     * @return mixed
     * author hongwenyang
     * method description : 根据销量获得省份排行榜
     */
    public function GetArea(){
        // 根据销量获取城市排行
        $data = UserApply::join('product','product.id','=','user_apply.product_id')
            ->valid()->select(DB::raw('sum(order_count) as total_money'),'product.*')->groupBy('product.province')
            ->orderBy('total_money','desc')->get();

        return $data;
    }
}
