<?php

namespace App\Http\Controllers\Back;

use App\Model\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        dd(time()+60*60*24*3);
        $data = Admin::paginate(10);
        $Pagetitle = 'admin';
        return view('Back.admin.Index',compact('data','Pagetitle'));
    }
}
