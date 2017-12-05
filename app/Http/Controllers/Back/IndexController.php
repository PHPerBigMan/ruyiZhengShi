<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Model\BusinessUser;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    public function index(Request $request){
        $admin_id   = $request->session()->get('admin_user');
        $admin_name = DB::table('admin')->where(['id'=>$admin_id])->value('admin_name');

        //分类数据
        $productCat = DB::table('product_cat')->where([
            'level'=>1,
            'is_del'=>0
        ])->get();
        $j = [
            'Pagetitle'     =>'ProductCatList',
            'admin_name'=>$admin_name,
            'productCat'=>$productCat
        ];
        return view('Back/Product/product',$j);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 首页数据统计
     */

    public function detail()
    {
        $user_count = User::count();
        $business_count = BusinessUser::count();
        $Pagetitle = 'home';
        $order = UserApply::valid()->select(DB::raw('sum(order_count) as total_money, count(*) as orders_count'))
            ->first();
        $tops = UserApply::join('product','product.id','=','user_apply.product_id')
            ->join('business_user','business_user.id','=','product.business_id')
            ->valid()->select('business_user.*',DB::raw('sum(order_count) as total_money'))->groupBy('product.business_id')
            ->orderBy('total_money','desc')
            ->get();

        $ProductTops = UserApply::join('product','product.id','=','user_apply.product_id')
            ->join('business_user','business_user.id','=','product.business_id')
            ->join('product_cat as c','c.id','=','product.cat_id')
            ->valid()->select('business_user.*',DB::raw('sum(order_count) as total_money'),'product.*',DB::raw('count(product_id) as pCount'),'c.cat_name')->groupBy('product.id')
            ->orderBy('pCount','desc')
            ->paginate(10);

//        dd($ProductTops);
        return view('Back.dashboard', compact('Pagetitle','user_count','order','tops','business_count','ProductTops'));
    }

}
