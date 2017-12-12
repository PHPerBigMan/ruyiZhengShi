<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:38
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Area;
use App\Model\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
    protected $model= "";
    protected $apply= "";
    protected $return = "";
    public function __construct()
    {
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 读取产品详细数据
     * param :id 产品id
     */

    public function read(Request $request){
        $ProductId = $request->except(['s']);
        $s = new Logs();
        $s->logs('产品详情查看',$ProductId);
        $ProductData = DB::table('product as p')
            ->join('product_cat as c','c.id','=','p.cat_id')
            ->join('business_user as b','b.id','=','p.business_id')
            ->where(['p.id'=>$ProductId['product_id']])
            ->select('p.*','c.cat_name','b.companyName','c.id as cat_id')->first();

        $ProductData->content = json_decode($ProductData->content);
//        dd($ProductData);
        $ProductData->content->is_show = empty($ProductData->is_show) ? "" :$ProductData->is_show;
        $ProductData->content->cat_name = empty($ProductData->cat_name) ? "" :$ProductData->cat_name;
        $ProductData->content->property = !isset($ProductData->content->type) ? "" :$ProductData->content->type;
        $ProductData->content->companyName = empty($ProductData->companyName) ? "" :$ProductData->companyName;

        $retJson = returnData($ProductData->content);
        return response()->json($retJson);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 产品评价
     * param:product_id  产品id
     */

    public function evaluate(Request $request){
        $EvaluateData = $request->except(['s']);
        $data = DB::table('product_evaluate')->where(['product_evaluate.product_id'=>$EvaluateData['product_id'],'type'=>0])
            ->join('user','product_evaluate.user_id','=','user.id')
            ->select('product_evaluate.desc','product_evaluate.score','user.user_name','user.user_pic','user.updated_at as time','product_evaluate.order_id','product_evaluate.product_id','product_evaluate.create_time')->get();
        foreach($data as $v){
            $v->time    = $v->create_time;
            $v->content = DB::table('product_evaluate')->where([
                'product_id'=>$v->product_id,
                'order_id'=>$v->order_id,
                'type'=>1
            ])->value('content');
        }

        $retJson = returnData($data);
        return response()->json($retJson);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description :  匹配表单选择项数据
     * param: cat_id 二级分类id
     */

    public function Property(Request $request){
        $PropertyCatId = $request->except(['s']);
        $CatName = DB::table('product_cat')->where(['id'=>DB::table('product_cat')->where(['id'=>$PropertyCatId])->value('p_id')])->value('cat_name');

        $table = 'house_property';
        $key = ['car_year','gongZi','jifen','house_status','count_money','job','license','decorate','accrual','product_cycle','add_product_cycle','type','life','house_type','use','lending_cycle','lending_type','money','property','around','credit'];
        $data = DB::table($table)->where(['cat_id'=>$PropertyCatId['cat_id']])->first();


//        dd($data);
        foreach($data as $k=>$v){
            if(in_array($k,$key)){
                $data->$k = json_decode($v,true);
            }
        }

        //如果是B端上传产品  则 product_cycle 数据改成 add_product_cycle
        if(isset($PropertyCatId['type'])){
            $data->product_cycle = $data->add_product_cycle;
        }
        $retJson = returnData($data);

        return response()->json($retJson);
    }

    public function demo(){
        $Message = DB::table('article')->whereIn('type',[4,10])->get();
        //注册成功发送消息
        $message = [4,10];

        foreach($Message as $k=>$v){
            if($message[$k] == 4 || ($message[$k] == 5)){
                $messageType = 2;
            }else{
                $messageType = 0;
            }
            DB::table('message')->insert([
                'user_id'=>6,
                'equipment_type'=>0,
                'title'=>$v->title,
                'content'=>$v->content,
                'type'=>$messageType
            ]);
        }
    }
}