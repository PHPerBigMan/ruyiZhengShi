<?php

namespace App\Http\Controllers\Back;

use App\Model\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index(){
        $data = Article::paginate(15);
        $Pagetitle = "article";
        return view('Back.article.Index',compact('data','Pagetitle'));
    }
    /**
     * @param Request $request
     * @return mixed
     * 编辑页面
     */

    public function edit(Request $request){
        $id = $request->input('id');
        $data = Article::where('id',$id)->first();
        $Pagetitle = "article";
        return view('Back.article.edit',compact('data','Pagetitle'));
    }

    /**
     * @param Request $request
     * @return mixed
     * 保存数据
     */

    public function save(Request $request){
        $data = $request->except(['s']);
        $s = Article::where('id',$data['id'])->update([
            'content'=>$data['content'],
            'title'=>$data['title']
        ]);
        return returnStatus($s);
    }
}
