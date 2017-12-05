<?php

namespace App\Http\Controllers\Back;

use App\Model\IntegralList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IntegralController extends Controller
{
    public function index(Request $request)
    {
        $data = IntegralList::when($request->keyword, function ($query) use ($request){
            return $query->where('user_id', $request->keyword);
        })->latest('create_time')->paginate(15);
        $Pagetitle = 'userIntegral';
        return view('Back.integral.index', compact('data', 'Pagetitle'));
    }
}
