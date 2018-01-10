<?php

namespace App\Http\Controllers\Back;

use App\Model\IntegralList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IntegralController extends Controller
{
    public function index(Request $request)
    {
        $data = IntegralList::when($request->keyword, function ($query) use ($request){
            return $query->where('user_id', $request->keyword);
        })->when($request->is_gold, function ($query) use ($request){
            switch ($request->is_gold){
                case 1:
                    $whereIn = [1];
                    break;
                case 2:
                    $whereIn = [2];
                    break;
                default:
                    $whereIn = [1,2];
                    break;
            }
            return $query->whereIn('is_gold', $whereIn);
        })->when($request->time, function ($query) use ($request){

            if(!empty($request->time)){
                $time = explode(' - ',$request->time);
                $startTime = strtotime($time[0]);
                $endTime   = strtotime($time[1]);
            }else{
                //如果没有时间  则查询一年内的
                $startTime = time() - (60*60*24*365);
                $endTime = time();
            }
            return $query->whereBetween('create_time', [$startTime,$endTime]);

        })->when($request->user_type, function ($query) use ($request){
            switch ($request->user_type){
                case 3:
                    $whereIn = [0,1];
                    break;
                case 2:
                    $whereIn = [1];
                    break;
                default:
                    $whereIn = [0];
                    break;
            }
            return $query->whereIn('user_type', $whereIn);
        })->latest('create_time')->paginate(15);
        $Pagetitle = 'userIntegral';
        $is_gold = $request->is_gold;
        $user_type = empty($request->user_type) ? 3 :$request->user_type;
        $keyword = isset($request->keyword) ? $request->keyword : "";
        $time = isset($request->time) ? $request->time : "";

        // 积分增加统计
        $increment = [0,1,2,3,4,7];
        $incrementCount = IntegralList::whereIn('type',$increment)->sum('integral');
        // 减少统计
        $decrement = [5,6];
        $decrementCount = IntegralList::whereIn('type',$decrement)->sum('integral');
        // 总数
        return view('Back.integral.index', compact('data', 'Pagetitle','is_gold','keyword','time','user_type'));
    }
}
