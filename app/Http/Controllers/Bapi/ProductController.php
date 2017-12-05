<?php

namespace App\Http\Controllers\Bapi;

use App\Model\Logs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 分类下的产品列表
     */

    public function ProductList(Request $request){
        $CatId = $request->except(['s']);
        $ProductData = DB::table('product')->where($CatId)->whereIn('is_show',[0,1])->select('id','content','is_show','create_time','cat_id')->get();

//        dd($ProductData);
        foreach($ProductData as $k=>$v){
            $content                        = json_decode($v->content,true);
            $ProductData[$k]->money         = $content['money'];
            $ProductData[$k]->accrual       = $content['accrual'];
            $ProductData[$k]->lending_cycle = $content['product_cycle'];
            $ProductData[$k]->is_home       = $content['is_home'];
            $ProductData[$k]->lending_type  = $content['lending_type'];
            $ProductData[$k]->pNumber  = $content['pNumber'];
            unset($ProductData[$k]->content);
        }
        $retData = returnData($ProductData);
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 对产品进行上下架 删除操作
     */

    public function ProductChange(Request $request){
        $ProductData = $request->except(['s']);
        $Change['is_show'] = $ProductData['status'];
        if($ProductData['status'] == 2){
            $Change['is_del'] =  1;
        }
        $s = DB::table('product')->whereIn('id',$ProductData['id'])->update($Change);
        $retStatus = returnStatus($s);
        return response()->json($retStatus);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存产品数据
     */
    public function ProductSave(Request $request){
        $ProductData = $request->except(['s']);

        $business_id = $ProductData['business_id'];
        $cat_id      = $ProductData['cat_id'];

        unset($ProductData['business_id']);
        unset($ProductData['cat_id']);

        $ProductData['district'] = $ProductData['area'];

        //处理产品范围数据
        $save['province'] = "不限";
        $save['city'] = "不限";
        $save['district'] = "不限";
        // 添加产品类型
        $ProductData['type'] = DB::table('product_cat')->where('id',$cat_id)->value('cat_name');
        if(isset($ProductData['area']) && $ProductData['area'] != "null" && $ProductData['area']!= "不限"){
                $dizhi = explode(' ',$ProductData['area']);
                $save['province'] = $dizhi[0];
                $save['city'] = $dizhi[1];
                $save['district'] = $dizhi[2];

        }
        if(empty($ProductData['id'])){
            $s = DB::table('product')->insert([
                'business_id'=>$business_id,
                'cat_id'     =>$cat_id,
                'content'    =>json_encode($ProductData),
                'province'   =>$save['province'],
                'city'       =>$save['city'],
                'district'   =>$save['district'],
            ]);
        }else{
            $s = DB::table('product')->where(['id'=>$ProductData['id']])->update([
                'business_id'=>$business_id,
                'cat_id'     =>$cat_id,
                'content'    =>json_encode($ProductData),
                'province'   =>$save['province'],
                'city'       =>$save['city'],
                'district'   =>$save['district'],
            ]);
        }
        $retStatus = returnStatus($s);
        return response()->json($retStatus);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 随机产品编号
     */
    public function ProductNo(){
        $numbers = range(1,999999);
        shuffle($numbers);
        $num = 1;
        $result = array_slice($numbers,0,$num);

        $retData = returnData($result[0]);
        return response()->json($retData);
    }
}
