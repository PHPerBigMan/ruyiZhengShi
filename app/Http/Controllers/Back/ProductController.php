<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Model\BusinessUser;
use App\Model\Product;
use App\Model\ProductCat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author  hongwenyang
     * method description : 贷款分类列表
     */

    public function Plist(){
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author  hongwenyang
     * method description : 删除分类
     */

    public function cat_del(Request $request){
        $map['id'] = $request->input('id');
        //查询是否有二级分类
        $isHave = DB::table('product_cat')->where([
            'p_id'=>$map['id']
        ])->get();
        if($isHave->count()){
           $code = 403;
           $msg = "一级分类下存在二级分类，无法直接删除";
        }else{
            DB::table('product_cat')->where($map)->update([
                'is_del'=>1
            ]);
            $code = 200;
            $msg = "删除成功";
        }

        $j = [
            'code'=>$code,
            'msg' =>$msg
        ];
        return response()->json($j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author  hongwenyang
     * method description : 编辑分类
     */

    public function edit(Request $request){
        $map['id']  = $request->input('id');
        $data       = DB::table('product_cat')->where($map)->first();
        if($map['id']  == 0){
            $data           = json_decode('{}');
            $data->cat_name = "";
            $data->cat_pic  = "";
            $data->id       = 0;
        }

        $j = [
            'data'=>$data
        ];

        return view('Back.Product.edit',$j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse 图标路径
     * author  hongwenyang
     * method description : 接收分类图标
     */

    public function cat_pic(Request $request){
        $file = $request->file('file')->store('imgs','img');
        $return   = '/uploads/'.$file;
        $j= [
            'url'=>$return
        ];
        return json_encode($j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存贷款分类数据
     */

    public function CatAdd(Request $request){
      $CatData = $request->except(['s']);
      if($CatData['type'] == 0){
          //修改分类
          $s = DB::table('product_cat')->where([
              'id'=>$CatData['id']
          ])->update([
              'cat_name'=>$CatData['cat_name']
          ]);
      }else if($CatData['type'] == 1){
          //新增一级分类
          $s = DB::table('product_cat')->insert([
              'cat_name'=>$CatData['cat_name'],
              'level'=>1
          ]);
      }
      $retStatus = returnStatus($s);

      return response()->json($retStatus);
    }



    public function product(){
        $data = DB::table('product')->get();

        $j = [
            'data'=>$data,
            'title'=>'产品列表'
        ];

        return view('Back.Product.product',$j);
    }

    /**
     * @param $p_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 二级分类
     */

    public function SecCat($p_id){
        $data = DB::table('product_cat')->where([
            'p_id'=>$p_id,
            'is_del'=>0
        ])->get();

        $title = "ProductCatList";

        $j = [
            'Pagetitle'=>$title,
            'productCat'=>$data
        ];

        return view('Back.Product.sec',$j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除二级分类
     */

    public function sec_cat_del(Request $request){
        $map['id'] = $request->input('id');

        $s =  DB::table('product_cat')->where($map)->update([
            'is_del'=>1
        ]);
        $retStatus = returnStatus($s);
        return response()->json($retStatus);
    }

    public function checkList()
    {
        $data = Product::latest('create_time')->where(['is_show'=>2])->paginate(10);
//        dd($data);
        $Pagetitle = 'product';
        return view('Back.Product.checklist', compact('data','Pagetitle'));
    }


    public function showCheck($id)
    {
        $product = Product::findOrFail($id);
//        dd($product);
        $Pagetitle = 'product';
        $arr = [
            'pNumber' => '产品编号',
            'area'=>  '地区范围',
            'money'=>  '额度',
            'accrual'=>'利息',
            'credit'=>'征信要求',
            'product_cycle'=>'可借款周期',
            'audit_time'=> '审核周期',
            'is_mortgage'=>'是否有抵押',
            'type'=>'类型',
            'property_cut'=>'估值率',
        ];
        return view('Back.Product.showCheck', compact('product','Pagetitle','arr'));
    }


    public function editStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $result = $product->update(['is_show' => $request->type]);
        if ($result) {
            return ['code' => 200, 'msg' => '更新成功'];
        }
        return ['code' => 404, 'msg' => '更新出错'];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 已上传产品的用户
     */
    public function productList(Request $request){
       $keyword = $request->input('keyword');

       $data =  BusinessUser::getUserProduct($keyword);

       $Pagetitle = 'Product';

       return view('Back.business.index',compact('data','Pagetitle','keyword'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : B端用户已上传产品分类列表
     */
    public function productCat($id){
        $data = BusinessUser::getUserProductCat($id);

        $Pagetitle = 'Product';

        $companyName = BusinessUser::where('id',$id)->value('companyName');

        return view('Back.business.catList',compact('data','Pagetitle','id','companyName'));
    }

    public function productCatMore($id,$cat_id){
       $data =  BusinessUser::CatProduct($id,$cat_id);
//       dd($data);
       $Pagetitle = 'Product';

       $catName = ProductCat::where('id',$cat_id)->value('cat_name');

       return view('Back.business.productList',compact('data','Pagetitle','catName'));
    }
}
