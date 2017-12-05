<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/8/22
 * Time: 14:38
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\BusinessUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class BusinessSettledController
 * @package App\Http\Controllers\Api
 * B端用户部分操作类
 */

class BusinessSettledController extends Controller {
    protected $business_user = "";
    public function __construct()
    {
        $this->business_user = new BusinessUser();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 保存用户信息
     */

    public function SaveData(Request $request){
        $SaveData = $request->except(['s']);
        $return = $this->business_user->SaveDataModel($SaveData);
        return response()->json($return);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 修改金融管家电话
     */

    public function SaveCompanyPhone(Request $request){
        $CompanyPhone = $request->except(['s']);
        //这部分后面需要删掉
        $request->session()->put('code',1);
        $return = $this->business_user->SaveHouseKeeper($CompanyPhone);
        return response()->json($return);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 公司资料
     */

    public function UserData(Request $request){
        $UserId = $request->input('user_id');
        $UserData = DB::table('business_user')->where(['id'=>$UserId])
            ->select([
                'companyName',
                'companyCode',
                'companyAddress',
                'companyLegal',
                'phone',
                'companyHouse',
                'companyHousePhone',
                'qualification',
                'type',
                'remark',
                'pic',
                'money'
            ])
            ->first();
        if($UserData){
            $retJson['code'] = 200;
            $retJson['msg']  = '获取数据成功';
            $retJson['data'] = $UserData;
        }else{
            $retJson['code'] = 404;
            $retJson['msg']  = '获取数据失败';
            $retJson['data'] = "{}";
        }

        return response()->json($retJson);
    }
}