<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
    protected function err($msg = "",$code =""){
        return response()->json([
            'msg'=>$msg,
            'code'=>$code
        ]);
    }
}
