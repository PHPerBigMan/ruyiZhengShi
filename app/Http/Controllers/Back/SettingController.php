<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class SettingController extends Controller
{
    /**
     * 数据列表
     */
    public function SettingList(){
        $title = 'setting';

        $data = DB::table('match_score')->get();

        $j = [
            'data'=>$data,
            'Pagetitle'=>$title
        ];

        return view('Back.setting.Index',$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *  修改数据
     */

    public function edit(Request $request){
        $data = $request->except(['s']);
        $s = DB::table('match_score')->where([
            'id'=>$data['id']
        ])->update([
            'match_score'=>$data['match_score']
        ]);

        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 服务费
     */

    public function Serve(){
        $title = 'settingServe';

        $data = DB::table('rate')->get();

        $j = [
            'data'=>$data,
            'Pagetitle'=>$title
        ];

        return view('Back.setting.Serve',$j);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *  修改服务费
     */

    public function Sedit(Request $request){
        $data = $request->except(['s']);
        $s = DB::table('config')->where([
            'id'=>$data['id']
        ])->update([
            'value'=>$data['value']
        ]);
        $retStatus = returnStatus($s);

        return response()->json($retStatus);
    }


}
