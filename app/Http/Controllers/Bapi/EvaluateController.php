<?php

namespace App\Http\Controllers\Bapi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EvaluateController extends Controller
{
    public function EvaList(Request $request){
        $EvaStatus = $request;
        $OrderData = DB::table('user_apply as u')
                   ->join('product as p','p.id','=','u.product_id')
                   ->join('product_cat as c','c.id','=','p.cat_id')
                   ->join('apply_basic as b','b.user_id','=','u.user_id')
                   ->select('p.cat_id','b.data')
                   ->where([
                       'u.c_is_evaluate'=>1,
                       'u.b_is_evaluate'=>0,
                       'p.business_id'=>$EvaStatus['business_id']
                   ])->get();
        dd($OrderData);
    }
}
