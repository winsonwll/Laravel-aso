<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Account;
use App\Models\Client;
use App\Models\Record;
use Cache;

class TaskController extends Controller
{
    /**
     * 客户端请求任务接口
     * 请求方式：get
     * 请求参数：idfa,udid
     * 返回结果：
     * 'status' => 200,
     * 'msg' => 请求成功,
    'data' => [
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

    //分发方式 绑定手机：一个账号只绑定一个手机  一个账号 一天只用一次
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
        }elseif (strlen($client['udid'])<40){
            return response()->json([
                'status' => 2113,
                'msg' => 'udid格式不正确'
            ]);
        }

        //分发方式
        $bindState = Cache::get('bindState', '1');
        //当天此udid是否已绑定的标识
        $bFlag = true;

        //查询此设备是否存在
        $cid = Client::where('udid',$client['udid'])->value('cid');
        if(empty($cid)){    //不存在此设备
            $bFlag = false;         //当天此设备未绑定
            $cid = Client::create($client)->cid;      //存储此设备

            if(empty($cid)){
                return response()->json([
                    'status' => 2011,
                    'msg' => '保存设备idfa和udid失败'
                ]);
            }
        }

        //取最小请求数
        $vcnt = Task::where('state','1')->min('vcnt');
        if(empty($vcnt)){
            $vcnt = 0;
        }

        $task = Task::where([
            ['state', '=', 1],
            ['vcnt', '=', $vcnt],
        ])->first();

        if(!empty($task)){
            $arr_aid = Record::where([
                ['appid', '=', $task->appid],
                ['status', '=', '200'],
            ])->pluck('aid');                                 //查找做过此appid的账号

            if($bindState==1){      //分发方式  绑定手机
                if(count($arr_aid)){                        //老任务
                    echo 2;
                    if($bFlag){
                        echo 3;
                        $account = Account::whereIn('state', [0, 1, 2])
                            ->whereNotIn('aid', $arr_aid)
                            ->whereNull('flag')
                            ->where('cid', $cid)
                            ->inRandomOrder()
                            ->first();
                        if(empty($account)){
                            echo 4;
                            $account = Account::whereIn('state', [0, 1, 2])
                                ->whereNotIn('aid', $arr_aid)
                                ->whereNull('cid')
                                ->inRandomOrder()
                                ->first();
                        }
                    }else{     //不存在此设备  新建的cid
                        echo 5;
                        $account = Account::whereIn('state', [0, 1, 2])
                            ->whereNotIn('aid', $arr_aid)
                            ->whereNull('cid')
                            ->inRandomOrder()
                            ->first();
                    }
                }else{              //新任务
                    if($bFlag){
                        echo 6;
                        $account = Account::whereIn('state', [0, 1, 2])
                            ->whereNull('flag')
                            ->where('cid', $cid)
                            ->inRandomOrder()
                            ->first();
                        if(empty($account)){
                            echo 7;
                            $account = Account::whereIn('state', [0, 1, 2])
                                ->whereNull('cid')
                                ->inRandomOrder()
                                ->first();
                        }
                    }else{      //如果设备是第一次请求
                        echo 8;
                        $account = Account::whereIn('state', [0, 1, 2])
                            ->whereNull('cid')
                            ->inRandomOrder()
                            ->first();
                    }
                }
            }else{                  //分发方式  不绑定手机
                if(count($arr_aid)){
                    echo 9;
                    $account = Account::whereIn('state', [0, 1, 2])
                        ->whereNotIn('aid', $arr_aid)
                        ->inRandomOrder()
                        ->first();
                }else{
                    echo 10;
                    $account = Account::whereIn('state', [0, 1, 2])
                        ->inRandomOrder()
                        ->first();
                }
            }

            echo '<pre>';
            print_r($account);

            die;

            if(!empty($account)){
                Task::where('tid', $task->tid)->increment('vcnt',1);      //更新请求数

                if($bindState==1) {      //分发方式  绑定手机
                    $bindTimes = Cache::get('bindTimes');       //账号当天可使用的次数

                    $times = Account::where('aid', $account->aid)->value('times');
                    ++$times;

                    if($times>=$bindTimes){
                        Account::where('aid', $account->aid)->update(['state' => 1, 'cid'=> $cid, 'times'=> $times, 'flag'=>1]);   //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败
                    }else{
                        Account::where('aid', $account->aid)->update(['state' => 1, 'cid'=> $cid, 'times'=> $times]);   //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败
                    }
                }else{
                    Account::where('aid', $account->aid)->update(['state' => 1]);   //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败
                }

                if(empty($task->first_time)){
                    Task::where('tid', $task->tid)->update(['first_time' => date('Y-m-d H:i:s')]);        //记录任务首次执行时间
                }

                $data['tid'] = $task->tid;                              //任务id
                $data['appid'] = $task->appid;                          //广告id
                $data['aid'] = $account->aid;                           //账号id
                $data['apple_id'] = $account->apple_id;                //苹果账号id
                $data['cid'] = $cid;                                    //设备id
                $data['status'] = 1;                                    //任务完成状态码  0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错
                $data['start_time'] = date('Y-m-d H:i:s');            //任务开始时间
                $data['ip'] = $request->getClientIp();                  //ip

                $rid = Record::create($data)->rid;   //生成任务记录rid
                if(!empty($rid)){
                    //成功响应
                    return response()->json([
                        'status' => 200,
                        'msg' => '请求成功',
                        'data' => [
                            'rid' => $rid,                                      //任务记录id
                            'appid' => trim($task->appid),                     //appid
                            'appkey' => trim($task->appkey),                  //任务名称
                            'timeout' => $task->timeout,                      //超时时间  默认600毫秒
                            'download' => $task->download,                    //是否下载app  1 是  0 否
                            'active' => $task->active,                        //是否激活app  1 是  0 否
                            'postLoginStore' => $task->postLoginStore,      //是否后登录  1 是  0 否
                            'openDetailDirectly' => $task->openDetailDirectly,  //是否滑动  0 模式一：正常模式 滑动  1 模式二：简约模式  只滑动到60多位
                            'apple_id' => trim($account->apple_id),          //苹果账号
                            'apple_pwd' => trim($account->apple_pwd),        //苹果密码
                        ]
                    ]);
                }else{
                    //失败响应
                    return response()->json([
                        'status' => 2014,                                //生成任务记录失败
                        'msg' => '生成任务记录失败'
                    ]);
                }
            }else{
                Task::where('tid', $task->tid)
                    ->update([
                        'state' => 3,
                        'last_time' => date('Y-m-d H:i:s')      //记录任务完成时间
                    ]);

                return response()->json([
                    'status' => 2013,                                   //没有可使用的苹果账号
                    'msg' => '没有可使用的苹果账号，任务结束'
                ]);
            }
        }else{
            if($bindState==1) {      //分发方式  绑定手机
                $msg = '没有分发方式为绑定手机的在线任务';
            }else{
                $msg = '没有分发方式为不绑定手机的在线任务';
            }
            return response()->json([
                'status' => 2012,                                       //没有在线的任务
                'msg' => $msg
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
    'msg' => '任务成功'
     */
    public function finishTask(Request $request)
    {
        $data['rid'] = trim($request->input('rid'));
        $data['status'] = trim($request->input('status'));

        //验证
        if(empty($data['rid']) || empty($data['status']) ){
            return response()->json([
                'status' => 2112,
                'msg' => '非法提交'
            ]);
        }

        //判断是否存在提交的rid
        $record = Record::find($data['rid']);
        if(empty($record)){
            return response()->json([
                'status' => 2112,
                'msg' => '非法提交'
            ]);
        }

        //不能重复提交
        if(!empty($record->end_time)){
            return response()->json([
                'status' => 2114,
                'msg' => '不能重复提交'
            ]);
        }

        $tid = $record->tid;
        $aid = $record->aid;

        //验证任务完成状态
        if($data['status'] == 200){      //任务成功
            $task = Task::find($tid);

            $success_count = $task->success_count;
            $count = $task->count;
            ++$success_count;

            $data['end_time'] = date('Y-m-d H:i:s');

            //执行更新任务记录
            $res = Record::where('rid', $data['rid'])->update($data);
            if(!empty($res)){
                if($success_count < $count){
                    $result = Task::where('tid', $tid)->update(['success_count' => $success_count]);
                }else{
                    $result = Task::where('tid', $tid)
                        ->update([
                            'success_count' => $success_count,
                            'state' => 3,
                            'last_time' => date('Y-m-d H:i:s')      //记录任务完成时间
                        ]);
                }

                $result2 = Account::where('aid', $aid)->update(['state' => 2]); //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败

                if($result && $result2){
                    //更新任务成功数和账号状态成功
                    return response()->json([
                        'status' => 200,
                        'msg' => '更新任务成功数和账号状态成功'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 2016,
                    'msg' => '更新任务记录失败'
                ]);
            }
        }else{    //任务失败
            /*任务完成状态码  0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错*/
            //分发方式
            $bindState = Cache::get('bindState', '1');

            if ($data['status'] == 205){
                if($bindState==1) {      //分发方式  绑定手机
                    $data_account = ['state' => 3, 'cid'=> Null, 'flag'=> Null, 'times'=> Null];
                }else{
                    $data_account = ['state' => 3];
                }
            }elseif ($data['status'] == 212 || $data['status'] == 204){
                if($bindState==1) {      //分发方式  绑定手机
                    $data_account = ['state' => 4, 'cid'=> Null, 'flag'=> Null, 'times'=> Null];
                }else{
                    $data_account = ['state' => 4];
                }
            }else{
                if($bindState==1) {      //分发方式  绑定手机
                    $data_account = ['state' => 0, 'cid'=> Null, 'flag'=> Null, 'times'=> Null];
                }else{
                    $data_account = ['state' => 0];
                }
            }
            Account::where('aid',$aid)->update($data_account);

            $data['end_time'] = date('Y-m-d H:i:s');

            //执行更新
            $res = Record::where('rid', $data['rid'])->update($data);
            if($res){
                //任务失败更新成功
                return response()->json([
                    'status' => 200,
                    'msg' => '任务失败状态更新成功'
                ]);
            }else{
                return response()->json([
                    'status' => 2016,                                           //更新任务记录失败
                    'msg' => '更新任务记录失败'
                ]);
            }
        }
    }
}