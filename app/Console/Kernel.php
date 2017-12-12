<?php

namespace App\Console;

use App\Model\BusinessUser;
use App\Model\IntegralChange;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 每5分钟运行一次
        $schedule->call(function (){
            // 查询购买排名保护的企业 保护时间是否已到规定时间
            $isBuyCompany = BusinessUser::where('is_buy',1)->get();
            if(!$isBuyCompany->isEmpty()){
                // 如果不为空则进行判断
                $time = time();
                foreach($isBuyCompany as $v){
                    if($v->stop_time < $time){
                        // 保护时间已经到 更改 保护状态
                        BusinessUser::where('id',$v->id)->update(['is_buy',0]);
                        // 增加库存
                        IntegralChange::where('id',1)->increment('stock');
                    }
                }
            }
        })->everyFiveMinutes();;
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
