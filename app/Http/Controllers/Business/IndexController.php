<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index(){
        $title = 'userInfo';
        return view('Business.index',compact('title'));
    }


    public function demo(){
        echo 1;
    }
}
