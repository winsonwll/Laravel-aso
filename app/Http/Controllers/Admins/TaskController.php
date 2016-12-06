<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    /**
     * 显示任务列表页
     */
    public function index(Request $request)
    {
        //读取数据 并且分页
        $list = \DB::table('aso_task')
            ->where(function($query) use ($request){
                if($request->input('keyword')){
                    $query->where('appkey','like','%'.$request->input('keyword').'%')
                          ->orWhere('appid', '=', $request->input('keyword'));
                }
            })
            ->orderBy('tid', 'desc')
            ->paginate(10);
        return view('admins.task.index',['list'=>$list, 'request'=>$request->all()]);
    }

    /**
     * 投放任务
     */
    public function doStart(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        $data['state'] = 1;        //已上线

        //执行开始
        $res = \DB::table('aso_task')->where('tid',$data['tid'])->update($data);
        $task_name = \DB::table('aso_task')->where('tid',$data['tid'])->value('appkey');

        if($res){
            //成功
            return response()->json([
                'status' => 1,
                'msg' => '任务：'.$task_name.' 已上线！'
            ]);
        }else{
            return response()->json([
                'status' => 3,
                'msg' => '任务：'.$task_name.' 投放失败！'
            ]);
        }
    }

    /**
     * 下线任务
     */
    public function doPause(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        $data['state'] = 2;        //已下线

        //执行修改
        $res = \DB::table('aso_task')->where('tid',$data['tid'])->update($data);
        $task_name = \DB::table('aso_task')->where('tid',$data['tid'])->value('appkey');

        if($res){
            //成功
            return response()->json([
                'status' => 1,
                'msg' => '任务：'.$task_name.' 已下线！'
            ]);
        }else{
            return response()->json([
                'status' => 3,
                'msg' => '任务：'.$task_name.' 下线失败！'
            ]);
        }
    }

    /**
     * 显示创建任务页
     */
    public function create()
    {
        return view('admins.task.create');
    }

    /**
     * 执行创建任务
     */
    public function store(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证appid 任务名称
        if($data['appid'] == '' || $data['appkey'] == '') {
            return response()->json([
                'status' => 0,
                'msg' => 'AppID和任务名称不能为空！'
            ]);
        }

        //验证执行数量
        if($data['count'] == '') {
            return response()->json([
                'status' => 4,
                'msg' => '请输入正确的数量'
            ]);
        }

        $data['state'] = 0;     //默认 未投放
        $data['success_count'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');

        //执行创建任务
        $res = \DB::table('aso_task')->insert($data);
        if($res){
            //创建成功
            return response()->json([
                'status' => 1,
                'msg' => '创建成功！'
            ]);
        }else{
            return response()->json([
                'status' => 3,
                'msg' => '创建失败！'
            ]);
        }
    }

    /**
     * 显示单条任务
     */
    public function show($id)
    {
        $res = \DB::table('aso_task')->where('tid',$id)->first();
        return view('admins.task.show',['res'=>$res]);
    }

    /**
     * 显示修改任务的页面
     */
    public function edit($id)
    {
        $res = \DB::table('aso_task')->where('tid',$id)->first();
        return view('admins.task.edit',['res'=>$res]);
    }

    /**
     * 执行修改任务
     */
    public function update(Request $request, $id)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证任务状态
        if($data['state'] ==1){
            return response()->json([
                'status' => 5,
                'msg' => '任务进行中，暂时不能修改！'
            ]);
        }

        //验证任务名称
        if($data['appkey'] == '') {
            return response()->json([
                'status' => 0,
                'msg' => 'AppID和任务名称不能为空！'
            ]);
        }

        //验证执行数量
        if($data['count'] == '') {
            return response()->json([
                'status' => 4,
                'msg' => '请输入正确的数量'
            ]);
        }

        $data['state'] = 0;     //默认 未投放
        $data['updated_at'] = date('Y-m-d H:i:s');

        //执行修改
        $res = \DB::table('aso_task')->where('tid',$id)->update($data);
        if($res){
            //修改成功
            return response()->json([
                'status' => 1,
                'msg' => '修改成功！'
            ]);
        }else{
            return response()->json([
                'status' => 3,
                'msg' => '修改失败！'
            ]);
        }
    }

    /**
     * 删除
     */
    public function destroy(Request $request, $id)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证任务状态
        if($data['state'] ==1){
            return response()->json([
                'status' => 5,
                'msg' => '任务进行中，暂时不能删除！'
            ]);
        }

        $res = \DB::table('aso_task')->where('tid',$id)->delete();

        if($res){
            //删除成功
            return response()->json([
                'status' => 1,
                'msg' => '删除成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '删除失败！'
            ]);
        }
    }

    /**
     * 任务记录
     */
    public function record()
    {
        //读取数据 并且分页
        $list = \DB::table('aso_task_record')
            ->join('aso_task', 'aso_task_record.tid', '=', 'aso_task.tid')
            ->join('aso_account', 'aso_task_record.aid', '=', 'aso_account.aid')
            ->join('aso_client', 'aso_task_record.cid', '=', 'aso_client.cid')
            ->orderBy('rid', 'desc')
            ->paginate(10);

        return view('admins.task.record',['list'=>$list]);
    }
}
