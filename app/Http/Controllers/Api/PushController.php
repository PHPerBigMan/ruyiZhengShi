<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JPush\Client as JpushClient;

class PushController extends Controller
{

    public function pushMessage(){
        $client = new JpushClient("42214598c08203e01b6eb2a9","41d668919e0c3082377a6d14 ");
    }
}
