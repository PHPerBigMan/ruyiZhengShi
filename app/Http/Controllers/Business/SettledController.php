<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettledController extends Controller
{
    //
    public function index(){
        return view('Business.settled.index');
    }
}
