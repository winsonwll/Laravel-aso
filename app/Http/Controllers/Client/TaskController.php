<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{

    /**
     * 客户端请求任务接口
     */
    public function distributeTask(Request $request)
    {
        $client['idfa'] = trim($request->input('idfa'));                //用于说明哪个客户端来请求任务

        if(empty($client['idfa'])){
            return response()->json([
                'status' => 2111,                                       //没有idfa的非法请求
                'msg' => '没有idfa的非法请求'
            ]);
        }

        $hasCid = \DB::table('aso_client')->where('idfa',$client['idfa'])->value('cid');

        if(empty($hasCid)){     //不存在此设备
            $client['created_at'] = date('Y-m-d H:i:s');
            $cid = \DB::table('aso_client')->insertGetId($client);      //存储一个设备

            if(!empty($cid)){
                $task = \DB::table('aso_task')
                    ->where('state',1)
                    ->inRandomOrder()
                    ->distinct()
                    ->first();                                          //随机不重复取出一条已上线的任务

                if(!empty($task)){
                    $account = \DB::table('aso_account')
                        ->where('state',1)
                        ->inRandomOrder()
                        ->distinct()
                        ->first();                                      //随机不重复取出一个可用的苹果账号

                    if(!empty($account)){
                        $data['tid'] = $task->tid;                              //任务id
                        $data['appid'] = $task->appid;                          //appid
                        $data['aid'] = $account->aid;                           //账号id
                        $data['cid'] = $cid;                                    //设备id
                        $data['status'] = 1;                                    //任务完成状态  0 未开始 1 已开始 2 成功 3 失败
                        $data['start_time'] = date('Y-m-d H:i:s');            //任务开始时间

                        $rid = \DB::table('aso_task_record')->insertGetId($data);   //生成任务记录rid

                        if(!empty($rid)){
                            //成功响应
                            return response()->json([
                                'status' => 200,
                                'msg' => '请求成功',
                                'data' => [
                                    'rid' => $rid,                               //任务记录id
                                    'appid' => $task->appid,                    //appid
                                    'appkey' => $task->appkey,                  //任务名称
                                    'apple_id' => $account->apple_id,           //苹果账号
                                    'apple_pwd' => $account->apple_pwd          //苹果密码
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
                            'status' => 2013,                                   //派发苹果账号失败
                            'msg' => '派发苹果账号失败'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 2012,                                       //没有任务了
                        'msg' => '没有任务了'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 2011,                                           //保存设备idfa失败
                    'msg' => '保存设备idfa失败'
                ]);
            }
        }else{                  //存在此设备
            //如果是激活的需求  则同一个idfa不能做同一个appid的任务
            /*$arr_appid = \DB::table('aso_task_record')
                ->where('cid',$hasCid)
                ->whereBetween('status', [1,2])
                ->pluck('appid');*/

            //非激活的需求 则同一个idfa不能用同一个苹果账号
            $arr_aid = \DB::table('aso_task_record')
                ->where('cid',$hasCid)
                ->whereBetween('status', [1,2])
                ->pluck('aid');

            if(!empty($arr_aid)){
                $task = \DB::table('aso_task')
                    ->where('state',1)
                    ->inRandomOrder()
                    ->distinct()
                    ->first();                                                      //随机不重复取出一条已上线的任务

                if(!empty($task)){
                    $account = \DB::table('aso_account')
                        ->where('state',1)
                        ->whereNotIn('aid', $arr_aid)
                        ->inRandomOrder()
                        ->distinct()
                        ->first();                                                  //随机不重复取出一个可用的苹果账号

                    if(!empty($account)){
                        $data['tid'] = $task->tid;                                   //任务id
                        $data['appid'] = $task->appid;                              //appid
                        $data['aid'] = $account->aid;                                //账号id
                        $data['cid'] = $hasCid;                                      //设备id
                        $data['status'] = 1;                                         //任务完成状态  0 未开始 1 已开始 2 成功 3 失败
                        $data['start_time'] = date('Y-m-d H:i:s');                 //任务开始时间

                        $rid = \DB::table('aso_task_record')->insertGetId($data);   //生成任务记录

                        if(!empty($rid)){
                            //成功响应
                            return response()->json([
                                'status' => 200,
                                'msg' => '请求成功',
                                'data' => [
                                    'rid' => $rid,                                   //任务记录id
                                    'appid' => $task->appid,                        //appid
                                    'appkey' => $task->appkey,                      //任务名称
                                    'apple_id' => $account->apple_id,               //苹果账号
                                    'apple_pwd' => $account->apple_pwd              //苹果密码
                                ]
                            ]);
                        }else{
                            //失败响应
                            return response()->json([
                                'status' => 2014,                                   //生成任务记录失败
                                'msg' => '生成任务记录失败'
                            ]);
                        }
                    }else{
                        //失败响应
                        return response()->json([
                            'status' => 2013,                                       //派发苹果账号失败
                            'msg' => '派发苹果账号失败'
                        ]);
                    }
                }else{
                    //失败响应
                    return response()->json([
                        'status' => 2012,                                           //没有任务了
                        'msg' => '没有任务了'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 2015,                                               //此idfa的任务记录中账号信息不存在
                    'msg' => '此idfa的任务记录中账号信息不存在'
                ]);
            }
        }
    }

    /**
     * 客户端完成任务后接口
     */
    public function finishTask(Request $request)
    {
        $data['rid'] = trim($request->input('rid'));
        $data['status'] = trim($request->input('status'));

        //验证
        if(empty($data['rid']) || empty($data['status']) ){
            return response()->json([
                'status' => 2112,
                'msg' => '非法提交任务完成状态'
            ]);
        }

        //验证任务完成状态
        if($data['status'] == 2){      //任务成功
            $tid = \DB::table('aso_task_record')
                ->where('rid',$data['rid'])
                ->value('tid');

            $success_count = \DB::table('aso_task_record')
                ->join('aso_task', 'aso_task_record.tid', '=', 'aso_task.tid')
                ->where('rid',$data['rid'])
                ->value('success_count');

            $count = \DB::table('aso_task_record')
                ->join('aso_task', 'aso_task_record.tid', '=', 'aso_task.tid')
                ->where('rid',$data['rid'])
                ->value('count');

            $success_count++;

            //每次提交成功时 需要判断成功数跟投放总量的大小
            if($success_count < $count){
                $data['status'] = 2;     //任务成功
                $data['end_time'] = date('Y-m-d H:i:s');

                //执行更新任务记录
                $res = \DB::table('aso_task_record')->where('rid', $data['rid'])->update($data);

                if(!empty($res)){
                    $result = \DB::table('aso_task')->where('tid', $tid)->update(['success_count' => $success_count]);
                    if($result){
                        //更新成功
                        return response()->json([
                            'status' => 201,
                            'msg' => '任务成功'
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 2016,                                           //更新任务记录失败
                        'msg' => '更新任务记录失败'
                    ]);
                }
            }elseif ($success_count == $count){
                $data['status'] = 2;     //任务成功
                $data['end_time'] = date('Y-m-d H:i:s');

                //执行更新任务记录
                $res = \DB::table('aso_task_record')->where('rid', $data['rid'])->update($data);

                if(!empty($res)){
                    $result = \DB::table('aso_task')->where('tid', $tid)->update([
                        'success_count' => $count,
                        'state' => 3
                    ]);
                    if($result){
                        //更新成功
                        return response()->json([
                            'status' => 202,
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
                    'status' => 203,
                    'msg' => '已完成投放总量，任务已结束'
                ]);
            }
        }elseif ($data['status'] == 3){    //任务失败
            $code = trim($request->input('code'));

            if(empty($code)){
                return response()->json([
                    'status' => 2112,
                    'msg' => '非法提交任务完成状态'
                ]);
            }

            /*204 苹果账号或密码错误   205 账号禁用    206 ip错误    207 搜索错误    208 购买错误    209 超时*/
            if ($code == 204 || $code == 205){
                $aid = \DB::table('aso_task_record')->where('rid',$data['rid'])->value('aid');

                $account['state'] = 2;
                $account['updated_at'] = date('Y-m-d H:i:s');

                \DB::table('aso_account')->where('aid',$aid)->update($account);
            }

            $data['status'] = 3;                                                //任务完成状态：失败
            $data['code'] = $code;                                              //失败状态码
            $data['end_time'] = date('Y-m-d H:i:s');

            //执行更新
            $res = \DB::table('aso_task_record')->where('rid', $data['rid'])->update($data);
            if($res){
                //任务失败更新成功
                return response()->json([
                    'status' => 2017,
                    'msg' => '任务失败状态更新成功'
                ]);
            }else{
                return response()->json([
                    'status' => 2016,                                           //更新任务记录失败
                    'msg' => '更新任务记录失败'
                ]);
            }
        }else{
            return response()->json([
                'status' => 2112,
                'msg' => '非法提交任务完成状态'
            ]);
        }
    }
}