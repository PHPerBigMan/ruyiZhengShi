<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Model\BusinessUser;
use App\Model\Product;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * C／B两端用户列表
     */

    public function UserList($type){
        $keyword = "";

        if($type == 1){
            //C端用户
            $table = 'user';
            $title = 'userC';
            $view = "Index";
        }else{
            //B端用户
            $table = 'business_user';
            $title = 'userB';
            $view = "Bindex";
        }
        if(!empty($_GET['keyword'])){
            $keyword = $_GET['keyword'];
            if($type == 1){
                $data = DB::table($table)->orWhere([
                    'user_name'=>$keyword
                ])->orWhere([
                    'phone'=>$keyword
                ])->orderBy('create_time','desc')->paginate(10);
            }else{
                $data = DB::table($table)->orWhere([
                    'companyName'=>$keyword
                ])->orWhere([
                    'phone'=>$keyword
                ])->orderBy('create_time','desc')->paginate(10);
            }

        }else{
            $data = DB::table($table)->orderBy('create_time','desc')->paginate(10);
        }

        $j = [
            'Pagetitle'=>$title,
            'data'=>$data,
            'type'=>$type
        ];

        return view('Back.user.'.$view,$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 修改用户是否通过审核
     */

    public function changeStatus(Request $request){
        $data = $request->except(['s']);
        $s = DB::table('business_user')->where([
            'id'=>$data['id']
        ])->update([
            'is_pass'=>$data['is_pass']
        ]);

        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $Pagetitle = 'userC';

        return view('Back.user.detail', compact('user', 'Pagetitle'));
    }

    public function showCompany($id)
    {
        $user = BusinessUser::findOrFail($id);
        $Pagetitle = 'userB';
        // 计算成交单数，成交额
        $product_ids = Product::where('business_id',$id)->pluck('id');
        $order = UserApply::whereIn('product_id', $product_ids)->valid()
            ->select(DB::raw('sum(order_count) as total_money, count(*) as orders_count'))
            ->first();
        return view('Back.user.company', compact('user', 'Pagetitle', 'order'));
    }


    public function demo(){
        $data = User::inRandomOrder()->first();
        dd($data);
    }
}
