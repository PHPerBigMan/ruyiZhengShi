<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = "admin";

    public function getRoleArrAttribute(){
        if($this->roleid == 0){
            return "超级管理员";
        }else{
            return "子账号";
        }
    }
}
