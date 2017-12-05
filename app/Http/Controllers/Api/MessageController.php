<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class MessageController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 消息列表
     */

    public function MessageList(Request $request){
        $UserId  = $request->except(['s']);
        if($UserId['equipment_type'] == 1){
            $UserId['user_id'] = $UserId['business_id'];
            unset($UserId['business_id']);
        }
        $FirData = DB::table('message')->where($UserId)->where(['type'=>0])->select('title','type','content','create_time','id','is_read')->first();
        $SecData = DB::table('message')->where($UserId)->where(['type'=>1])->select('title','type','content','create_time','img','id','is_read')->first();
        $ThiData = DB::table('message')->where($UserId)->where(['type'=>2])->select('title','type','content','create_time','img','id','is_read')->first();
        $retData = [];

        if(!empty($FirData)){
            $retData[0]['title']       = $FirData->title;
            $retData[0]['type']        = $FirData->type;
            $retData[0]['content']     = $FirData->content;
            $retData[0]['id']          = $FirData->id;
            $retData[0]['is_read']     = !empty(DB::table('message')->where([
                'user_id'=>$UserId['user_id'],
                'type'=>$FirData->type,
                'is_read'=>0
            ])->first()) ? 0 : 1;
        }
        if(!empty($SecData)){
            $retData[1]['title']       = $SecData->title;
            $retData[1]['type']        = $SecData->type;
            $retData[1]['content']     = $SecData->content;
            $retData[1]['id']          = $SecData->id;
            $retData[1]['img']         = $SecData->img;
            $retData[1]['is_read']     = !empty(DB::table('message')->where([
                'user_id'=>$UserId['user_id'],
                'type'=>$SecData->type,
                'is_read'=>0
            ])->first()) ? 0 : 1;
        }
        if(!empty($ThiData)){
            $retData[2]['title']       = $ThiData->title;
            $retData[2]['type']        = $ThiData->type;
            $retData[2]['content']     = $ThiData->content;
            $retData[2]['id']          = $ThiData->id;
            $retData[2]['img']         = $ThiData->img;
            $retData[2]['is_read']     = !empty(DB::table('message')->where([
                'user_id'=>$UserId['user_id'],
                'type'=>$ThiData->type,
                'is_read'=>0
            ])->first()) ? 0 : 1;
        }
        sort($retData);
        $retData = returnData($retData);
        return response()->json($retData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 根据type 查询消息列表
     */

    public function MoreMessageList(Request $request){
        $MoreData = $request->except(['s']);
        $where = [
            'type'=>$MoreData['type'],
            'user_id'=> $MoreData['equipment_type'] == 1 ? $MoreData['business_id'] : $MoreData['user_id'],
            'equipment_type'=>$MoreData['equipment_type'],
        ];

        $data       = DB::table('message')->where($where)->select('title','content','id','create_time','img','is_read')->get();
        $retData    = returnData($data);
        return response()->json($retData);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author hongwenyang
     * method description : 消息详情
     */

    public function MessageRead(Request $request){
        $MessageId = $request->except(['s']);
        $MessageData = DB::table('message')->where($MessageId)->select('title','create_time','content','img')->first();
        //修改状态为已读
        DB::table('message')->where($MessageId)->update([
            'is_read'=>1
        ]);
        $retData = returnData($MessageData);
        return response()->json($retData);
    }
}
