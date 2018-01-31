<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController1 extends Controller
{
    /**
     * 客户端请求任务接口
     * 请求方式：get
     * 请求参数：idfa
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
    'apple_id' => $account->apple_id,           //苹果账号
    'apple_pwd' => $account->apple_pwd          //苹果密码
    ]
     */
    public function distributeTask(Request $request)
    {
        $client['idfa'] = trim($request->input('idfa'));                //用于说明哪个客户端来请求任务

        //验证idfa
        if(empty($client['idfa'])){
            return response()->json([
                'status' => 2111,                                       //没有idfa的非法请求
                'msg' => '没有idfa的非法请求'
            ]);
        }elseif (strlen($client['idfa'])<36){
            return response()->json([
                'status' => 2113,                                       //idfa格式不正确
                'msg' => 'idfa格式不正确'
            ]);
        }

        $cid = \DB::table('aso_client')
            ->where('idfa',$client['idfa'])
            ->value('cid');                                             //查询此设备是否存在

        if(empty($cid)){    //不存在此设备
            $client['created_at'] = date('Y-m-d H:i:s');
            $cid = \DB::table('aso_client')->insertGetId($client);      //存储此设备

            if(empty($cid)){
                return response()->json([
                    'status' => 2011,                                    //保存设备idfa失败
                    'msg' => '保存设备idfa失败'
                ]);
            }
        }

        $vcnt = \DB::table('aso_task')
            ->where('state',1)
            ->whereColumn('vcnt', '<=', 'count')
            ->min('vcnt');

        if(empty($vcnt)){
            $vcnt = 0;
        }

        $task = \DB::table('aso_task')->where([
            ['state', '=', 1],
            ['vcnt', '=', $vcnt]
        ])->whereColumn('vcnt', '<=', 'count')
            ->first();                                            //不重复取出一条已上线的任务

        if(!empty($task)){
            \DB::table('aso_task')
                ->where('tid', $task->tid)
                ->increment('vcnt', 1);    //更新请求数

            $account = \DB::table('aso_account')
                ->whereIn('state', [0, 2])
                ->inRandomOrder()
                ->first();                                             //不重复随机取出一个未使用的苹果账号

            if(!empty($account)){
                \DB::table('aso_account')                           //更新账号使用中状态
                ->where('aid', $account->aid)
                    ->update(['state' => 1]);                         //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败

                if(empty($task->first_time)){
                    \DB::table('aso_task')
                        ->where('tid', $task->tid)
                        ->update(['first_time' => date('Y-m-d H:i:s')]);        //记录任务首次执行时间
                }

                $data['tid'] = $task->tid;                              //任务id
                $data['aid'] = $account->aid;                           //账号id
                $data['cid'] = $cid;                                    //设备id
                $data['status'] = 1;                                    //任务完成状态码  0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错
                $data['start_time'] = date('Y-m-d H:i:s');            //任务开始时间

                $rid = \DB::table('aso_task_record')->insertGetId($data);   //生成任务记录rid

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
                return response()->json([
                    'status' => 2013,                                   //没有可使用的苹果账号
                    'msg' => '没有可使用的苹果账号'
                ]);
            }
        }else{
            return response()->json([
                'status' => 2012,                                       //没有在线的任务
                'msg' => '没有在线的任务'
            ]);
        }
    }

    /**
     * 客户端完成任务后接口
     * 提交方式：post
     * 提交参数：idfa  rid  status
     * 任务完成状态码  0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错
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
        $record = \DB::table('aso_task_record')->where('rid',$data['rid'])->first();

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

        //验证任务完成状态
        if($data['status'] == 200){      //任务成功
            $aid = $record->aid;

            $task = \DB::table('aso_task')->where('tid',$tid)->first();

            $success_count = $task->success_count;
            $count = $task->count;

            ++$success_count;

            //每次提交成功时 需要判断成功数跟投放总量的大小
            if($success_count < $count){
                $data['end_time'] = date('Y-m-d H:i:s');

                //执行更新任务记录
                $res = \DB::table('aso_task_record')->where('rid', $data['rid'])->update($data);

                if(!empty($res)){
                    $result = \DB::table('aso_task')->where('tid', $tid)->update([
                        'success_count' => $success_count
                    ]);

                    $result2 = \DB::table('aso_account')
                        ->where('aid', $aid)
                        ->update(['state' => 2]);                   //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败

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
            }elseif ($success_count == $count){
                $data['end_time'] = date('Y-m-d H:i:s');

                //执行更新任务记录
                $res = \DB::table('aso_task_record')->where('rid', $data['rid'])->update($data);

                if(!empty($res)){
                    $result = \DB::table('aso_task')
                        ->where('tid', $tid)
                        ->update([
                            'success_count' => $count,
                            'state' => 3,
                            'last_time' => date('Y-m-d H:i:s')      //记录任务完成时间
                        ]);

                    $result2 = \DB::table('aso_account')
                        ->where('aid', $aid)
                        ->update(['state' => 2]);                   //账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败

                    if($result && $result2){
                        //更新任务成功数和账号状态成功
                        return response()->json([
                            'status' => 200,
                            'msg' => '任务成功，已完成投放总量'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 2016,                                            //更新任务记录失败
                        'msg' => '更新任务记录失败'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 2015,
                    'msg' => '任务已结束，已完成投放总量'
                ]);
            }
        }else{    //任务失败
            \DB::table('aso_task')
                ->where('tid', $tid)
                ->decrement('vcnt', 1);    //更新请求数

            /*任务完成状态码  0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错*/
            if ($data['status'] == 205){
                $aid = \DB::table('aso_task_record')
                    ->where('rid',$data['rid'])
                    ->value('aid');
                \DB::table('aso_account')->where('aid',$aid)->update(['state' => 3]);
            }
            if ($data['status'] == 212){
                $aid = \DB::table('aso_task_record')
                    ->where('rid',$data['rid'])
                    ->value('aid');
                \DB::table('aso_account')->where('aid',$aid)->update(['state' => 4]);
            }

            $data['end_time'] = date('Y-m-d H:i:s');

            //执行更新
            $res = \DB::table('aso_task_record')->where('rid', $data['rid'])->update($data);
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