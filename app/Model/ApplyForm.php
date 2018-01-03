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
use Mockery\Exception;

class ApplyForm extends Model{
    protected $table = 'apply_form';

    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 保存申请信息
     */

    public function applyData($data){
        //保存的数据不包括个人基本数据
        $title = ['name','sex','idCard','address','phone','merry','job','income'];

        if($data['applicantType'] == 1){
            $data['user_id'] = $data['business_id'];
        }
        foreach($title as $k=>$v){
            unset($data[$v]);
        }
        $SaveData = json_encode($data);
        $isHave = $this->where(['user_id'=>$data['user_id'],'cat_id'=>$data['cat_id'],'equipment_type'=>$data['applicantType']])->first();
        if(empty($isHave)){
            $return  = $this->insert([
                'user_id'=>$data['user_id'],
                'need_data'   =>$SaveData,
                'equipment_type'   =>$data['applicantType'],
                'cat_id'=>$data['cat_id']
            ]);
        }else{
            $return  = $this->where(['user_id'=>$data['user_id'],'cat_id'=>$data['cat_id'],'equipment_type'=>$data['applicantType']])->update([
                'need_data'   =>$SaveData,
            ]);
        }


        if($return){
            $retJson['code'] = 200;
            $retJson['msg']  = "需求信息保存成功!";
//            $retJson['property_data']  = empty($data) ? [] :$data;
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = "需求信息保存失败!";
        }

        return $retJson;
    }



    /**
     * @param $ApplyData
     * @return mixed
     * author hongwenyang
     * method description : 根据用户的匹配表单数据 匹配 产品
     * param :  company->公司名称 housekeeper->金融管家  company_phone->公司联系方式
     *          accrual->产品利息 money->金额  product_cycle->借款周期  lending_type->还本付息方式  lending_cycle->放款周期 property_cut->估值率
     *          other_money->其他费用（array  server:服务费  investigate:调查费 other:其他费用 ）
     *
     *          产品放款范围根据不同的大类进行选择字段名称
     *
     *
     *       有抵押：daikuanYear->按揭年限  daikuanMoney->贷款余额  daikuanTime->到期时间
     */

    public function apply($ApplyData,$type){

        if($ApplyData['applicantType'] == 1){
            $ApplyData['user_id'] = $ApplyData['business_id'];
        }
        if($ApplyData['sort'] == 0 && !empty($ApplyData['area'])){

            //保存房产证图片 正反面
            if(!empty($ApplyData['certificateA']) && !empty($ApplyData['certificateB'])){
                if(is_file($ApplyData['certificateA']) ){
                    $ApplyData['certificateA'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $ApplyData['certificateA']);
                }

                if(is_file($ApplyData['certificateB']) ){
                    $ApplyData['certificateB'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $ApplyData['certificateB']);
                }

            }

            if(isset($ApplyData['imgs'])){
                $save = $ApplyData['imgs'];
                foreach($save as $k=>$v){
                    $imgs[$k] = '/uploads/'.$v->store('img','img');
                }
                $ApplyData['imgs'] = json_encode($imgs);
            }

            //保存车辆登记图片
            if(!empty($ApplyData['cardj'])){
                if(is_file($ApplyData['cardj']) ){
                    $ApplyData['cardj'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $ApplyData['cardj']);
                }
            }
            //保存行驶证
            if(!empty($ApplyData['carjs'])){
                if(is_file($ApplyData['carjs']) ){
                    $ApplyData['carjs'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $ApplyData['carjs']);
                }
            }
            //保存驾驶证
            if(!empty($ApplyData['carxs'])){
                if(is_file($ApplyData['carxs']) ){
                    $ApplyData['carxs'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $ApplyData['carxs']);
                }
            }

            //商标注册证书
            if(!empty($ApplyData['Shangbiao'])){
                if(is_file($ApplyData['Shangbiao']) ){
                    $ApplyData['Shangbiao'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $ApplyData['Shangbiao']);
                }
            }

            $log = new Logs();
            $log->logs("保存合同类担保品数据",$ApplyData);

            $SaveData = json_encode($ApplyData);
            $s = DB::table('apply_form')->where(['user_id'=>$ApplyData['user_id'],'cat_id'=>$ApplyData['cat_id'],'equipment_type'=>$ApplyData['applicantType']])->update([
                'data'=>$SaveData
            ]);

        }

        //商品信息
        $ProductData = DB::table('product')
            ->join('business_user','business_user.id','=','product.business_id')
            ->where(['product.cat_id'=>$ApplyData['cat_id'],'product.is_del'=>0])
            ->select('product.*','business_user.number')
            ->get();


        if(count($ProductData) != 0){
            foreach($ProductData as $k=>$v){
                $CheckProduct[$k]               = json_decode($v->content,true);
                $CheckProduct[$k]['matching']   = 0;
                $CheckProduct[$k]['id']         = $v->id;
                $CheckProduct[$k]['business_id']= $v->business_id;
                $CheckProduct[$k]['is_buy']     = BusinessUser::where('id',$v->business_id)->value('is_buy');
                $CheckProduct[$k]['company']    = $v->number;
                $CheckProduct[$k]['score']      = round(DB::table('product_evaluate')->where(['product_id'=>$v->id])->avg('score'),1);
                $CheckProduct[$k]['count']      = DB::table('user_apply')->where(['product_id'=>$v->id])->count();
                $successApply                   = DB::table('user_apply')->where(['product_id'=>$v->id,'c_apply_status'=>8])->where("b_apply_status",">","5")->count();
                if($successApply == 0){
                    $CheckProduct[$k]['rate'] = "0";
//                    $CheckProduct[$k]['rate'] = "0"."%";
                }else{
                    $CheckProduct[$k]['rate'] = round(($successApply / $CheckProduct[$k]['count'])*100)  . "%";
                }
            }
            //需求信息 需要对比的字段内容  需求信息全部一样所以不变
            $title = ['money','product_cycle','accrual','lending_type','lending_cycle'];

            //获取担保品信息 需要对比的字段内容
            $pro_title = DB::table('check_key')->where(['cat_id'=>$ApplyData['cat_id']])->select('key')->get();
            if(!empty($pro_title)){
                foreach($pro_title as $k=>$v){
                    $check_title[$k] = $v->key;
                }
                $new_title = array_merge($check_title,$title);
            }
            //获取所有已保存的匹配数据
            $ContrastData = $this->where(['user_id'=>$ApplyData['user_id'],'cat_id'=>$ApplyData['cat_id'],'equipment_type'=>$ApplyData['applicantType']])->select('data','need_data')->get()->toArray();
            $ContrastDataA = json_decode($ContrastData[0]['data'],true);
            $ContrastDataB = json_decode($ContrastData[0]['need_data'],true);
            $ContrastData  = array_merge($ContrastDataA,$ContrastDataB);
//            dd($ContrastData);
            //获取除 基本信息以外的匹配字段数量
            $count = count($new_title)-5;
            //匹配 最低分数
            $match_score = DB::table('match_score')->where(['type'=>0])->value('match_score');
            $noChecked = array();
            //需求品平均分
            $needScoreAvg = 50/count($title);

            foreach($CheckProduct as $k=>$v){
                foreach($new_title as $k1=>$v1){
                    try{

                            // 如果不是如易类
                            if($v1 == 'money'){
                                if($v['money'] == "1-10万" || $v['money'] == "10-100万"){
                                    $money = explode('-',$v['money']);
                                    $ChineseMoney = explode('万',$money[1]);
                                    $checkMoney = $ChineseMoney[0];
                                }else if($v['money'] == "5000万以上"){
                                    $ChineseMoney = explode('万以上',$v['money']);
                                    $checkMoney = $ChineseMoney[0];
                                }else{
                                    $ChineseMoney = explode('万以内',$v['money']);
                                    $checkMoney = $ChineseMoney[0];
                                }

                                if($ContrastData[$v1]<= $checkMoney){
                                    if($ApplyData['cat_id'] != 37){
                                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                                    }else{
                                        $CheckProduct[$k]['matching'] += 20;
                                    }
                                }
                            }else if($v1 == 'accrual'){
                                //如果是利息则判断是否在区间内
                                $accrual = explode('-',$ContrastData['accrual']);
                                if($v[$v1] >= $accrual[0] && $v[$v1]<= $accrual[1] ){
                                    if($ApplyData['cat_id'] != 37){
                                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                                    }else{
                                        $CheckProduct[$k]['matching'] += 20;
                                    }
                                }
                            }else if($v1 == 'area'){
                                //如果B端产品填写不限  则不管在哪里都加 上对应的百分比分数  如果不是不限则  相同时才加上分数
                                if($ApplyData['cat_id'] != 37) {
                                    if ($v['area'] == "不限 不限 不限") {
                                        $CheckProduct[$k]['matching'] += 50 / $count;
                                    } else {
                                        if ($v['area'] == $ContrastData['area']) {

                                            $CheckProduct[$k]['matching'] += 50 / $count;

                                        }
                                    }
                                }
                            }else if($v1 == 'lending_cycle'){
                                $lending_cycle = explode('-',$ContrastData['lending_cycle']);
                                if($v['audit_time'] <= $lending_cycle[1]){
                                    if($ApplyData['cat_id'] != 37){
                                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                                    }else{
                                        $CheckProduct[$k]['matching'] += 20;
                                    }
                                }
                            }else if($v1 == 'product_cycle'){
                                //处理 product_cycle
                                // 用户填写的数据
                                $product_cycle = explode('个月',$ContrastData['product_cycle']);
                                if($ContrastData['product_cycle'] == "1-3个月"){
                                    $ContrastData['product_cycle'] = $product_cycle[1];
                                }else if($ContrastData['product_cycle'] == "6个月"){
                                    $ContrastData['product_cycle'] = $product_cycle[0];
                                }else if($ContrastData['product_cycle'] == "6-12个月"){
                                    $ContrastData['product_cycle'] = $product_cycle[1];
                                }else{
                                    $product_cycle = explode('个月以上',$ContrastData['product_cycle']);
                                    $ContrastData['product_cycle'] = $product_cycle[0];
                                }

                                //产品数据
                                $product_cycle_check = explode('个月',$v['product_cycle']);
                                if($v['product_cycle'] == "1-3个月"){
                                    $ContrastData['product_cycle'] = $product_cycle_check[1];
                                }else if($v['product_cycle'] == "6个月"){
                                    $ContrastData['product_cycle'] = $product_cycle_check[0];
                                }else if($v['product_cycle'] == "12个月"){
                                    $v['product_cycle'] = $product_cycle_check[1];
                                }else{
                                    $product_cycle_check = explode('个月以上',$v['product_cycle']);
                                    $v['product_cycle'] = $product_cycle_check[0];
                                }

                                if($v['product_cycle'] >= $ContrastData['product_cycle']){
                                    if($ApplyData['cat_id'] != 37){
                                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                                    }else{
                                        $CheckProduct[$k]['matching'] += 20;
                                    }
                                }
                            }else if($v1 == 'is_mortgage'){
                                if($ApplyData['cat_id'] != 37){
                                    if($v['is_mortgage'] == '不限'){
                                        $CheckProduct[$k]['matching'] += 50/$count;
                                    }else{
                                        if($ContrastData['mortgage'] == '无'){
                                            $CheckProduct[$k]['matching'] += 50/$count;
                                        }
                                    }
                                }
                            }else if($v1 == 'credit'){
                                if($ApplyData['cat_id'] != 37){
                                    if($v['credit'] == '1年内逾期超过3次或超过90天'){
                                        $CheckProduct[$k]['matching'] += 50/$count;
                                    }else if($v['credit'] = "1年内逾期少于3次且少于90天"){
                                        if($ContrastData['credit'] == '1年内逾期少于3次且少于90天'  || $ContrastData['credit'] == '信用良好无逾期'){
                                            $CheckProduct[$k]['matching'] += 50/$count;
                                        }
                                    }else{
                                        if($ContrastData['credit'] == '信用良好无逾期'){
                                            $CheckProduct[$k]['matching'] += 50/$count;
                                        }
                                    }
                                }
                            }else{
                                if(($v[$v1] == $ContrastData[$v1]) && in_array($v1,$title) && ($v1 != 'money') && (($v1 != 'accrual'))){
                                    // 如果是在需求品字段数组内 且不是 money 匹配成功则 每一项匹配成功 +10
                                    $CheckProduct[$k]['matching'] += $needScoreAvg;
                                }else if(($v[$v1] == $ContrastData[$v1] && (!in_array($v1,$title)) && (in_array($v1,$new_title)))){
                                    // 如果在担保品字段数组内 剩余的50分 按照担保字段的数量进行平均分配 匹配成功则 +平均分
                                    $CheckProduct[$k]['matching'] += 50/$count;
                                }else{
                                    if(!in_array($v1,$noChecked)){
                                        //保存未匹配项字段名称
                                        array_push($noChecked,$v1);
                                    }
                                }
                            }
                    }catch (\Exception $e){
                        $Log = new Logs();
                        $Log->logs('产品缺少参数',$v1);
                    }

                }
                $CheckProduct[$k]['matching'] = ceil($CheckProduct[$k]['matching']);

                //匹配低于 规定的去除
                if($CheckProduct[$k]['matching'] < $match_score){
                    unset($CheckProduct[$k]);
                }else{
                    $CheckProduct[$k]['matching'] = $CheckProduct[$k]['matching'];
                    if(!empty($ApplyData['city'])){
                        if($CheckProduct[$k]['district'] == $ApplyData['city']){
                            $SameCity[] = $CheckProduct[$k];
                        }else{
                            $DifferentCity[] = $CheckProduct[$k];
                        }
                    }
                }
            }

            if(!empty($ApplyData['city'])){
                // 根据用户定位对数据进行重新组合
                if(!empty($SameCity) && (empty($DifferentCity))){

                    $SameData       = getIsBuy($SameCity,$ApplyData['sort']);
                    $CheckProduct   = $SameData;
                }else if((empty($SameCity)) && (!empty($DifferentCity))){

                    $DifferentData  = getIsBuy($DifferentCity,$ApplyData['sort']);
                    $CheckProduct   = $DifferentData;
                }else if(!empty($DifferentCity) && !empty($SameCity)){

                    $SameData       = getIsBuy($SameCity,$ApplyData['sort']);
                    $DifferentData  = getIsBuy($DifferentCity,$ApplyData['sort']);
                    $CheckProduct   = array_merge($SameData,$DifferentData);
                }
            }else{

                $CheckProduct = getIsBuy($CheckProduct,$ApplyData['sort']);
            }

        }else{
            $CheckProduct = "";
        }
        $use_title = ['lending_type','property','accrual','product_cycle','is_home','is_home','company','matching','score','id','count','rate','order_id','other','audit_time','other_need'];
        if($type == 0){
            if($s){
                $retArray['code'] = 200;
                $retArray['msg'] = "保存成功";
            }else{
                $retArray['code'] = 200;
                $retArray['msg'] = "数据未改变";
            }
        }else{
            if(empty($CheckProduct)){
                $retArray['code'] = 400;
                $retArray['data'] = [];
            }else{
                foreach ($CheckProduct as $k=>$v){
                    foreach ($v as $k1=>$v1){
                        if(!in_array($k1,$use_title)){
                            unset($CheckProduct[$k][$k1]);
                        }
                    }
                }
                $retArray['code'] = 200;
                $retArray['data'] = $CheckProduct;
                $retArray['msg'] = "获取数据成功";
            }
        }

        return $retArray;
    }


    /**
     * @param $data
     * @return array|mixed
     * author hongwenyang
     * method description : 如果结算的时候担保品数据丢失了，先拿存储的数据抵挡一下
     */
    public static function getData($data){
        $get = array();
        // 查找用户的担保品数据
        $productId= UserApply::where('order_id',$data['order_id'])->first();
        // 查找分类
        $productCat = Product::where('id',$productId->product_id)->value('cat_id');
        // 查找用户的担保品数据
        if($data['applicantType']){
            $get = json_decode(ApplyForm::where([
                'user_id'=>$productId->user_id,
                'cat_id'=>$productCat,
                'equipment_type'=>$data['applicantType']
            ])->value('data'),true);
        }

        return $get;
    }
}