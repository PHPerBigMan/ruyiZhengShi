<?php
/**
 * Created by PhpStorm.
 * User: hwy
 * Date: 2017/9/18
 * Time: 11:54
 */
use App\Model\Logs;
use App\Model\OrderApplyForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @param $data
 * @return array
 * author hongwenyang
 * method description : 返回数据
 */

function returnData($data){
    if(empty($data)){
        $data = "";
        $retJson['code'] = 400;
        $retJson['msg'] = "";
    }else{
        $retJson['code'] = 200;
        $retJson['msg'] = "获取数据成功";
    }

    $j = [
        'data'=>$data,
        'code'=>$retJson['code'],
        'msg'=>$retJson['msg'],
    ];

    return $j;
}


/**
 * @param $data
 * @return array
 * author hongwenyang
 * method description : 返回消息数据
 */

function returnDataMessage($data,$count){
    if(empty($data)){
        $retJson['code'] = 400;
        $retJson['msg'] = "";
    }else{
        $retJson['code'] = 200;
        $retJson['msg'] = "获取数据成功";
    }

    $j = [
        'data'=>$data,
        'count'=>$count,
        'code'=>$retJson['code'],
        'msg'=>$retJson['msg'],
    ];

    return $j;
}


/**
 * @param $data
 * @return array
 * author hongwenyang
 * method description : 返回用户积分
 */

function returnIntegral($data){
    if(empty($data)){
        $retJson['code'] = 400;
        $retJson['msg'] = "";
    }else{
        $retJson['code'] = 200;
        $retJson['msg'] = "获取数据成功";
    }

    $j = [
        'Integral'=>$data,
        'code'=>$retJson['code'],
        'msg'=>$retJson['msg'],
    ];

    return $j;
}

/**
 * @param $data  查询后的结果
 * @param $title 需要返回的接口字段数据
 * @return mixed
 * author hongwenyang
 * method description : 返回产品列表信息
 */

function productData($data,$title){
//dd($data);
    foreach ($data as $k=>$v){
        $returnData[$k]                         = json_decode($v->content,true);
        $returnData[$k]['lending_cycle']        =  $returnData[$k]['audit_time'];
        if(!empty($v->data)){
            $phone = json_decode($v->data,true);
            //如果是普通订单联系方式为用户基本信息中填写的内容  如果是共享订单 联系方式则需要查询企业联系方式
            if($v->order_type == 0 && $v->is_company == 0){
                $returnData[$k]['phone'] = $phone['phone'];
            }else if($v->order_type == 0 && $v->is_company == 1){
                $returnData[$k]['phone'] = "";
            }else{
                $returnData[$k]['phone'] = DB::table('business_user')->where([
                    'id'=>$v->user_id
                ])->value('phone');
            }
            $returnData[$k]['first_cat_name'] = DB::table('product_cat')->where([
                'id'=>DB::table('product_cat')->where([
                    'id'=>$v->cat_id
                ])->value('p_id')
            ])->value('cat_name');
        }
        if(!empty($v->companyName)){
            $returnData[$k]['company']              = $v->companyName;
        }else{
            $returnData[$k]['company']              = $v->number;
        }
        if(!empty($v->order_id)){
            $returnData[$k]['order_id']             = $v->order_id;
        }
        $returnData[$k]['cat_name']                 = $v->cat_name;
        $returnData[$k]['create_time']              = date('Y-m-d',$v->create_time);
        $returnData[$k]['c_is_evaluate']            = $v->c_is_evaluate;
        $returnData[$k]['c_apply_status']           = $v->c_apply_status;
        $returnData[$k]['order_count']              = $v->order_count;
        $returnData[$k]['number']                   = $v->number;
        $returnData[$k]['cat_id']                   = $v->cat_id;
    }
    $emptyData = [];
    if(empty($returnData)){
        $retArray['code'] = 400;
        $retArray['data'] = $emptyData;
        $retArray['msg']  = '';
    }else{
        foreach ($returnData as $k=>$v){
            foreach ($v as $k1=>$v1){
                if(!in_array($k1,$title)){
                    unset($returnData[$k][$k1]);
                }
            }
        }

        $retArray['code'] = 200;
        $retArray['msg']  = '获取数据成功';
        $retArray['data'] = $returnData;
    }

    return $retArray;
}

/**
 * @param $data 接收数据
 * @param $type
 * @return mixed
 * author hongwenyang
 * method description : 根据用户申请信息和产品信息进行数据的匹配
 */

function ProductCheck($data,$type){

    $title = ['money','product_cycle','accrual','lending_type','lending_cycle'];
    $titleDesc= [
        'money'         =>'借款金额',
        'product_cycle' =>'借款周期',
        'accrual'       =>'利息',
        'lending_cycle' =>'期望放款周期',
        'lending_type'  =>'还款方式',
    ];
    //获取担保品信息 需要对比的字段内容
    if($type == 0){
        $cat_id = $data['cat_id'];
    }else if($type == 1){
        $cat_id = DB::table('product')->where(['id'=>$data['product_id']])->value('cat_id');
    }
    $pro_title = DB::table('check_key')->where(['cat_id'=>$cat_id])
        ->select('key')->get();

    $check_title = [];
    //结合所有需要匹配的字段名称
    if(!empty($pro_title)){
        foreach($pro_title as $k=>$v){
            $check_title[$k] = $v->key;
        }
        $new_title = array_merge($check_title,$title);
    }

    $ProductData = DB::table('product')
        ->join('business_user','business_user.id','=','product.business_id')
        ->where(['product.id'=>$data['product_id']])
        ->select('product.*','business_user.number')
        ->get();

    foreach($ProductData as $k=>$v){
        $CheckProduct[$k]               = json_decode($v->content,true);
        $CheckProduct[$k]['matching']   = 0;
        $CheckProduct[$k]['id']         = $v->id;
        $CheckProduct[$k]['company']    = $v->number;
    }

    //合并 需求资料和担保品资料
    $ContrastData = DB::table('apply_form')->where(['user_id'=>$data['user_id'],'cat_id'=>$cat_id,'equipment_type'=>$data['equipment_type']])->select('data','need_data')->first();

    $ContrastDataA = json_decode($ContrastData->data,true);
    $ContrastDataB = json_decode($ContrastData->need_data,true);
    $ContrastData  = array_merge($ContrastDataA,$ContrastDataB);

    $count = count($new_title)-5;
    $noChecked = array();
    //需求品平均分
    $linshiProductId = [45,46,47,48,49,50];

    $needScoreAvg = 50/count($title);
    foreach($CheckProduct as $k=>$v){
        foreach($new_title as $k1=>$v1){
            try{
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
                    if(isset($ContrastData[$v1])){
                        if($ContrastData[$v1]<= $checkMoney){
                            $CheckProduct[$k]['matching'] += $needScoreAvg;
                        }else{
                            array_push($noChecked,$v1);
                        }
                    }else{
                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                    }

                }else if($v1 == 'accrual'){
                    //如果是利息则判断是否在区间内
                    $accrual = explode('-',$ContrastData['accrual']);
                    if($v[$v1] >= $accrual[0] && $v[$v1]<= $accrual[1] ){
                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                    }else{
                        array_push($noChecked,$v1);
                    }
                }else if($v1 == 'area'){
                    //如果B端产品填写不限  则不管在哪里都加 上对应的百分比分数  如果不是不限则  相同时才加上分数
                    if($v['area'] == "不限 不限 不限"){
                        $CheckProduct[$k]['matching'] += 50/$count;
                    }else{
                        if($v['area'] == $ContrastData['area']){
                            $CheckProduct[$k]['matching'] += 50/$count;
                        }else{
                            array_push($noChecked,$v1);
                        }
                    }
                }else if($v1 == 'lending_cycle'){
                    $lending_cycle = explode('-',$ContrastData['lending_cycle']);
                    if($v['audit_time'] <= $lending_cycle[1]){
                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                    }else{
                        array_push($noChecked,$v1);
                    }
                    //处理 lending_cycle的数据
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
                        $CheckProduct[$k]['matching'] += $needScoreAvg;
                    }else{
                        array_push($noChecked,$v1);
                    }
                    //处理 lending_cycle的数据
                }else if($v1 == 'is_mortgage'){
                    if($v['is_mortgage'] == '不限'){
                        $CheckProduct[$k]['matching'] += 50/$count;
                    }else{
                        if($ContrastData['mortgage'] == '无'){
                            $CheckProduct[$k]['matching'] += 50/$count;
                        }else{
                            array_push($noChecked,$v1);
                        }
                    }
                }else if($v1 == 'credit'){
                    if($v['credit'] == '1年内逾期超过3次或超过90天'){
                        $CheckProduct[$k]['matching'] += 50/$count;
                    }else if($v['credit'] = "1年内逾期少于3次且少于90天"){
                        if($ContrastData['credit'] == '1年内逾期少于3次且少于90天'  || $ContrastData['credit'] == '信用良好无逾期'){
                            $CheckProduct[$k]['matching'] += 50/$count;
                        }else{
                            array_push($noChecked,$v1);
                        }
                    }else{
                        if($ContrastData['credit'] == '信用良好无逾期'){
                            $CheckProduct[$k]['matching'] += 50/$count;
                        }else{
                            array_push($noChecked,$v1);
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
                $Log->logs('产品缺少参数',88);
            }

        }

        //申请的产品 最低匹配分数
        $match_score = DB::table('match_score')->where(['type'=>$type])->value('match_score');
        $CheckProduct[$k]['matching'] = ceil($CheckProduct[$k]['matching']);

        //判断匹配的分数是否低于要求
        if($CheckProduct[$k]['matching'] < $match_score){
            foreach($noChecked as $k=>$v){
                if(!in_array($v,$title)){
                    $noCheckedDesc = DB::table('check_key')->where(['cat_id'=>$ContrastData['cat_id'],'key'=>$v])->value('desc');
                }else{
                    $noCheckedDesc = $titleDesc[$v];
                }
                if($v != 'is_mortgage'){
                    $noCheckedTitle[$k]['title'] = $noCheckedDesc;
                    $noCheckedTitle[$k]['content'] = $ContrastData[$v];
                }
            }

            sort($noCheckedTitle);
            $retJson['code'] = 400;
            $retJson['data'] = $noCheckedTitle;
            $retJson['match_score'] = $match_score;
            $retJson['msg']  = "不符合申请条件";
        }else{
            $retJson['code'] = 200;
            $retJson['data'] = [];
            $retJson['msg']  = "符合申请条件";
        }
    }

    return $retJson;
}

/**
 * @param $s
 * @return mixed
 * author hongwenyang
 * method description : 反馈操作状态
 */

function returnStatus($s){
    if($s){
        $retJson['code'] = 200;
        $retJson['msg']  = "操作成功";
    }else{
        $retJson['code'] = 404;
        $retJson['msg']  = "操作失败";
    }

    return $retJson;
}

function returnStatusBack($s){
    if($s){
        $retJson['code'] = 200;
        $retJson['msg']  = "操作成功";
    }else{
        $retJson['code'] = 404;
        $retJson['msg']  = "数据未修改";
    }

    return $retJson;
}

/**
 * @param $product_id
 * @return mixed
 * author hongwenyang
 * method description : 获取拒单理由
 */

function read($product_id){
    $ProductData = DB::table('product')
        ->join('business_user','business_user.id','=','product.business_id')
        ->join('product_cat','product_cat.id','=','product.cat_id')
        ->where(['product.id'=>$product_id])->select('product.content','product_cat.cat_name','business_user.number','product.create_time')->first();


    $ProductData->content = json_decode($ProductData->content);
    //放款周期为  产品的审核时间
    $ProductData->content->lending_cycle = $ProductData->content->audit_time;
    $ProductData->content->create_time = $ProductData->create_time;

    $retJson = $ProductData;

    return $retJson;
}


/**
 * @param $product_id
 * @param $user_id
 * @return mixed
 * author hongwenyang
 * method description : B端订单详情
 */

function OrderRead($product_id,$user_id,$order_id){
    $ProductData = DB::table('product')
        ->join('business_user','business_user.id','=','product.business_id')
        ->join('product_cat','product_cat.id','=','product.cat_id')
        ->join('apply_form as a','a.cat_id','=','product_cat.id')
        ->where(['product.id'=>$product_id,'a.user_id'=>$user_id])->select('product.content','product_cat.cat_name','business_user.number','a.need_data','a.data')->first();

    $OrderForm = OrderApplyForm::where('order_id',$order_id)->select('need_data','data')->first();

    $productData   = json_decode($ProductData->content);
    $needData   = json_decode($OrderForm->need_data);
    $data       = json_decode($OrderForm->data);

    $ProductData->content = $data;
    $ProductData->content->accrual          = $needData->accrual;
    $ProductData->content->is_issue         = $needData->is_issue;
    $ProductData->content->lending_cycle    = $needData->lending_cycle;
    $ProductData->content->lending_type     = $needData->lending_type;
    $ProductData->content->money            = $needData->money ;
    $ProductData->content->product_cycle    = $needData->product_cycle;
    //这里可能会有问题
    $ProductData->content->is_mortgage      = !empty($data->mortgage) ? $data->mortgage : "";
    $ProductData->content->other_need       = $data->product_cycle;
    $ProductData->content->other            = $productData->other;
    $ProductData->content->is_home          = $productData->is_home;
    $ProductData->content->property_cut     = isset($needData->discount) ? $needData->discount : "";
    //这里  用户的 lending_cycle  为B端用户上传产品时的 audit_time
    $ProductData->content->audit_time       = $needData->lending_cycle;
    $ProductData->content->remark           = "";

    $retJson = $ProductData;

    return $retJson;
}

/**
 * @param $str
 * @return null|string
 * author hongwenyang
 * method description : 根据中文首字母转为对应的英文
 */

function _getFirstCharter($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    //虽然不知道为什么 单独拿出这几个市进行判断
    if($str == "衢州市")            return 'Q';
    if($str == "亳州市")            return 'H';
    if($str == "漯河市" || $str == "泸州市")  return 'L';
    if($str == "濮阳市")            return 'P';
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}

/**
 * @param $data 按字母顺序的城市列表
 * @param $city 最热城市
 * @return array
 * author hongwenyang
 * method description : 返回城市列表和最热城市
 */

function returnCityData($data,$city){
    if(empty($data)){
        $retJson['code'] = 400;
        $retJson['msg'] = "";
    }else{
        $retJson['code'] = 200;
        $retJson['msg'] = "获取数据成功";
    }

    $j = [
        'data'=>$data,
        'code'=>$retJson['code'],
        'msg'=>$retJson['msg'],
        'city'=>$city
    ];

    return $j;
}

/**
 * @param $CheckProduct 筛选到的数据
 * @param $SortKey   筛选类型
 * @return mixed
 * author hongwenyang
 * method description : 筛选数据
 */
function ProductSort($CheckProduct,$SortKey){
    // 根据sort进行关键词的排序
    switch ($SortKey){
        case 0:
            $sort = 'matching';
            break;
        case 1:
            $sort = 'accrual';
            break;
        case 2:
            $sort = 'accrual';
            break;
        case 3:
            $sort = 'product_cycle';
            break;
        default:
            $sort = 'product_cycle';
            break;
    }
    sort($CheckProduct);

    for($i = 1;$i<count($CheckProduct);$i++){
        for ($j = count($CheckProduct)-1;$j>=$i  ;$j-- ){
            if($SortKey == '0' || $SortKey == '2' || $SortKey == '4'){
                if($CheckProduct[$j][$sort] >= $CheckProduct[$j-1][$sort]){
                    $temp = $CheckProduct[$j];
                    $CheckProduct[$j] = $CheckProduct[$j-1];
                    $CheckProduct[$j-1] = $temp;
                }
            }else{
                if($CheckProduct[$j][$sort] < $CheckProduct[$j-1][$sort]){
                    $temp = $CheckProduct[$j];
                    $CheckProduct[$j] = $CheckProduct[$j-1];
                    $CheckProduct[$j-1] = $temp;
                }
            }
        }

    }
    foreach($CheckProduct as $k=>$v){
        $CheckProduct[$k]['matching'] = $v['matching'].'%';
    }
    return $CheckProduct;
}

/**
 * @param $data
 * @param $SortKey
 * @return array
 * author hongwenyang
 * method description : 获取所有匹配到的产品数据  并且根据企业是否购买排名保护 进行序列的调整
 */
function getIsBuy($data,$SortKey){
    $isBuy = array();
    $notBuy = array();
    $AllData= array();
    if(!empty($data)){
        foreach ($data as $k=>$v){
            if($v['is_buy']){
                // 购买了 报名保护
                $isBuy[$k] = $v;
            }else{
                $notBuy[$k] = $v;
            }
        }
        // 对购买排名保护的产品进行排序
        if(!empty($isBuy)){
            $isBuy = ProductSort($isBuy,$SortKey);
        }
        // 对未购买排名保护的产品进行排序
        if(!empty($notBuy)){
            $notBuy = ProductSort($notBuy,$SortKey);
        }
        $AllData = array_merge($isBuy,$notBuy);

    }
    return $AllData;
}
/**
 * @param $data
 * @param $type
 * @return mixed
 * author hongwenyang
 * method description : B端订单管理处理产品数据
 */

function BusinessOrderData($data,$type){

    $title = [
        'accrual',
        'is_home',
    ];
    foreach($data as $k=>$v){
        if($v->order_type == 0){
            $v->userInfor = $v->user_id;
            $where = [
                'user_id'=>$v->user_id,
                'cat_id' =>$v->cat_id,
                'equipment_type'=>0
            ];

        }else{
            $v->userInfor = $v->user_id;
            $where = [
                'user_id'=>$v->user_id,
                'cat_id' =>$v->cat_id,
                'equipment_type'=>1
            ];

        }
        $v->cat_name            = DB::table('product_cat')->where(['id'=>$v->p_id])->value('cat_name') .'-'. $v->cat_name;
        $product                = json_decode($v->pData,true);
        $v->is_home             = $product['is_home'];
        $v->product_cycle             = $product['product_cycle'];
//        $v->lending_cycle       = $product['lending_cycle'];
        $v->accrual             = $v->accrual;
        foreach ($product as $k1=>$v1){
            if(!in_array($k1,$title)){
                unset($product[$k1]);
            }
        }
        $userApply              = json_decode(DB::table('apply_form')->where($where)->value('need_data'),true);
        $v->share               = "";
        if($v->order_type == 1){
            $share = DB::table('config')->where('key','share')->value('value');

            //共享条件
            $v->share = "借款金额".($share * 100)."%";
        }
        $v->lending_type        = $userApply['lending_type'];
        //查询是否是黑名单用户 0 否 1 是
        $v->isBlack = empty(DB::table('black_user')->where(['user_no'=>$v->user_idcard])->whereIn('status',[0,7])->first()) ? 0 : 1;
        if($v->order_type == 0){
            //获取用户基本信息
            $UserBasic = json_decode(DB::table('apply_basic_form')->where(['user_id'=>$v->user_id,'type'=>0])->value('data'),true);
            $v->userName    = empty($UserBasic['name']) ? "" :$UserBasic['name'];
            $v->userPhone   = empty($UserBasic['phone']) ?"" :$UserBasic['phone'];
        }else{
            $CompanyBasic = DB::table('business_user')->where(['id'=>$v->user_id])->select('companyName','phone')->first();
            $UserBasic = json_decode(DB::table('apply_basic_form')->where(['user_id'=>$v->user_id,'type'=>1])->value('data'),true);
            if($v->order_type == 1){
                $v->companyName = "";
                $v->companyphone = "";
                if($CompanyBasic){
                    $v->companyName    = $CompanyBasic->companyName;
                    $v->companyphone   = $CompanyBasic->phone;
                }
            }
            $v->userName    =  $UserBasic['name'];
            $v->userPhone   =  $UserBasic['phone'];
        }
        $v->money   = $userApply['money'];
        $v->create_time = date('Y-m-d',$v->create_time);
        unset($v->pData);
    }
    return $data;
}

/**
 * @param $EvaluateData
 * @param $type
 * @return mixed
 * author hongwenyang
 * method description : 保存评价数据
 */

function SaveEvaluate($EvaluateData,$type){
    $map['order_id']                    = $EvaluateData['order_id'];
    if($type == 0){
        $ApplyEvaluateData['c_is_evaluate'] = 1;
        $EvaluateData['type']               = 0;
    }else{
        $ApplyEvaluateData['b_is_evaluate'] = 1;
        $EvaluateData['type']               = 1;
    }


    $s                                  = DB::table('user_apply')->where($map)->update($ApplyEvaluateData);
    $SaveData                           = DB::table('user_apply')->where($map)->select('product_id','user_id')->first();
    $EvaluateData['product_id']         = $SaveData->product_id;
    $EvaluateData['user_id']            = $SaveData->user_id;
    DB::table('product_evaluate')->insert($EvaluateData);
    if($s){
        $retJson['code'] = 200;
        $retJson['msg']  = '评价成功';
    }else{
        $retJson['code'] = 404;
        $retJson['msg']  = '评价失败';
    }
    return $retJson;
}

/**
 * @return array
 * author hongwenyang
 * method description : 返回省市区列表
 */

function getArea(){
    //返回省份列表
    $province = DB::table('ruyi_province')->get();
    //返回城市列表
    $city = DB::table('ruyi_city')->where([
        'father'=>110000
    ])->get();
    //返回地区列表
    $district = DB::table('ruyi_area')->where([
        'father'=>110100
    ])->get();

    $j = [
        'province'=>$province,
        'city'=>$city,
        'district'=>$district,
    ];

    return $j;
}

/**
 * @return array
 * author hongwenyang
 * method description : 返回省市区列表
 */

function getAreaList($provinceName,$cityName){
    //返回省份列表
    $province = DB::table('ruyi_province')->get();
    //返回城市列表
    $city = DB::table('ruyi_city')->where([
        'father'=>DB::table('ruyi_province')->where([
            'province'=>$provinceName
        ])->value('provinceID')
    ])->get();
    //返回地区列表
    $district = DB::table('ruyi_area')->where([
        'father'=>DB::table('ruyi_city')->where([
            'city'=>$cityName
        ])->value('cityID')
    ])->get();

    $j = [
        'province'=>$province,
        'city'=>$city,
        'district'=>$district,
    ];

    return $j;
}


function QiNiu(){
    $disk = Storage::disk('qiniu');
    $disk->put('avatars/1', 'file');
// get timestamp
    $time = $disk->lastModified('file1.jpg');
    $time = $disk->getTimestamp('file1.jpg');

// copy a file
    $disk->copy('old/file1.jpg', 'new/file1.jpg');

// move a file
    $disk->move('old/file1.jpg', 'new/file1.jpg');

// get file contents
    $contents = $disk->read('folder/my_file.txt');

// fetch file
    $file = $disk->fetch('folder/my_file.txt');

// get file url
    $url = $disk->getUrl('folder/my_file.txt');

// get file upload token
    $token = $disk->getUploadToken('folder/my_file.txt');
    $token = $disk->getUploadToken('folder/my_file.txt', 3600);

}

/**
 * @param $data
 * @return string
 * author hongwenyang
 * method description : 身份证附属地查询
 */
function IdBelonging($data){
    $belonging = "";
    $key = config('app.IdBelonging');
    $url = "http://apis.juhe.cn/idcard/index?key=".$key."&cardno=".$data;

    $return = json_decode(file_get_contents($url));

    if($return->resultcode == 200){
        $belonging = $return->result->area;
    }
    return $belonging;
}







