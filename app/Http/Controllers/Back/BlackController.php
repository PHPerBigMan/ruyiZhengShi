<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class BlackController extends Controller
{
    public function index($type){
        if($type == 1){
          //新增审核
            $whereNotIn = [0,1,3,4,5,6,7];
            $title = 'new';
        }else if($type == 2){
            //上架审核
            $whereNotIn = [0,1,2,3,4,6,7];
            $title = 'show';
        }else if($type == 3){
            $whereNotIn= [2,5];
            $title = 'list';
        }else{
            $whereNotIn= [0,2,1,3,4,5,7];
            $title = 'unpass';
        }

        $data = DB::table('black_user as u')->whereNotIn('status',$whereNotIn)
            ->join('business_user as b','b.id','=','u.business_id')
            ->select('u.*','b.companyName','b.number')
            ->orderBy('u.create_time','desc')
            ->paginate(10);


        $j = [
            'Pagetitle'=>$title,
            'data'=>$data,
            'type'=>$type
        ];

        return view('Back.black.Index',$j);
    }


    public function change(Request $request){
        $data = $request->except(['s']);
        $s = DB::table('black_user')->where([
            'id'=>$data['id']
        ])->update([
            'status'=>$data['status']
        ]);

        $retStatus = returnStatus($s);
        return response()->json($retStatus);
    }
}
