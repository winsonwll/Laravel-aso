<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    /**
     * 显示设备列表页
     */
    public function index()
    {
        //读取数据 并且分页
        $list = \DB::table('aso_client')
            ->orderBy('cid', 'desc')
            ->paginate(10);
        return view('admins.client.index',['list'=>$list]);
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
                'status' => 0,
                'msg' => 'idfa不能为空！'
            ]);
        }

        $idfa = \DB::table('aso_client')->where('idfa',$data['idfa'])->first();
        if($idfa){
            return response()->json([
                'status' => 2,
                'msg' => 'idfa已经存在！'
            ]);
        }

        //$data['token'] = md5(uniqid(rand()));
        $data['created_at'] = date('Y-m-d H:i:s');

        //执行添加
        $res = \DB::table('aso_client')->insert($data);
        if($res){
            //添加成功
            return response()->json([
                'status' => 1,
                'msg' => '添加成功！'
            ]);
        }else{
            return response()->json([
                'status' => 3,
                'msg' => '添加失败！'
            ]);
        }
    }

    /**
     * 显示单个设备
     */
    public function show($id)
    {
        $res = \DB::table('aso_client')->where('cid',$id)->first();
        //读取数据 并且分页
        $list = \DB::table('aso_task_record')
            ->join('aso_task', 'aso_task_record.tid', '=', 'aso_task.tid')
            ->join('aso_account', 'aso_task_record.aid', '=', 'aso_account.aid')
            ->where('cid',$id)
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
