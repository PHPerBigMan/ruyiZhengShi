<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminRule extends Model
{
    protected $table = 'admin_rule';
    protected $fillable = ['identifying','id'];
    public $timestamps = false;
}
