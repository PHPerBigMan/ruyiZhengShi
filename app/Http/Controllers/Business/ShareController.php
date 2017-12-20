<?php

namespace App\Http\Controllers\Business;

use App\Model\Area;
use App\Model\City;
use App\Model\MatchScore;
use App\Model\Product;
use App\Model\Province;
use App\Model\Share;
use App\Model\UserApply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ShareController extends Controller
{
    protected $model = "";
    protected $user_apply = "";
    public function __construct()
    {
        $this->model = new Share();
        $this->user_apply = new UserApply();
    }

    public function index(Request $request,$cat_id,$sec){
        $ProductCat = DB::table('product_cat')->where(['level'=>1])->get();
        foreach ($ProductCat as $k=>$v){
           $data = DB::table('product_cat')->where(['p_id'=>$v->id])->first();

            $v->sec_id = 20;
            if(!empty($data)){
                $v->sec_cat_name = $data->cat_name;
                $v->sec_id = $data->id;
            }
        }
        //获取房屋类对应二级分类
        $secCat = DB::table('product_cat')->where([
            'p_id'=>$cat_id,
            'is_del'=>0
        ])->get();

        //共享条件
        $score = DB::table('match_score')->where(['type'=>2])->value('match_score');
        //担保品条件
        $property = DB::table('house_property')->where(['cat_id'=>$sec])->get();

        //获取用户基本资料
        $company = DB::table('business_user')->where([
            'id'=>session('business_admin')
        ])->first();
        //获取共享条件
        $share = DB::table('match_score')->where([
            'type'=>2
        ])->value('match_score');
        $title = ['money','decorate','accrual','product_cycle','lending_cycle','lending_type','type','life','house_type','use','house_status','around','license','property','job','count_money'];
        if(!empty($property)){
            foreach($property as $v){
                foreach ($title as $value){
                    $v->$value = json_decode($v->$value,true);
                }
            }
        }
        //获取申请的信息
        $basic = json_decode(DB::table('apply_basic_form')->where([
            'user_id'=>session('business_admin'),
            'type'=>1,
        ])->value('data'));

        if(empty($basic)){
            $basic = json_decode('{}');
            $basic->name = "";
        }
        $data = DB::table('apply_form')->where([
            'user_id'=>session('business_admin'),
            'equipment_type'=>1,
            'cat_id'=>$sec
        ])->select('need_data','data')->first();
        if(!empty($data->need_data)){
            $data->need_data = json_decode($data->need_data);
        }

            if(!empty($data->data)){

                $data->data = json_decode($data->data);
                if($sec != 37){
                    if(!empty($data->data->area)){

                        $data->data->area = explode(" ",$data->data->area);

                        $AllArea = getAreaList($data->data->area[0],$data->data->area[1]);
                        //返回省份列表
                        $province = $AllArea['province'];
                        //返回城市列表
                        $city = $AllArea['city'];
                        //返回地区列表
                        $district = $AllArea['district'];
                    }
                }else{
                    $province = '';
                    $city = '';
                    $district = '';
                }
            }else{
                $AllArea = getArea();
                //返回省份列表
                $province = $AllArea['province'];
                //返回城市列表
                $city = $AllArea['city'];
                //返回地区列表
                $district = $AllArea['district'];
            }


        $j = [
            'score'  =>$score,
            'company'=>$company,
            'share'  =>$share,
            'property'=>empty($property[0]) ? [] : $property[0],
            'product_cat'   =>$ProductCat,
            'secCat'=>$secCat,
            'sec'=>$sec,
            'cat_id'=>$cat_id,
            'province'=>$province,
            'city'=>$city,
            'district'=>$district,
            'title'=>'share',
            'basic'=>$basic,
            'data'=>$data,
            'user_id'=>session('business_admin')
        ];

        $view = "Business.share.index_".$sec;
        return view($view,$j);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 填写共享信息页面
     */

    public function getData($cat_id){

        //共享条件
        $score = DB::table('match_score')->where(['type'=>2])->value('match_score');
        //担保品条件
        $property = DB::table('house_property')->where(['cat_id'=>$cat_id])->get();
        //获取用户基本资料
        $company = DB::table('business_user')->where([
            'id'=>session('business_admin')
        ])->first();
        //获取共享条件
        $share = DB::table('match_score')->where([
            'type'=>2
        ])->value('match_score');
        if(!empty($property)){
            foreach($property as $v){
                $v->money       = json_decode($v->money,true);
                $v->decorate    = json_decode($v->decorate,true);
                $v->accrual     = json_decode($v->accrual,true);
                $v->product_cycle   = json_decode($v->product_cycle,true);
                $v->lending_cycle   = json_decode($v->lending_cycle,true);
                $v->lending_type    = json_decode($v->lending_type,true);
                $v->type            = json_decode($v->type,true);
                $v->life            = json_decode($v->life,true);
                $v->house_type      = json_decode($v->house_type,true);
                $v->property        = json_decode($v->property,true);
            }
        }

        $j = [
            'score'  =>$score,
            'company'=>$company,
            'share'  =>$share,
            'property'=>$property[0]
        ];
        return view('Business.share.get',$j);
    }


    /**
     * @param Request $request
     * @return
     * author hongwenyang
     * method description :  进行共享订单的匹配
     */
    public function SearchData(Request $request){
        $data = $request->except(['s']);

        //基础资料数据
        $basic = ['name','phone','number','share'];
        //需求品资料数据
        $need = ['cat_id','money','product_cycle','accrual','lending_cycle','lending_type','is_issue','discount'];
        //担保品资料数据
        $proper = [
            'area',
            'measure',
            'decoration',
            'type',
            'acquisition_time',
            'years',
            'title_card',
            'credit',
            'mortgage',
            'frozen',
            'certificate_type',
            'owner',
            'is_owner_live',
            'only_housing',
            'certificateA',
            'certificateB',
            'gps',
            'bx',
            'cardj',
            'carxs',
            'carjs',
            'brand',
            'Fayuan',
            'otherPin',
            'otherHuan',
            'YinShou',
            'Shangbiao',
            'TouBao',
            'BeiTouBao',
            'ShouYi',
            'gongZi',
            'onJob',
            'job',
            'heTong',
            'other',
            'bankTime',
            'bank',
            'Jifen',
            'money',
        ];

        //保存各项数据
        foreach($data as $k=>$v){
            if(in_array($k,$basic)){
                //保存基础资料
                $basicData[$k] = $v;
            }
            if(in_array($k,$need)){
                //需求资料
                $needData[$k]= $v;
            }
            if(in_array($k,$proper)){
                //担保品资料
                $properData[$k] = $v;
            }
        }
        if(isset($data['province']) || isset($data['city']) || isset($data['diqu'])){

            $properData['area'] = Province::where('provinceID',$data['province'])->value('province')
                ." ".City::where('cityID',$data['city'])->value('city')
                ." ". Area::where('areaID',$data['diqu'])->value('area');
        }

        //保存所有数据并搜索产品
        $basicDataSave['data'] = json_encode($basicData);
        $basicDataSave['user_id'] = session('business_admin');
        $basicDataSave['type'] = 1;
        $bIsHave = DB::table('apply_basic_form')->where([
            'user_id'=>session('business_admin'),
            'type'=>1
        ])->get();
        //保存基础资料
        if($bIsHave->isEmpty()){
            DB::table('apply_basic_form')->insert($basicDataSave);
        }else{
            DB::table('apply_basic_form')->where([
                'user_id'=>$basicDataSave['user_id'],
                'type'=>1
            ])->update([
                'data'=>$basicDataSave['data']
            ]);
        }
        //保存其余资料
        $oDataSave['user_id'] = session('business_admin');
        $oDataSave['need_data'] = json_encode($needData);
        $oDataSave['data'] = json_encode($properData);
        $oDataSave['equipment_type'] = 1;
        $oDataSave['cat_id'] = $needData['cat_id'];
        $oIsHave = DB::table('apply_form')->where([
            'user_id'=>session('business_admin'),
            'equipment_type'=>1,
            'cat_id'=>$needData['cat_id']
        ])->get();
        if($oIsHave->isEmpty()){
            DB::table('apply_form')->insert($oDataSave);
        }else{
            DB::table('apply_form')->where([
                'user_id'=>$basicDataSave['user_id'],
                'equipment_type'=>1,
                'cat_id'=>$needData['cat_id']
            ])->update($oDataSave);
        }
        return response()->json(['code'=>200]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 查询匹配数据
     */

    public function sort(Request $request){
        $data = $request->except(['s']);
        $url = URL.'/api/Sort?business_id='.session('business_admin').'&cat_id='.$data['cat_id'].'&applicantType=1&sort=0';

        $productData = json_decode(file_get_contents($url),true);
        $retData = returnData($productData['data']);

        return response()->json($retData);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 处理共享订单中的图片
     */

    public function shareImg(Request $request){
        $Img = $request->file('file')->store('img','img');
        return response()->json(['data'=>'/uploads/'.$Img]);
    }


    /**
     * @param $id
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 共享订单不符合要求详情
     */
    public function shareContent($id,$data){
        $content = explode('-,',$data);
        foreach ($content as $k=>$v){
            $content[$k] = explode(',',$v);
        }
        $user_id = session('business_admin');
        //产品匹配分数
        $score = MatchScore::where([
            'type'=>1
        ])->value('match_score');

        return view('/Business/share/shareContent',compact('id','content','score','user_id'));
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 共享订单匹配产品数据
     */
    public function ShareRead($id){
        $data = Product::join('business_user as b','b.id','=','product.business_id')
            ->where([
                'product.id'=>$id
            ])->select('product.content','b.number')->first();
        $data->content = json_decode($data->content);
        $user_id = session('business_admin');
        return view('Business/share/shareProduct',compact('id','data','user_id'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : B端 共享订单列表
     */

    public function sharePay(){
        $user_id = session('business_admin');
        $data = $this->user_apply->getNoPayList($user_id);
        $title = 'share';
        $product_cat = DB::table('product_cat')->where(['level'=>1])->get();

        foreach ($product_cat as $k=>$v){
            $cat_data = DB::table('product_cat')->where(['p_id'=>$v->id])->first();

            $v->sec_id = 20;
            if(!empty($cat_data)){
                $v->sec_cat_name = $cat_data->cat_name;
                $v->sec_id = $cat_data->id;
            }
        }
//        dd($data);
        return view('Business/share/sharePay',compact('data','title','product_cat'));
    }
}
