<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IntegralList extends Model
{
    protected $table = 'integral_list';

    public function getDescAttribute()
    {
        $arr = [
            '注册',
            '推荐他人注册',
            '借款成功',
            '还款无违约',
            '还款已完成',
            '支付消耗',
            '金币保护购买',
            '后台充值'
        ];
        return isset($arr[$this->type]) ? $arr[$this->type] : '未定义说明';
    }

    public function getUserTypeInfoAttribute()
    {
        return $this->user_type == 1? 'B端' : 'C端';
    }
    // 积分变化的增减
    public function getChangeIntegralAttribute()
    {
        $minus = [5,6];
        if (in_array($this->type, $minus)) {
            return '-'.$this->integral;
        }
        return '+'.$this->integral;
    }


    public function getIsGoldAttribute($value){
        if($value == 1){
            return "如易金币";
        }
        return "如易金券";
    }
}
