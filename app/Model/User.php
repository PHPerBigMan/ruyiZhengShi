<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';



    public function getTuiInfoAttribute()
    {
        if ($this->is_tui) {
            return '是';
        }
        return '否';
    }

    public function getCreateTimeAttribute($value){
        return date('YmdHis',strtotime($value));
    }

}
