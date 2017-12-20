<?php

namespace App\Http\Controllers\Back;

use App\Model\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $data = Admin::paginate(10);
        $Pagetitle = 'admin';
        return view('Back.admin.Index',compact('data','Pagetitle'));
    }

    public function PerMessionError(){
        return view('errors.401');
    }


    public function dataTotal(){
        $Pagetitle  = "data";
        return view('Back.admin.data',compact("Pagetitle"));
    }

    /**
     * @param Request $r equest
     * @return mixed
     * author hongwenyang
     * method description : 删除管理账号
     */
    public function del(Request $request){
        $s = Admin::where('id',$request->id)->delete();

        return returnStatus($s);
    }
    /**
     * @param Request $request
     * @return mixed
     * author hongwenyang
     * method description : 新增
     */
    public function add(Request $request){
        $data = $request->except(['s']);
        $data['roleid'] = 2;
        $data['admin_pwd'] = sha1(sha1($data['admin_pwd']).'1234');
        $s = Admin::insert($data);
        return returnStatus($s);
    }

    public function edit(Request $request){
        $data = $request->except(['s']);
        $s = Admin::where('id',$data['id'])->update([
            'admin_name'=>$data['admin_name'],
            'admin_pwd'=>sha1(sha1($data['admin_pwd_before']).'1234'),
            'admin_pwd_before'=>$data['admin_pwd_before']
        ]);
        return returnStatus($s);
    }
}
