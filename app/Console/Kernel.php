<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Task;
use App\Models\Record;
use App\Models\Account;
use Illuminate\Support\Facades\Redis;

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
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->call(function () {
            /*$res = Record::where('status', 1)->first();
            $ttl = Redis::ttl("record:".$res->rid);
            $task = Task::where([
                ['state', '=', 1],
                ['tid', '=', $res->tid]
            ])->first();

            if(!empty($res) && $ttl == -1 && !empty($task)){
                Redis::rpush("tid", $res->tid);
                //做过的账号 后面不在使用来做此appid的任务
                $arr_apple_id = Redis::smembers("appid:".$res->appid);
                $account = Account::whereIn('state', [0, 1, 2])
                    ->whereNotIn('apple_id', $arr_apple_id)
                    ->inRandomOrder()
                    ->first();

                Redis::hmset("account:".$account->aid, json_decode($account, true));
                Redis::rpush("task:".$res->tid.":following", $account->aid);

                Redis::del("record:".$res->rid);

                $data['status'] = 215;
                $data['end_time'] = date('Y-m-d H:i:s');
                Record::where('rid', $res->rid)->update($data);
            }*/

            /*$vpnActive = Redis::get("vpnActive");
            if(empty($vpnActive)){
                Redis::setex("vpnActive", 600, 1);
            }*/
        })->cron('*/5 * * * *');
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
