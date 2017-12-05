<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'logs';
    //
    public function logs($message,$data){
        $logData = [
            'message'=>$message,
            'data'=>json_encode($data)
        ];
        $this->insert($logData);
    }

    public function logs_a($message,$data){
        $logData = [
            'message'=>$message,
            'data'=>$data
        ];
        $this->insert($logData);
    }
}
