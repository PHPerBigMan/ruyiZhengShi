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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApplyBasic extends Model{
    protected $table = 'apply_basic_form';

    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 保存用户基本资料
     * param : 基础资料  name-姓名  sex-性别  idCard-身份证号 address-家庭住址  phone-手机号 merry-婚姻状况  job-工作  income-年收入
     */

    public function applyBasic($data){
        $title = ['name','sex','idCard','address','phone','merry','job','income','sf','jh'];
        if(empty($data['sf'])){
            $data['sf'] = [];

        }
        if(empty($data['jh'])){
            $data['jh'] = [];
        }
        if($data['applicantType'] == 1){
            $data['user_id'] = $data['business_id'];
            $title = ['name','phone','number','share'];
        }

        foreach($title as $k=>$v){
            //基本资料
            $Basic[$v] = $data[$v];
        }

        //保存 身份证和结婚证
        if(!empty($data['sf'])){
            foreach ($data['sf'] as $k=>$v){
                if(is_file($v)){
                    $Basic['sf'][$k] = '/uploads/'.Storage::disk('fcz')->put('fcz', $v);
                }
            }
        }

        if(!empty($data['jh'])){
            foreach ($data['jh'] as $k=>$v){
                if(is_file($v)){
                    $Basic['jh'][$k] = '/uploads/'.Storage::disk('fcz')->put('fcz', $v);
                }
            }
        }
        if(isset($Basic['share'])){
            // 说明：APP端返回的是 中文字符串 不利于后期计算 所以保存的时候使用 int
            $Basic['share'] =  DB::table('match_score')->where(['type'=>2])->value('match_score');
        }
        $Basic = json_encode($Basic);
        $is_company = 0;
        $CompanyCatId = [35,36,62,63,64,67,68,70,65,66,71,69];
        if(in_array($data['cat_id'],$CompanyCatId)){
            $is_company = 1;
        }


        $isHave = $this->where(['user_id'=>$data['user_id'],'is_company'=>$is_company,'type'=>$data['applicantType']])->first();
        if(empty($isHave)){
            //保存基本资料
           $s =  $this->insert([
                'user_id'=>$data['user_id'],
                'data'=>$Basic,
                'type'=>$data['applicantType'],
                'is_company'=>$is_company
            ]);
        }else{
            $s = $this->where(['user_id'=>$data['user_id'],'type'=>$data['applicantType'],'is_company'=>$is_company])->update([
                'data'=>$Basic
            ]);
        }

        //查看是否之前保存过需求资料


        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = "基本数据保存成功!";
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = "基本数据保存失败!";
        }

        return $retJson;
    }

    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 保存企业贷资料
     */

    public function applCompanyBasic($data){

        $title = ['companyName','companyFa','companyAddress','companyType','companyZhu','companyYin','companyXu','companyMoney','companyCount','companyList','companyGd','companyLd','companyQt'];
        if($data['applicantType'] == 1){
            $data['user_id'] = $data['business_id'];
            $title = ['companyName','companyFa','companyAddress','companyType','companyZhu','companyYin','companyXu','companyMoney','companyCount','companyList','companyGd','companyLd','companyQt','share'];
        }

        foreach($title as $k=>$v){
            //基本资料
            $Basic[$v] = $data[$v];
            if(!empty($data['companyYin'])){
                if(is_file($data['companyYin']) ){
                    $Basic['companyYin'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $data['companyYin']);
                }
            }

            if(!empty($data['companyXu'])){
                if(is_file($data['companyXu']) ){
                    $Basic['companyXu'] = '/uploads/'.Storage::disk('fcz')->put('fcz', $data['companyXu']);
                }
            }
        }

        $Basic = json_encode($Basic);
        $isHave = $this->where(['user_id'=>$data['user_id'],'is_company'=>1])->first();
        if(empty($isHave)){
            //保存基本资料
            $s =  $this->insert([
                'user_id'=>$data['user_id'],
                'data'=>$Basic,
                'type'=>$data['applicantType'],
                'is_company'=>1
            ]);
        }else{
            $s = $this->where(['user_id'=>$data['user_id'],'type'=>$data['applicantType'],'is_company'=>1])->update([
                'data'=>$Basic
            ]);
        }

        //查看是否之前保存过需求资料


        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = "基本数据保存成功!";
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = "基本数据保存失败!";
        }

        return $retJson;
    }


    /**
     * @param $data 查询依据
     * @param $type 类型
     * @return mixed
     * author hongwenyang
     * method description :
     * 根据 $type 查询不同类型的数据
     */

    public function SearchApplyData($data,$type){
        if($data['applicantType'] == 1){
            $data['user_id'] = $data['business_id'];
        }
        if($type == 1){
            $is_company = 0;
            //如果 分类是企业贷 下的二级分类 则显示用户填写的企业信息
            $arr = [35,36,62,63,64,65,66,67,68,69,71];
            if(in_array($data['cat_id'],$arr)){
                $is_company = 1;
            }
            $retData = DB::table('apply_basic_form')->where(['user_id'=>$data['user_id'],'type'=>$data['applicantType'],'is_company'=>$is_company])->select('data')->first();

            if($data['applicantType'] == 1){
                //企业信息
                $company = DB::table('business_user')->where(['id'=>$data['user_id']])->first();
                //共享条件
//                dd($retData);
//                $share = DB::table('match_score')->where(['type'=>2])->value('match_score');
                $share = "借款金额 1%";
                if(!empty($retData)){

                    $retData->data = json_decode($retData->data);
                    $retData->number = $company->number;
                    $retData->phone = $company->companyHousePhone;
                    $retData->share = $share;
                    $retData->name =  $retData->data->name;
                }else{
                    $retData['share'] = $share;
                    $retData['phone'] = $company->companyHousePhone;
                    $retData['number'] = $company->number;;
                    $retData['name'] = "";
                    $retData = json_decode(json_encode($retData));
                }
            }else{
               if(empty($retData)){
                   $retData = [];
               }else{
                   $retData = json_decode($retData->data);
               }
               }

        }else if($type == 2){

            $retData = json_decode(DB::table('apply_form')->where(['user_id'=>$data['user_id'],'cat_id'=>$data['cat_id'],'equipment_type'=>$data['applicantType']])->value('need_data as data'));

        }else{
            $retData = json_decode(DB::table('apply_form')->where(['user_id'=>$data['user_id'],'cat_id'=>$data['cat_id'],'equipment_type'=>$data['applicantType']])->value('data'),true);
        }

        $retJson['code'] = 200;
        $retJson['msg']  = "获取成功";
        $retJson['data'] = empty($retData) ? "" : $retData;

        return $retJson;
    }


    /**
     * @param $data
     * @param $type
     * @return mixed
     * author hongwenyang
     * method description : 获取企业贷数据资料
     */
    public function SearchCompanyData($data,$type){
        if($data['applicantType'] == 1){
            $data['user_id'] = $data['business_id'];
        }
        if($type == 1){
            $retData = DB::table('apply_basic_form')->where(['user_id'=>$data['user_id'],'type'=>$data['applicantType'],'is_company'=>1])->first();

            if($data['applicantType'] == 1){
                if(empty($retData)){
                    //企业信息
                    $company = DB::table('business_user')->where(['id'=>$data['user_id']])->first();
                    //共享条件
                    $share = DB::table('match_score')->where(['type'=>2])->value('match_score');
//                    $retData['companyName'] = $company->companyName;
//                    $retData['companyFa'] = $company->companyFa;
//                    $retData['companyAddress'] = $company->companyAddress;
//                    $retData['companyType'] = $company->companyType;
//                    $retData['companyZhu'] = $company->companyZhu;
//                    $retData['companyYin'] = $company->companyYin;
//                    $retData['companyXu'] = $company->companyXu;
//                    $retData['companyMoney'] = $company->companyMoney;
//                    $retData['companyCount'] = $company->companyCount;
//                    $retData['companyList'] = $company->companyList;
//                    $retData['companyGd'] = $company->companyGd;
//                    $retData['companyLd'] = $company->companyLd;
//                    $retData['companyQt'] = $company->companyQt;
                    $retData['share'] = $share;
                }
            }
        }else if($type == 2){
            $retData = json_decode(DB::table('apply_form')->where(['user_id'=>$data['user_id'],'cat_id'=>$data['cat_id'],'equipment_type'=>$data['applicantType']])->value('need_data as data'),true);
        }else{
            $retData = json_decode(DB::table('apply_form')->where(['user_id'=>$data['user_id'],'cat_id'=>$data['cat_id'],'equipment_type'=>$data['applicantType']])->value('data'),true);
        }

//        dd($retData);
        if(!empty($retData->data)){
            $retData = json_decode($retData->data,true);
        }

        $retJson['code'] = 200;
        $retJson['msg']  = "获取成功";
        $retJson['data'] = empty($retData) ? "" : $retData;

        return $retJson;
    }
}