<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Batch;
use App\Models\Record;
use App\Models\Task;
use App\Models\Client;
use Config;
use Excel;

class AccountController extends Controller
{
    /**
     * 显示账号列表页
     */
    public function index(Request $request)
    {
        $count = Account::count();
        //读取数据 并且分页
        $list = Account::where(function($query) use ($request){
            if($request->input('apple_id')){
                $query->where('apple_id','like','%'.trim($request->input('apple_id')).'%');
            }
        })->paginate(10);

        $batch = Batch::get();

        return view('admins.account.index',['list'=>$list, 'count'=>$count, 'batch'=>$batch, 'request'=>$request->all()]);
    }

    /**
     * 导入账号
     */
    public function import(Request $request)
    {
        if($request->hasFile('accounts')){              //判断是否有上传
            $file=$request->file('accounts');           //获取上传信息

            if($file->isValid()){                       //确认上传的文件是否成功
                $ext=$file->getClientOriginalExtension();   //获取上传文件名的后缀名

                if($ext !== 'txt'){
                    return response()->json([
                        'status' => 0,
                        'msg' => '请上传后缀名为txt的文件！'
                    ]);
                }

                //提取账号
                $str = file_get_contents($file);
                $str = trim($str);
                if(!strlen($str)){
                    return response()->json([
                        'status' => 1,
                        'msg' => '上传文件不能为空！'
                    ]);
                }

                //执行移动上传文件
                $filename=time().rand(1000,9999).'.'.$ext;
                $file->move(Config::get('app.upload_dir'),$filename);

                $arr = explode("\n",$str);     //必须为双引号

                if(strpos($str,',')){                   //逗号分隔形式的账号
                    foreach($arr as $k=>$v){
                        if(!empty(strpos($v,','))){
                            $arr[$k]=explode(',',$v);
                        }else{
                            $listError[] = [array_splice($arr, $k, 1, '')];
                            continue;
                        }
                    }
                }elseif (strpos($str,' ')){         //空格分隔形式的账号
                    foreach($arr as $k=>$v){
                        if(!empty(strpos($v,' '))){
                            $arr[$k]=explode(' ',$v);
                        }else{
                            $listError[] = [array_splice($arr, $k, 1, '')];
                            continue;
                        }
                    }
                }

                //二维数组去重
                $arr = $this->assoc_unique($arr, '0');

                //批次
                if($request->action != 'issue'){
                    $data_batch['note'] = '';
                    $batch = \DB::table('batch')->insertGetId($data_batch);
                }

                foreach($arr as $k=>$v){
                    if(empty($v)){
                        array_splice($arr, $k, 1);
                        continue;
                    }
                    //验证苹果账号 密码
                    if(trim($v[0]) == '' || trim($v[1]) == '') {
                        $listError[] = $v;
                        continue;
                    }

                    $data['apple_id']=trim($v[0]);
                    $data['apple_pwd']=trim($v[1]);

                    //执行添加
                    $hasAppleid = Account::where('apple_id',$data['apple_id'])->first();
                    if(empty($hasAppleid)){
                        $data['batch'] = $batch;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $res = Account::create($data);
                    }else{
                        $data['state']=0;
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $res = Account::where('apple_id',$data['apple_id'])->update($data);
                    }

                    if($res){
                        $list[] = $v;
                    }
                }

                if(!empty($list)){
                    if(empty($listError)){
                        return response()->json([
                            'status' => 2,
                            'msg' => '添加成功！',
                            'data' => [
                                'len' => count($list),
                                'listSuccess' => $list
                            ]
                        ]);
                    }else{
                        return response()->json([
                            'status' => 3,
                            'msg' => '添加成功！',
                            'data' => [
                                'len' => count($list)+count($listError),
                                'listSuccess' => $list,
                                'listError' => $listError
                            ]
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 清除该批次
     */
    public function clear(Request $request)
    {
        //提取部分参数
        $batch = $request->input('batch');
        Batch::where('batch', '=', $batch)->delete();
        $res = Account::where('batch', '=', $batch)->delete();

        if($res){
            //清空成功
            return response()->json([
                'status' => 1,
                'msg' => '清空成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '清空失败！'
            ]);
        }
    }

    /**
     * 清空全部账号
     */
    public function clearall(Request $request)
    {
        /*Record::truncate();
        Task::truncate();
        Client::truncate();*/
        Account::truncate();
        Batch::truncate();

        $res = Account::count();

        if($res<2){
            //清空成功
            return response()->json([
                'status' => 1,
                'msg' => '清空成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '清空失败！'
            ]);
        }
    }

    /**
     * 二维数组去重
     */
    private function assoc_unique($arr, $key)
    {
        $tmp_arr = [];
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }

    /**
     * 显示新建账号页
     */
    public function create()
    {
        return view('admins.account.create');
    }

    /**
     * 执行添加账号
     */
    public function store(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        $data['apple_id'] = trim($data['apple_id']);
        $data['apple_pwd'] = trim($data['apple_pwd']);

        //验证苹果账号 密码
        if($data['apple_id'] == '' || $data['apple_pwd'] == '') {
            return response()->json([
                'status' => 201,
                'msg' => '苹果账号和密码不能为空！'
            ]);
        }

        $apple_id = Account::where('apple_id',$data['apple_id'])->first();
        if($apple_id){
            return response()->json([
                'status' => 202,
                'msg' => '该苹果账号已经存在！'
            ]);
        }

        //执行添加
        $res = Account::create($data);
        if($res){
            //添加成功
            return response()->json([
                'status' => 1,
                'msg' => '添加成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '添加失败！'
            ]);
        }
    }

    /**
     * 显示单条
     */
    public function show($id)
    {
        $res = Account::find($id);
        //读取数据 并且分页
        $list = \DB::table('record')
            ->join('task', 'record.tid', '=', 'task.tid')
            ->join('client', 'record.cid', '=', 'client.cid')
            ->where('aid',$id)
            ->paginate(10);

        return view('admins.account.show',['res'=>$res, 'list'=>$list]);
    }

    /**
     * 显示修改账号的页面
     */
    public function edit($id)
    {
        //
    }

    /**
     * 执行修改账号
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 问题账号修改为正常
     */
    public function change($id)
    {
        $res = Account::where('aid', $id)->update(['state' => 0]);
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
     * 问题账号全部修改为正常
     */
    public function changeall()
    {
        $res = Account::whereIn('state', [3,4])->update(['state' => 0]);
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
     * 问题账号全部删除
     */
    public function removeall()
    {

        $res = Account::whereIn('state', [3, 4])->delete();;
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
     * 删除
     */
    public function destroy($id)
    {
        $res = Account::destroy($id);
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
     * 显示问题账号列表页
     */
    public function issue()
    {
        $batch = Account::whereIn('state', [3,4])->pluck('batch');
        $batch = json_decode($batch, true);
        $batch = array_flip($batch);
        $batch = array_flip($batch);

        //读取数据 并且分页
        $list = Account::whereIn('state', [3,4])->paginate(10);
        return view('admins.account.issue',['list'=>$list, 'batch'=>$batch]);
    }

    /**
     * 显示重复账号列表页
     */
    public function repeat()
    {
        $batch = Account::pluck('batch');
        $batch = json_decode($batch, true);
        $batch = array_flip($batch);
        $batch = array_flip($batch);

        //读取数据 并且分页
        $list = Account::whereColumn('updated_at', '>', 'created_at')->paginate(10);
        return view('admins.account.repeat',['list'=>$list, 'batch'=>$batch]);
    }

    /**
     * 导出问题账号
     */
    public function issueExport(Request $request)
    {
        $cellData = [
            ['苹果账号','密码','批次','状态']
        ];

        $batch = $request->batch;
        $state = $request->state;

        //读取数据
        $list = Account::where('state', $state)->where('batch', $batch)->get();
        $stateStatus = null;

        foreach($list as $k => $v) {
            if($v->state == 3){
                $stateStatus = '账号禁用';
            }elseif ($v->state == 4){
                $stateStatus = '登录失败';
            }
            $cellData[] = [
                $v->apple_id,
                $v->apple_pwd,
                $v->batch,
                $stateStatus
            ];
        }
        
        Excel::create('问题账号报表_批次'.$batch,function($excel) use ($cellData){
            $excel->sheet('问题账号', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    /**
     * 导出重复账号
     */
    public function repeatExport($id)
    {
        $cellData = [
            ['苹果账号','密码','批次','状态']
        ];

        //读取数据
        $list = Account::whereColumn('updated_at', '>', 'created_at')->get();
        $state = null;

        foreach($list as $k => $v) {
            if($v->state == 1){
                $state = '使用中';
            }elseif ($v->state == 2){
                $state = '已使用';
            }elseif($v->state == 3){
                $state = '账号禁用';
            }elseif ($v->state == 4){
                $state = '登录失败';
            }else {
                $state = '未使用';
            }
            $cellData[] = [
                $v->apple_id,
                $v->apple_pwd,
                $v->batch,
                $state
            ];
        }

        Excel::create('重复账号报表_批次'.$id,function($excel) use ($cellData){
            $excel->sheet('重复账号', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    /**
     * 批次情况
     */
    public function batch()
    {
        //读取数据 并且分页
        $list = Batch::paginate(10);
        return view('admins.account.batch',['list'=>$list]);
    }

    /**
     * 显示编辑批次
     */
    public function editBatch($id)
    {
        $res = Batch::find($id);
        return view('admins.account.editBatch',['res'=>$res]);
    }

    /**
     * 编辑批次
     */
    public function doEditBatch(Request $request, $id)
    {
        $res = Batch::where('batch', $id)->update(['note' => $request->note]);
        if($res){
            //编辑成功
            return response()->json([
                'status' => 1,
                'msg' => '编辑成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '编辑失败！'
            ]);
        }
    }
}