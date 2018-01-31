<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Account;
use App\Models\Client;
use App\Models\Record;
use Illuminate\Support\Facades\Redis;

class TaskController extends Controller
{
    public function getAddr(Request $request){
        $data['ip'] = $request->getClientIp();
        Redis::rpush("addr", $data['ip']);
        print_r('恭喜您，访问成功');
    }


    /**
     *  客户端请求vpn设置接口
     *  0 ：关闭  1 ：开启
     */
    public function getVpn(){
        $vpnActive = Redis::get("vpnActive");
        if(empty($vpnActive)){
            $vpnActive = 0;
        }

        return response()->json([
            'status' => 200,
            'msg' => '请求成功',
            'data' => [
                'vpnActive' => $vpnActive
            ]
        ]);
    }
    
    /**
     * 客户端请求任务接口
     * 请求方式：get
     * 请求参数：idfa,udid
     * 返回结果：
     * 'status' => 200,
     * 'msg' => 请求成功,
     * 'data' => [
        'rid' => $rid,                               //任务记录id
        'appid' => $task->appid,                    //appid
        'appkey' => $task->appkey,                  //任务名称
        'timeout' => $task->timeout,                //超时时间  默认600毫秒
        'download' => $task->download,              //是否下载app  1 是  0 否
        'active' => $task->active,                  //是否激活app  1 是  0 否
        'postLoginStore' => $task->postLoginStore, //是否后登录  1 是  0 否
        'openDetailDirectly' => $task->openDetailDirectly, //是否滑动  0 模式一：正常模式 滑动  1 模式二：简约模式  只滑动到60多位
        'apple_id' => $account->apple_id,           //苹果账号
        'apple_pwd' => $account->apple_pwd          //苹果密码
     ]
     */

    public function distributeTask(Request $request)
    {
        $client['idfa'] = trim($request->input('idfa'));                //用于说明哪个客户端来请求任务
        $client['udid'] = trim($request->input('udid'));                //客户端唯一标识

        //验证idfa
        if(empty($client['idfa'])){
            return response()->json([
                'status' => 2111,
                'msg' => '没有idfa的非法请求'
            ]);
        }elseif (strlen($client['idfa'])<36){
            return response()->json([
                'status' => 2113,
                'msg' => 'idfa格式不正确'
            ]);
        }
        //验证udid
        if(empty($client['udid'])){
            return response()->json([
                'status' => 2111,
                'msg' => '没有udid的非法请求'
            ]);
        }elseif (strlen($client['udid'])<30){
            return response()->json([
                'status' => 2113,
                'msg' => 'udid格式不正确'
            ]);
        }

        //查询此设备是否存在
        $cid = Redis::hget("udid:".$client['udid'], 'cid');
        //不存在此设备
        if(empty($cid)){
            $cid = Client::create($client)->cid;      //存储此设备

            if(!empty($cid)){
                Redis::hmset("udid:".$client['udid'], array("cid"=>$cid, "udid"=>$client['udid']));
            }else{
                return response()->json([
                    'status' => 2011,
                    'msg' => '保存设备idfa和udid失败'
                ]);
            }
        }

        //取任务
        $tid = Redis::lpop("tid");
        $state = Redis::hget("task:".$tid, 'state');
        if($state == 1){
            Redis::rpush("tid", $tid);
        }else{
            return response()->json([
                'status' => 2012,
                'msg' => '没有在线的任务'
            ]);
        }

        $task = Redis::hgetall("task:".$tid);

        if(!empty($task)){
            $aid = Redis::lpop("task:".$tid.":following");
            $account = Redis::hgetall("account:".$aid);

            if(!empty($account)){
                //更新账号状态
                Account::where('aid', $aid)->update(['state' => 1]);

                //更新请求数
                $vcnt = Redis::hincrby("task:".$tid, 'vcnt', 1);
                $data_task = ['vcnt' => $vcnt];

                //记录任务首次执行时间
                if(empty($task['first_time'])){
                    $data_task = [
                        'vcnt' => $vcnt,
                        'first_time' => date('Y-m-d H:i:s')
                    ];
                    Redis::hset("task:".$tid, 'first_time', date('Y-m-d H:i:s'));
                }
                Task::where('tid', $tid)->update($data_task);

                $data['tid'] = $tid;                                    //任务id
                $data['appid'] = $task['appid'];                       //广告id
                $data['aid'] = $aid;                                    //账号id
                $data['apple_id'] = $account['apple_id'];             //苹果账号id
                $data['cid'] = $cid;                                    //设备id
                $data['status'] = 1;                                    //任务完成状态码  0:未开始 1:进行中
                $data['start_time'] = date('Y-m-d H:i:s');            //任务开始时间
                $data['ip'] = $request->getClientIp();                   //ip

                $rid = Record::create($data)->rid;                       //生成任务记录
                Redis::hmset("record:".$rid, $data);
                Redis::EXPIRE("record:".$rid,300);

                if(!empty($rid)){
                    //成功响应
                    return response()->json([
                        'status' => 200,
                        'msg' => '请求成功',
                        'data' => [
                            'rid' => $rid,                                       //任务记录id
                            'appid' => trim($task['appid']),                    //appid
                            'appkey' => trim($task['appkey']),                  //任务名称
                            'timeout' => $task['timeout'],                      //超时时间  默认180秒
                            'download' => $task['download'],                    //是否下载app  1 是  0 否
                            'active' => $task['active'],                        //是否激活app  1 是  0 否
                            'postLoginStore' => $task['postLoginStore'],      //是否后登录  1 是  0 否
                            'openDetailDirectly' => $task['openDetailDirectly'],  //是否滑动  0 模式一：正常模式 滑动  1 模式二：简约模式  只滑动到60多位
                            //'vpnActive' => $task['vpnActive'],  //是否开启VPN  0 ：不开启  1 ：开启
                            'apple_id' => trim($account['apple_id']),          //苹果账号
                            'apple_pwd' => trim($account['apple_pwd']),        //苹果密码
                        ]
                    ]);
                }else{
                    //失败响应
                    return response()->json([
                        'status' => 2014,
                        'msg' => '生成任务记录失败'
                    ]);
                }
            }else{
                if($state == 1){
                    //做过的账号 后面不在使用来做此appid的任务
                    $batch = Redis::hgetall("tidBatch:".$tid);
                    $arr_apple_id = Redis::smembers("appid:".$task['appid']);
                    $account = Account::whereIn('state', [0, 1, 2])
                        ->whereIn('batch', $batch)
                        ->whereNotIn('apple_id', $arr_apple_id)
                        ->inRandomOrder()
                        ->first();

                    //Redis::rpush("tid", $tid);
                    Redis::hmset("account:".$account->aid, json_decode($account, true));
                    Redis::rpush("task:".$tid.":following", $account->aid);
                }else{
                    return response()->json([
                        'status' => 2013,
                        'msg' => '没有可使用的苹果账号'
                    ]);
                }
            }
        }else{
            return response()->json([
                'status' => 2012,
                'msg' => '没有在线的任务'
            ]);
        }
    }

    /**
     * 客户端完成任务后接口
     * 提交方式：post
     * 提交参数：rid  status
     * 任务完成状态码  0:未开始 1:进行中  200:成功  204:账号或密码不对  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错
     * 返回结果：
     * 'status' => 200,
     * 'msg' => '任务成功'
     */

    public function finishTask(Request $request)
    {
        $data['rid'] = trim($request->input('rid'));
        $data['status'] = trim($request->input('status'));
        $data['end_time'] = date('Y-m-d H:i:s');

        //验证
        if(empty($data['rid']) || empty($data['status']) ){
            return response()->json([
                'status' => 2112,
                'msg' => '非法提交'
            ]);
        }

        //判断是否存在提交的rid
        $record = Redis::hgetall("record:".$data['rid']);
        if(empty($record)){
            return response()->json([
                'status' => 2112,
                'msg' => '非法提交'
            ]);
        }
        //不能重复提交
        if(!empty($record['end_time'])){
            return response()->json([
                'status' => 2114,
                'msg' => '不能重复提交'
            ]);
        }

        //执行更新任务记录
        $res = Record::where('rid', $data['rid'])->update($data);
        if(!empty($res)){
            Redis::del("record:".$data['rid']);

            $tid = $record['tid'];
            $aid = $record['aid'];
            $task = Redis::hgetall("task:".$tid);

            //验证任务完成状态
            if($data['status'] == 200){     //任务成功
                //更新成功数
                $success_count = Redis::hincrby("task:".$tid, 'success_count', 1);
                $count = $task['count'];

                if($success_count < $count){
                    $data_task = ['success_count' => $success_count];
                }else{
                    $data_task = [
                        'success_count' => $success_count,
                        'state' => 3,
                        'last_time' => date('Y-m-d H:i:s')
                    ];
                    Redis::lrem("tid", 0, $tid);
                    //Redis::del("task:".$tid);
                    Redis::del("task:".$tid.":following");
                    Redis::expire("task:".$tid, 300);
                    Redis::del("tidBatch:".$tid);
                }

                $result = Task::where('tid', $tid)->update($data_task);
                $result2 = Account::where('aid', $aid)->update(['state' => 2]); //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败
                if(!empty($result) && !empty($result2)){
                    return response()->json([
                        'status' => 200,
                        'msg' => '更新任务成功数和账号状态成功'
                    ]);
                }else{
                    return response()->json([
                        'status' => 2017,
                        'msg' => '更新任务成功数和账号状态失败'
                    ]);
                }
            }else{      //任务失败
                /*任务完成状态码
                    0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错*/
                //Redis::rpush("tid", $tid);

                if ($data['status'] == 205){
                    $data_account = ['state' => 3];
                }elseif ($data['status'] == 204 || $data['status'] == 212){
                    $data_account = ['state' => 4];
                }else{
                    $data_account = ['state' => 0];
                }

                Redis::del("account:".$aid);
                Redis::lrem("task:".$tid.":following", 0, $aid);

                $result2 = Account::where('aid', $aid)->update($data_account);
                if(!empty($result2)){
                    //做过的账号 后面不在使用来做此appid的任务
                    $arr_apple_id = Redis::smembers("appid:".$task['appid']);
                    $account = Account::whereIn('state', [0, 1, 2])
                        ->whereNotIn('apple_id', $arr_apple_id)
                        ->inRandomOrder()
                        ->first();

                    Redis::hmset("account:".$account->aid, json_decode($account, true));
                    Redis::rpush("task:".$tid.":following", $account->aid);

                    return response()->json([
                        'status' => 200,
                        'msg' => '任务失败状态更新成功'
                    ]);
                }else{
                    return response()->json([
                        'status' => 2018,
                        'msg' => '任务失败状态更新失败'
                    ]);
                }
            }
        }else{
            return response()->json([
                'status' => 2016,
                'msg' => '更新任务记录失败'
            ]);
        }
    }
}