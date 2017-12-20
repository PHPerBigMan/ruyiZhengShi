<?php

namespace App\Http\Controllers\Back;

use App\Model\DataTotal;
use App\Model\ProductCat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DataTotalController extends Controller
{
    protected  $cat,$catName,$cat_id,$dataTotal;
    public function __construct(Request $request)
    {
        $pid = $request->except(['s']);
        $this->cat = ProductCat::where('p_id',$pid)->get();

        // 获取一级分类的分类名
        $this->catName = ProductCat::where('id',$pid)->value('cat_name');

        $this->cat_id=  $pid;

        $this->dataTotal = new DataTotal();
    }

    public function TotalType(){
        $Pagetitle  = "data";

        $cat = ProductCat::where('level',1)->get();

        $area = $this->AreaList();

        return view('Back.data.choose',compact('Pagetitle','cat','area'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * author hongwenyang
     * method description : 获取不同子分类的成交数据
     */

    public function CatTodayTotal(Request $request){
        $totalType = $request->input('totalType');

        $catData = array();
        foreach ($this->cat as $k=>$v){
            $catData[$k] = $v->cat_name;
        }

        // 数据统计
        $catData = json_encode($catData);

        $catName = $this->catName;
        // 获取分类下的统计数据
        $array = ['today','month'];
        if(in_array($totalType,$array)){
            $count = json_encode($this->dataTotal->GetTotal($this->cat_id,$totalType));
        }else if($totalType == "Allmonth"){
            // 获取 月度数据
            $count          = $this->dataTotal->GetTotalAll($this->cat_id,$totalType);
            $catData        = json_encode($count['title']);
            $count          = json_encode($count['data']);
        }else if($totalType == "year"){
            // 获取 年度数据
            $year           = date('Y',time());
            $count          = json_encode($this->dataTotal->GetTotalYear());
            $catData        = json_encode([$year]);
        }else{
            // 根据 省份成交额排行榜查询数据 （详细到月份）
            $count          = $this->dataTotal->GetTotalAll($this->cat_id,$totalType,0);
            $catData        = json_encode($count['title']);
            $count          = json_encode($count['data']);
        }
        $Pagetitle  = "data";
        return view('Back.data.data',compact('Pagetitle','catData','catName','count'));
    }


    public function AreaList(){
        $data = $this->dataTotal->GetArea();
        return $data;
    }
}
