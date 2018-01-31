<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Account;
use App\Models\Batch;
use Curl\Curl;
use Illuminate\Support\Facades\Redis;

include_once('simple_html_dom.php');

class TaskController extends Controller
{
    /**
     * 显示任务列表页
     */
    public function index(Request $request)
    {
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

        $vpnActive = Redis::get("vpnActive");

        $batch = Batch::get();

        return view('admins.task.index',['list'=>$list, 'countAccount'=>$count_account, 'vpnActive'=>$vpnActive, 'batch'=>$batch, 'request'=>$request->all()]);
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

        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->get('https://itunes.apple.com/cn/app/id'.$data['appid']);
        if ($curl->error) {
            return response()->json([
                'status' => 201,
                'msg' => $curl->error_code
            ]);
        } else {
            $html = str_get_html($curl->response);
            $data['icon'] = $html->find("meta[itemprop=image]",0)->getAttribute('content');
        }

        $pos = strpos($data['appkey'], '/');
        if($pos === false){
            $res = Task::create($data);
        }else{
            $data['appkey'] = trim($data['appkey'], '/');
            $appkey = explode('/', $data['appkey']);

            foreach ($appkey as $v){
                $data['appkey'] = $v;
                $res = Task::create($data);
            }
        }

        //执行创建任务
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

        //验证执行数量
        if($data['count'] == '') {
            return response()->json([
                'status' => 203,
                'msg' => '请输入正确的数量！'
            ]);
        }

        $data['state'] = 0;     //默认 未投放
        $data['vcnt'] = 0;
        $data['success_count'] = 0;
        $data['first_time'] = null;
        $data['last_time'] = null;

        //执行修改
        $res = Task::where('tid', $id)->update($data);
        if($res){
            Redis::del("task:".$id);
            Redis::del("task:".$id.":vcnt");
            Redis::del("task:".$id.":following");

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
     * 投放任务
     */
    public function doStart(Request $request, $id)
    {
        $batch = explode('/',$request->batch);
        Redis::hmset("tidBatch:".$id, $batch);

        //成功
        $data['state'] = 1;        //已上线

        //执行投放任务
        $res = Task::where('tid', $id)->update($data);
        if($res){
            //成功
            $task = Task::find($id);
            Redis::rpush("tid", $id);
            Redis::hmset("task:".$id, json_decode($task, true));
            //做过的账号 后面不在使用来做此appid的任务
            $arr_apple_id = Redis::smembers("appid:".$task->appid);
            $limit = abs($task->count - $task->success_count);

            if(!$batch[0]){ //随机
                $account = Account::whereIn('state', [0, 1, 2])
                    ->whereNotIn('apple_id', $arr_apple_id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
            }else{
                $account = Account::whereIn('state', [0, 1, 2])
                    ->whereIn('batch', $batch)
                    ->whereNotIn('apple_id', $arr_apple_id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
            }

            foreach ($account as $v){
                Redis::hmset("account:".$v->aid, json_decode($v, true));
                Redis::rpush("task:".$id.":following", $v->aid);
            }

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
     * 一键投放任务
     */
    public function doStartAll(Request $request)
    {
        $arr_tid = explode('/',$request->tid);
        $batch = explode('/',$request->batch);
        //成功
        $data['state'] = 1;        //已上线

        //执行投放任务
        foreach ($arr_tid as $v){
            $res = Task::where('tid', $v)->update($data);

            //成功
            $task = Task::find($v);
            Redis::rpush("tid", $v);
            Redis::hmset("task:".$v, json_decode($task, true));
            //做过的账号 后面不在使用来做此appid的任务
            $arr_apple_id = Redis::smembers("appid:".$task->appid);
            $limit = abs($task->count - $task->success_count);

            if(!$batch[0]){ //随机
                $account = Account::whereIn('state', [0, 1, 2])
                    ->whereNotIn('apple_id', $arr_apple_id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
            }else{
                $account = Account::whereIn('state', [0, 1, 2])
                    ->whereIn('batch', $batch)
                    ->whereNotIn('apple_id', $arr_apple_id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
            }

            foreach ($account as $vv){
                Redis::hmset("account:".$vv->aid, json_decode($vv, true));
                Redis::rpush("task:".$v.":following", $vv->aid);
            }
        }

        if($res){
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
    public function doPause(Request $request, $id)
    {
        $data['state'] = 2;        //已下线
        $data['last_time'] = date('Y-m-d H:i:s');

        //执行下线任务
        $res = Task::where('tid', $id)->update($data);
        if($res){
            //成功
            Redis::lrem("tid", 0, $id);
            //Redis::del("task:".$id);
            Redis::del("task:".$id.":following");
            Redis::del("tidBatch:".$id);

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
        $list = Task::whereNotNull('first_time')->orderBy('last_time', 'desc')->paginate(10);

        return view('admins.task.record',['list'=>$list]);
    }

    /**
     * 任务记录详情
     */
    public function detail($id)
    {
        $res = Task::find($id);

        //读取数据 并且分页
        $list = \DB::table('record')
            ->where('tid', $id)
            ->join('client', 'record.cid', '=', 'client.cid')
            ->orderBy('rid', 'desc')
            ->get();

        $list2 = \DB::table('record')
            ->where('tid', $id)
            ->join('client', 'record.cid', '=', 'client.cid')
            ->orderBy('rid', 'desc')
            ->paginate(20);

        $status1 = 0;       //进行中
        $status204 = 0;     //账号或密码错误
        $status205 = 0;     //账号禁用
        $status207 = 0;     //该关键词没有搜索到指定app
        $status208 = 0;     //此时无法购买
        $status209 = 0;     //超时
        $status211 = 0;     //不能连接ItunesStore
        $status212 = 0;     //登录失败
        $status213 = 0;     //没查到app详情
        $status214 = 0;     //数据格式错
        //$status215 = 0;     //人工干预过
        $status216 = 0;     //服务器验证失败

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
                case 208:
                    $status208++;
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
                case 216:
                    $status216++;
                break;
            }
        }

        $status = [$status204, $status205, $status207, $status208, $status209, $status211, $status212, $status213, $status214, $status216, $status1];
        $count = count($list);

        return view('admins.task.detail',['res'=>$res, 'list'=>$list2, 'status'=>$status, 'count'=>$count]);
    }


    /**
     * 设置vpn
     */
    public function setVpn(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        switch ($data['vpnActive']){
            case 0:     //开启vpn
                $res = Redis::setex("vpnActive", 600, 1);
                break;
            case 1:     //关闭vpn
                $res = Redis::del("vpnActive");
                break;
        }

        if($res){
            //成功
            return response()->json([
                'status' => 1,
                'msg' => '成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '失败！'
            ]);
        }
    }
}
