<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * 显示设备列表页
     */
    public function index()
    {
        $count = Client::count();
        //读取数据 并且分页
        $list = Client::paginate(10);
        return view('admins.client.index',['list'=>$list, 'count'=>$count]);
    }

    /**
     * 显示添加设备页
     */
    public function create()
    {
        return view('admins.client.create');
    }

    /**
     * 执行添加设备
     */
    public function store(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证idfa
        if($data['idfa'] == '') {
            return response()->json([
                'status' => 201,
                'msg' => 'idfa不能为空！'
            ]);
        }elseif (strlen($data['idfa'])<36){
            return response()->json([
                'status' => 202,
                'msg' => 'idfa格式不正确'
            ]);
        }

        $idfa = Client::where('idfa',$data['idfa'])->first();
        if($idfa){
            return response()->json([
                'status' => 203,
                'msg' => 'idfa已经存在！'
            ]);
        }

        //执行添加
        $res = Client::create($data);
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
     * 显示单个设备
     */
    public function show($id)
    {
        $res = Client::find($id);
        //读取数据 并且分页
        $list = \DB::table('record')
            ->join('task', 'record.tid', '=', 'task.tid')
            ->paginate(10);
        
        return view('admins.client.show',['res'=>$res, 'list'=>$list]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
