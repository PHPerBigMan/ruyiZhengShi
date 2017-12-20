<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:38
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 产品贷款分类
     * param: level->分类等级  cat_id->二级分类时添加一级分类id
     */

    public function proCat(Request $request){
        $proData = $request->except('s');
        if($proData['level'] == 1){
            $data = DB::table('product_cat')->select('cat_name','cat_pic','id as cat_id')->where(['level'=>1,'is_del'=>0])->get();
        }else{
            $data = DB::table('product_cat')->select('cat_name','cat_pic','id as cat_id')->where(['level'=>2,'p_id'=>$proData['cat_id'],'is_del'=>0])->get();
            //如果传递了 business_id 则获取对应二级分类下产品的总数
            if(isset($proData['business_id'])){
               foreach($data as $k=>$v){
                   $v->count = DB::table('product')->where([
                       'business_id'=>$proData['business_id'],
                       'cat_id'=>$v->cat_id,
                       'is_del'=>0,
                       'is_show'=>1
                   ])->count();
               }
            }
        }

        $j = returnData($data);

        return response()->json($j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : APP图片
     * param:type->图片类型 0：启动页 1：过渡页 2：轮播图
     */

    public function img(Request $request){
        $imgData = $request->except('s');
        $data = DB::table('app_pic')->where($imgData)->select('pic')->get();

        $j = returnData($data);
        return response()->json($j);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取热门城市和城市列表
     */

    public function City(){
        $City = DB::table('city')->select('name','id','is_hot')->get();
        $retCity = [];
        $retHot  = [] ;
        foreach ($City as $k=>$v){
            $data = _getFirstCharter($v->name);
            $retCity[$data]['cityData'][] = $v;
            $retCity[$data]['en'] = $data;

            if($v->is_hot == 1 && !empty($v)){
                $retHot[] = $v;
            }
        }
        ksort($retCity);
        $retCity = array_values($retCity);
        $retData= returnCityData($retCity,$retHot);
        return response()->json($retData);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 关于我们
     */

    public function About(Request $request){
        $a = $request->input('type');

        $article = $request->input('article_type');


        if(!isset($a) || $a == 0){
            if(isset($article) && $article == 1){
                $type = [0];
            }else{
                $type = [2];
            }
        }else{
//            $ss = new Logs();
//            $ss->logs('获取服务协议',$request->except(['s']));
            if(isset($article) && $article == 1){
                $type = [0];
            }else{
                $type = [12];
            }

        }

        $data = DB::table('article')->whereIn('type',$type)->select('title','id','content','create_time')->orderBy('type','desc')->get();


        $j = returnData($data);

        return response()->json($j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 城市搜索
     */
    public function CityList(Request $request){
        $CityName = $request->input('keyword');
        $CityList = DB::table('city')->where('name','like',"%$CityName%")->select('name')->get();
        $retData = returnData($CityList);

        return response()->json($retData);
    }


    public function FenXiangImg(Request $request){
        $type = $request->input('type');
        if($type){
            //B端
            $img = URL.'/activity';
        }else{
            $img = URL.'/clientactivity';
        }

        $j = [
            'url'=>$img,
            'title'=>"【如易金服】现金大派送 100%,就怕你不用",
            'desc'=>"如易金服推广链接"
        ];

        return returnData($j);
    }
}