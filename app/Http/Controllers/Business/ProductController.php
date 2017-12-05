<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{
    protected $product = "";
    public function __construct()
    {
        $this->product = new Product();

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author  hongwenyang
     * method description : 贷款分类列表
     */

    public function index(){
        $ProductCat = DB::table('product_cat')->where(['level'=>1])->get();

        foreach ($ProductCat as $k=>$v){
            $v->sec_cat_name = DB::table('product_cat')->where(['p_id'=>$v->id])->value('cat_name');
        }
        $j = [
            'title'=>'index',
            'product_cat'=>$ProductCat
        ];
        return view('Business.product.index',$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取产品列表
     */

    public function productList(Request $request){
        $ProductCatId = $request->except('s');

        //判断传递过来的分类id 如果 是一级分类则先选择第一个 与之对应的二级分类
        //如果是二级分类则直接查询产品数据
        $isFirst = DB::table('product_cat')->where(['id'=>$ProductCatId['cat_id']])->value('level');
        if($isFirst != '1'){
            $ProductData = DB::table('product')->where($ProductCatId)->where(['business_id'=>session('business_admin'),'is_del'=>0])->get();
        }else{
            $ProductCatId['cat_id'] = DB::table('product_cat')->where(['p_id'=>$ProductCatId['cat_id']])->value('id');
            $ProductData = DB::table('product')->where($ProductCatId)->where(['business_id'=>session('business_admin'),'is_del'=>0])->get();
        }
        if(count($ProductData) == 0){
            $retData = [];
        }else{
            foreach($ProductData as $k=>$v){
                $retData[$k] = json_decode($v->content,true);
                $retData[$k]['id'] = $v->id;
                $retData[$k]['is_show'] = $v->is_show == 0 ? "已下架" : $v->is_show == 1 ? "已上架" : "上架审核中";
            }
        }
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 获取对应一级分类的二级分类
     */

    public function ProductSecondCat(Request $request){
        $pId = $request->except('s');
        if($pId['p_id'] == 0){
            $pId['p_id'] = 1;
        }
        $pId['is_del']= 0;
        $ProductSecondCat = DB::table('product_cat')->where($pId)->get();
        return response()->json($ProductSecondCat);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 获取产品数据
     */

    public function productRead($id,$type,$cat_id = 0){
        if(!$cat_id){
            $cat_id = DB::table('product')->where(['id'=>$id])->value('cat_id');
        }
        $map['id']= $id;
        //获取数据
        $ProductData = json_decode(DB::table('product')->where($map)->value('content'),true);

        //产品区域


        $diqu = DB::table('product')->where($map)->select('province','city','district')->first();
        if($diqu ==null){
            $diqu = json_decode('{}');
            $diqu->province = "不限";
            $diqu->city = "不限";
            $diqu->district = "不限";
        }
        //获取对应的选择项
        $table = "house_property";
        $list = DB::table($table)->where(['cat_id'=>$cat_id])->get();

        $title = [
            'money',
            'decorate',
            'accrual',
            'product_cycle',
            'lending_cycle',
            'lending_type',
            'type',
            'life',
            'property',
            'house_type',
            'around',
            'jifen'
        ];

        if(!empty($list)){
            $list = json_decode($list,true);
            foreach($list[0] as $k=>$v){
                if(in_array($k,$title)){
                    $list[0][$k] = json_decode($v,true);
                }
            }
        }

        //随机产品编号
        $numbers = range(1,999999);
        shuffle($numbers);
        $num = 1;
        $result = array_slice($numbers,0,$num);

        $AllArea = getArea();
        //返回省份列表
        $province = $AllArea['province'];
        //返回城市列表
        $city = $AllArea['city'];
        //返回地区列表
        $district = $AllArea['district'];
        //如果是 在建工程贷则对area进行处理
        if($cat_id == 62 && $id){
            $ProductData['area'] =  explode(',',$ProductData['area']);
        }

        $j = [
            'product'=>$ProductData,
            'type'   =>$type,
            'cat_id' =>$cat_id,
            'id'     =>$id,
            'list'   =>$list,
            'pNumber'=>$result[0],
            'province'=>$province,
            'city'=>$city,
            'district'=>$district,
            'diqu'=>$diqu

        ];
//       dd($j);
        $view = 'Business.product.productRead_'.$cat_id;

        return view($view,$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存产品数据
     */
    public function productSave(Request $request){
        $data = $request->except(['s','xieyi']);
        $insert['business_id'] = session('business_admin');
        $save['province'] = "不限";
        $save['city'] = "不限";
        $save['district'] = "不限";
        if($data['cat_id'] != 62){
            if(isset($data['area']) && $data['area'] == "null"){
                $data['area'] = "null";
                $data['district'] = $data['area'];
            }else if($data['province'] && $data['city'] && $data['diqu']){
                if($data['province'] == "不限"){
                    $data['area'] = "不限";
                    $data['district'] = "不限";
                }else{
                    $province = DB::table('ruyi_province')->where(['provinceID'=>$data['province']])->value('province');
                    $city = DB::table('ruyi_city')->where(['cityID'=>$data['city']])->value('city');
                    $area = DB::table('ruyi_area')->where(['areaID'=>$data['diqu']])->value('area');
                    $data['area'] = $province ." ". $city ." ". $area;
                    $data['district'] = $province ." ". $city ." ". $area;

                    $save['province'] = $province;
                    $save['city'] = $city;
                    $save['district'] = $area;
                }
            }
        }else{
           $data['area'] = implode(',',$data['area']);
           $data['district'] = $data['area'];
       }

        $data['other_need'] = $data['other_need_1'].','.$data['other_need_2'].','.$data['other_need_4'].','.$data['other_need_4'];

        if($data['cat_id'] == 20){
            if($data['life']){
                $data['life'] = $data['life_content'];
            }else{
                $data['life'] = "";
            }
        }

        if($data['other']){
            $data['other'] = $data['other_content'];
        }else{
            $data['other'] = "";
        }

        if($data['cat_id'] != 20){
            if(isset($data['years'])){
                $data['years'] = $data['year_content'];
            }else{
                $data['years'] = "";
            }
        }

        $need_title = [
            'pNumber',
            'other_need',
            'accrual',
            'product_cycle',
            'money',
            'lending_type',
            'audit_time',
            'property_cut',
            'property',
            'province',
            'area',
            'is_mortgage',
            'is_home',
            'credit',
            'life',
            'other',
            'cat_id',
            'years',
            'district',
            'type',
            'mortgage_type',
            'id',
            'Jifen'
        ];


        foreach($data as $k=>$v){
            if(!in_array($k,$need_title)){
                unset($data[$k]);
            }
        }


        $insert['content'] = json_encode($data);

        //       dd($insert);
        if(!empty($data['id'])){
            $s = $this->product->where(['id'=>$data['id']])->update([
                'content'=>$insert['content'],
                'province'=>$save['province'],
                'city'=>$save['city'],
                'district'=>$save['district'],
            ]);
        }else{
            $insert['cat_id'] = $data['cat_id'];

            $s = $this->product->insert([
                'content'=>$insert['content'],
                'cat_id'=>$insert['cat_id'],
                'business_id'=>$insert['business_id'],
                'province'=>$save['province'],
                'city'=>$save['city'],
                'district'=>$save['district'],
            ]);
        }
        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = '产品保存成功';
        }else{
            $retJson['code'] = 403;
            $retJson['msg']  = '产品保存失败';
        }

        return response()->json($retJson);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * author hongwenyang
         * method description : 删除产品
         */
        public function productDel(Request $request){
            $id = $request->except(['s']);
            foreach($id['id'] as $k=>$v){
                $this->product->where(['id'=>$v])->update([
                    'is_del'=>1
                ]);
            }
            $retJson['code'] = 200;
            $retJson['msg']  = "删除成功";

            return response()->json($retJson);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * author hongwenyang
         * method description : 产品上下架
         */
        public function shelves(Request $request){
            $Shelves = $request->except(['s']);
            foreach($Shelves['id'] as $v){
                $this->product->where(['id'=>$v])->update([
                    'is_show'=>$Shelves['is_show']
                ]);
            }

            $retJson['code'] = 200;
            return response()->json($retJson);
        }

        /**
         * author  hongwenyang
         * method description : 获取城市
         */
        public function city(Request $request){
            $father = $request->input('father');
            if($father == "不限"){
                $cityList = "<option value=''>请选择</option><option value='不限'>不限</option>";
            }else{
                $city = DB::table('ruyi_city')->where([
                    'father'=>$father
                ])->get();
                $cityList = "<option value=''>请选择</option>";
                foreach($city as $v){
                    $cityList .= "<option value='$v->cityID'>$v->city</option>";
                }
            }
            $retData = returnData($cityList);

            return response()->json($retData);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * author  hongwenyang
         * method description : 获取区域
         */

        public function area(Request $request){
            $father = $request->input('father');
            if($father == "不限"){
                $cityList = "<option value=''>请选择</option><option value='不限'>不限</option>";
            }else{
                $city = DB::table('ruyi_area')->where([
                    'father'=>$father
                ])->get();
                $cityList = "<option value=''>请选择</option>";
                foreach($city as $v){
                    $cityList .= "<option value='$v->areaID'>$v->area</option>";
                }
            }

            $retData = returnData($cityList);

            return response()->json($retData);
        }


}
