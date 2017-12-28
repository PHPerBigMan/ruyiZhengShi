<?php

namespace App\Http\Controllers\Back;
use App\Model\BusinessUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * @param Request $request
     * author hongwenyang
     * method description : 所有导出excel的方法
     */

    public function excel(Request $request){
        $export = $request->except(['s']);

        if($export['exl'] == "order"){
            $data = $this->getOrder($export);
            $this->orderExport($data);
        }else if($export['exl'] == "userB"){
            // 导出B端用户信息

            $data = $this->getUser($export,2);
            
            $this->UserExport($data,2);
        }

    }

    /**
     * @param $export
     * @return array
     * author hongwenyang
     * method description : 根据条件查询订单数据
     */

    public function getOrder($export){
        $key = array();
        switch ($export['type']){
            // B端待审核订单
            case 1:
                $where = [
                    'b_apply_status'=>3
                ];
                break;
            // C端待审核订单
            case 0:
                $where = [
                    'c_apply_status'=>1
                ];
                break;
            //审核已通过订单
            case 2:
                $where = [
                    'c_apply_status'=>4,
                    'b_apply_status'=>4
                ];
                break;
            //审核未通过订单
            case 3:
                $where = [
                    'c_apply_status'=>9,
                ];
                break;
            //已完成
            case 4:
                $where = [
                    'b_apply_status'=>7
                ];
                break;
            //C端用户取消订单
            case 5:
                $where = [
                    'c_apply_status'=>2
                ];
                break;
            //C端用户取消订单
            case 6:
                $where = [
                    'b_apply_status'=>1
                ];
                break;
        }

        if(!empty($export['keyword'])){
            if($export['selectType'] == 1){
                // 订单号
                $key = [
                    [ 'order_id','like','%'.$export['keyword'].'%']
                ];
            }else if($export['selectType'] == 3){
                //产品分类

                $key = [
                    [ 'c.cat_name','like','%'.$export['keyword'].'%']
                ];
            }else{
                //地区
                $key = [
                    [ 'p.city','like','%'.$export['keyword'].'%']
                ];
            }
        }
        // 时间搜索
        if(!empty($export['time'])){
            $time = explode(' - ',$export['time']);
            $startTime = strtotime($time[0]);
            $endTime   = strtotime($time[1]);

            $data = DB::table('user_apply as u')
                ->join('product as p','u.product_id','=','p.id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->join('business_user as b','b.id','=','p.business_id')
                ->where($where)->where($key)->whereBetween('u.create_time',[$startTime,$endTime])->select('u.*','p.content','b.companyName','b.number','c.cat_name as cat')->get();
        }else{
            $data = DB::table('user_apply as u')
                ->join('product as p','u.product_id','=','p.id')
                ->join('product_cat as c','c.id','=','p.cat_id')
                ->join('business_user as b','b.id','=','p.business_id')
                ->where($where)->where($key)->select('u.*','p.content','c.cat_name as cat','b.companyName','b.number')->get();
        }

        if(!$data->isEmpty()){
            return $data;
        }else{
           return [];
        }
    }

    public function getUser($export,$type){
        $where = [];
        if(!empty($export['keyword'])){
           if($type == 1){
               // C 端用户
           }else{
               // B 端用户
               $companyName = [
                   'companyName'=>$export['keyword']
               ];
               $where =  array_merge($where,$companyName);
           }
        }

        if($export['selectType'] != 3){
            $is_pass = [
                'is_pass'=>$export['selectType']
            ];
            $where = array_merge($is_pass,$where);
        }

        if($export['time'] != ""){
            $time = explode(' - ',$export['time']);
            $whereBetween = [$time[0],$time[1]];
        }else{
            $whereBetween = ["1997-01-01","2999-12-31"];
        }
        if($type == 1){

        }else{
            $data = BusinessUser::where($where)->whereBetween('create_time',$whereBetween)->get();
        }

        return $data;
    }
    /**
     * @param $data
     * @return string
     * author hongwenyang
     * method description :导出订单excel
     */

    public function orderExport($data){

        if(empty($data)){
//            return view('errors.404');
        }else{
            $exportData = [
                ['订单号','产品编号','订单总额(万元)','订单类型','产品所属企业','产品所属企业编号','产品分类','创建时间']
            ];
            foreach ($data as $k=>$v){
                $exportData[$k+1][0] = $v->order_id;
                $pNumber = json_decode($v->content);
                $exportData[$k+1][1] = $pNumber->pNumber;
                $exportData[$k+1][2] = $v->order_count;
                $exportData[$k+1][3] = $v->order_type == 0 ? "个人订单" : "共享订单";
                $exportData[$k+1][4] = $v->companyName;
                $exportData[$k+1][5] = $v->number;
                $exportData[$k+1][6] = $v->cat;
                $exportData[$k+1][7] = date("Y-m-d",$v->create_time);
            }

            Excel::create('订单数据表',function($excel) use ($exportData){
                $excel->sheet('订单', function($sheet) use ($exportData){
                    $sheet->rows($exportData,function ($row){
                    });
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                    ));
                });
            })->export('xls');
        }
    }


    public function UserExport($data,$type){
        if(empty($data)){
//            return view('errors.404');
        }else{
            $exportData = [
                ['企业编号','身份证','公司名称','企业代码','企业地址','企业法人','法人联系电话','金融管家','管家联系电话'
                ,'如易金币','审核状态']
            ];
            foreach ($data as $k=>$v){
                $exportData[$k+1][0] = $v->number;
                $exportData[$k+1][1] = $v->idcard;
                $exportData[$k+1][2] = $v->companyName;
                $exportData[$k+1][3] = $v->companyCode;
                $exportData[$k+1][4] = $v->companyAddress;
                $exportData[$k+1][5] = $v->companyLegal;
                $exportData[$k+1][6] = $v->phone;
                $exportData[$k+1][7] = $v->companyHouse;
                $exportData[$k+1][8] = $v->companyHousePhone;
                $exportData[$k+1][9] = $v->integral;
                $exportData[$k+1][10] = $v->is_pass == 0 ? "审核未通过" : $v->is_pass == 1 ? "审核通过" : "审核中";
            }

            Excel::create('商户端用户数据',function($excel) use ($exportData){
                $excel->sheet('商户端用户数据', function($sheet) use ($exportData){
                    $sheet->rows($exportData,function ($row){
                    });
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                    ));
                });
            })->export('xls');
        }
    }
}
