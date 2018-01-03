<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Model\BusinessUser;
use App\Model\IntegralList;
use App\Model\Product;
use App\Model\User;
use App\Model\UserApply;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $where = [];
        $whereBetween = [];
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
                $where = [
                    'phone'=>$keyword
                ];
            }else{
                $where = [
                    'companyName'=>$keyword
                ];
            }
        }

        if(isset($_GET['is_pass'])){
            $is_pass = $_GET['is_pass'];
            if($is_pass != "" && $is_pass != 3){

                $pass = [
                    'is_pass'=>$is_pass
                ];
                $where = array_merge($pass,$where);
            }

        }

        if(isset($_GET['exTime']) && !empty($_GET['exTime'])){
//            dd($_GET['exTime']);
            $time = explode(' - ',$_GET['exTime']);
            $whereBetween = [$time[0],$time[1]];
        }else{
            $whereBetween = ["1997-01-01","2999-12-31"];
        }
        // 查询用户状态
        if(!empty($_GET['keyword'])){
            $keyword = $_GET['keyword'];
            if($type == 1){
                $data = DB::table($table)->orWhere([
                    'user_name'=>$keyword
                ])->orWhere([
                    'phone'=>$keyword
                ])->orderBy('create_time','desc')->whereBetween('create_time',$whereBetween)->paginate(10);
            }else{
                $data = DB::table($table)->orWhere([
                    'companyName'=>$keyword
                ])->orWhere([
                    'phone'=>$keyword
                ])->orderBy('create_time','desc')->paginate(10);
            }

        }else{
            $data = DB::table($table)->where($where)->whereBetween('create_time',$whereBetween)->orderBy('create_time','desc')->paginate(10);
        }


        $j = [
            'Pagetitle'=>$title,
            'data'=>$data,
            'type'=>$type,
            'is_pass'=> isset($_GET['is_pass']) ? $_GET['is_pass'] : 3,
            'keyword'=>  isset($_GET['keyword']) ? $_GET['keyword'] : "",
            'time'=> isset($_GET['exTime']) ? $_GET['exTime'] : ""
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

    /**
     * @param Request $request
     * @return mixed
     * author hongwenyang
     * method description : 保存用户数据
     */
    public function save(Request $request){
        $post = $request->except(['s']);
        $map['id'] = $post['id'];
        unset($post['id']);
        $s = BusinessUser::where($map)->update($post);
        return returnStatus($s);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 处理图片
     */
    public function savePic(Request $request){
        $data = $request->except(['s']);
        if(is_file($data['file']) ){
            $img = '/uploads/'.Storage::disk('fcz')->put('fcz', $data['file']);
        }
        return response()->json(['data'=>$img]);
    }

    public function addInt(Request $request){
        $post = $request->except(['s']);
        if($post['type'] == 1){
            // 增加c端用户的金币 gold,integral 都要增加
            User::where('id',$post['id'])->increment('integral',$post['count']);
            User::where('id',$post['id'])->increment('gold',$post['count']);

            $integraling = User::where('id',$post['id'])->value('integral');
            $user_type = 0;
        }else{

            // 增加b端用户的金币 gold,integral 都要增加
            BusinessUser::where('id',$post['id'])->increment('integral',$post['count']);
            BusinessUser::where('id',$post['id'])->increment('gold',$post['count']);
//           dd($s);
            $integraling = BusinessUser::where('id',$post['id'])->value('integral');
            $user_type = 1;
        }
        $s = 1;
        // 增加金币操作列表
        IntegralList::insert([
            'user_id'=>$post['id'],
            'integral'=>$post['count'],
            'integraling'=>$integraling,
            'type'=>7,
            'user_type'=>$user_type,
            'is_gold'=>2
        ]);

        return returnStatus($s);
    }
}
