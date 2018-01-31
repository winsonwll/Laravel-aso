<?php

namespace App\Http\Controllers\Admins;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Baseconfig;
use Cache;

class TaskController extends Controller
{
    /**
     * 设置分发方式
     */
    public function setDistribute(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        $res = Baseconfig::where('bid',1)->update($data);
        if($res){
            if($data['bindState'] == 1){        //绑定手机
                Cache::put('bindState', $data['bindState'], 10000000);
                Cache::put('bindTimes', $data['bindTimes'], 10000000);
            }else{
                Cache::put('bindState', $data['bindState'], 10000000);
            }

            //成功
            return response()->json([
                'status' => 1,
                'msg' => '分发方式设置成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '分发方式设置失败！'
            ]);
        }
    }

    /**
     * 显示任务列表页
     */
    public function index(Request $request)
    {
        $bindState = Cache::get('bindState', '1');
        $count_account = Account::whereIn('state', [0,1])->count();
        //读取数据 并且分页
        $list = Task::where(function($query) use ($request){
                if($request->input('keyword')){
                    $query->where('appkey','like','%'.$request->input('keyword').'%')
                          ->orWhere('appid', '=', $request->input('keyword'));
                }
            })
            ->orderBy('tid', 'desc')
            ->paginate(10);

        return view('admins.task.index',['list'=>$list, 'countAccount'=>$count_account, 'bindState'=>$bindState, 'request'=>$request->all()]);
    }

    /**
     * 投放任务
     */
    public function doStart(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        $data['state'] = 1;        //已上线

        //执行投放任务
        $res = Task::where('tid',$data['tid'])->update($data);
        if($res){
            //成功
            return response()->json([
                'status' => 1,
                'msg' => '任务已上线！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '任务投放失败！'
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
        $data['last_time'] = date('Y-m-d H:i:s');

        //执行下线任务
        $res = Task::where('tid',$data['tid'])->update($data);
        if($res){
            //成功
            return response()->json([
                'status' => 1,
                'msg' => '任务已下线！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '任务下线失败！'
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
                'status' => 201,
                'msg' => 'AppID和任务名称不能为空！'
            ]);
        }

        //验证投放总量
        if($data['count'] == '') {
            return response()->json([
                'status' => 202,
                'msg' => '请输入正确的投放总量！'
            ]);
        }

        //验证投放单价
        if($data['price'] == '') {
            return response()->json([
                'status' => 203,
                'msg' => '请输入正确的投放单价！'
            ]);
        }

        //验证appid 任务名称 是否已存在
        $tid = Task::where([
            ['appid', '=', $data['appid']],
            ['appkey', '=', $data['appkey']],
        ])->first();

        if($tid) {
            if($tid->state == 0){
                return response()->json([
                    'status' => 204,
                    'msg' => '该任务已存在，且尚未投放，您可以直接投放！'
                ]);
            }
        }

        //执行创建任务
        $res = Task::create($data);
        if($res){
            //创建成功
            return response()->json([
                'status' => 1,
                'msg' => '任务创建成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '任务创建失败！'
            ]);
        }
    }

    /**
     * 查看单条任务
     */
    public function show($id)
    {
        $res = Task::find($id);
        return view('admins.task.show',['res'=>$res]);
    }

    /**
     * 显示修改任务的页面
     */
    public function edit($id)
    {
        $res = Task::find($id);
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
                'status' => 201,
                'msg' => '任务进行中，暂时不能修改！'
            ]);
        }

        //验证任务名称
        if($data['appkey'] == '') {
            return response()->json([
                'status' => 202,
                'msg' => 'AppID和任务名称不能为空！'
            ]);
        }

        //验证执行数量
        if($data['count'] == '') {
            return response()->json([
                'status' => 203,
                'msg' => '请输入正确的数量！'
            ]);
        }

        $data['state'] = 0;     //默认 未投放
        $data['first_time'] = null;
        $data['last_time'] = null;

        //执行修改
        $res = Task::where('tid',$id)->update($data);
        if($res){
            //修改成功
            return response()->json([
                'status' => 1,
                'msg' => '修改成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
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
                'status' => 201,
                'msg' => '任务进行中，暂时不能删除！'
            ]);
        }

        $res = Task::destroy($id);
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
     * 完成情况
     */
    public function record()
    {
        //读取数据 并且分页
        $list = Task::whereNotNull('first_time')->orderBy('tid', 'desc')->paginate(10);

        return view('admins.task.record',['list'=>$list]);
    }

    /**
     * 任务记录详情
     */
    public function detail($id)
    {
        $res = Task::find($id);

        //读取数据 并且分页
        /*$list = \DB::table('aso_task_record')
            ->where('tid', $id)
            ->join('aso_account', 'aso_task_record.aid', '=', 'aso_account.aid')
            ->join('aso_client', 'aso_task_record.cid', '=', 'aso_client.cid')
            ->orderBy('rid', 'desc')
            ->paginate(10);*/

        $list = \DB::table('record')
            ->where('tid', $id)
            ->join('account', 'record.aid', '=', 'account.aid')
            ->join('client', 'record.cid', '=', 'client.cid')
            ->orderBy('rid', 'desc')
            ->get();

        $list2 = \DB::table('record')
            ->where('tid', $id)
            ->join('account', 'record.aid', '=', 'account.aid')
            ->join('client', 'record.cid', '=', 'client.cid')
            ->orderBy('rid', 'desc')
            ->paginate(20);

        $status204 = 0;     //账号或密码错误
        $status205 = 0;     //账号禁用
        $status207 = 0;     //该关键词没有搜索到指定app
        $status209 = 0;     //超时
        $status211 = 0;     //不能连接ItunesStore
        $status212 = 0;     //登录失败
        $status213 = 0;     //没查到app详情
        $status214 = 0;     //数据格式错
        $status1 = 0;       //进行中

        foreach ($list as $v){
            switch ($v->status)
            {
                case 1:
                    $status1++;
                break;
                case 204:
                    $status204++;
                    break;
                case 205:
                    $status205++;
                break;
                case 207:
                    $status207++;
                break;
                case 209:
                    $status209++;
                break;
                case 211:
                    $status211++;
                break;
                case 212:
                    $status212++;
                break;
                case 213:
                    $status213++;
                break;
                case 214:
                    $status214++;
                break;
            }
        }

        $status = [$status204, $status205, $status207, $status209, $status211, $status212, $status213, $status214, $status1];
        $count = count($list);

        return view('admins.task.detail',['res'=>$res, 'list'=>$list2, 'status'=>$status, 'count'=>$count]);
    }
}
