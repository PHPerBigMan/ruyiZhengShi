<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminGroup extends Model
{
    protected $table = 'admin_group';
    protected $fillable = ['gname','ruleid','groupid'];
    public $timestamps = false;
    public function getRuleidAttribute($value){
        return explode(',',$value);
    }
}
