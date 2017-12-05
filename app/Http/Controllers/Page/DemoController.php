<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\BaseController;

class DemoController extends BaseController
{
    //
    public function getErro(){
        $a = [
            '1年内逾期超过3次或超过90天',
            '1年内逾期少于3次且少于90天',
            '信用良好无逾期'
        ];

        dd(response()->json($a));
        return $this->err('填写错误信息',404);
    }
}
