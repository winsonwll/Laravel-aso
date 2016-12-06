<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * 显示管理员列表页
     */
    public function index()
    {
        //读取数据 并且分页
        $list = \DB::table('aso_admin')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('admins.admin.index',['list'=>$list]);
    }

    /**
     * 显示添加管理员页
     */
    public function create()
    {
        return view('admins.admin.create');
    }

    /**
     * 执行添加账号
     */
    public function store(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证用户名和密码
        if($data['name'] == '' || $data['pwd'] == '') {
            return response()->json([
                'status' => 0,
                'msg' => '用户名或密码不能为空！'
            ]);
        }

        $name = \DB::table('aso_admin')->where('name', $data['name'])->first();
        if($name){
            return response()->json([
                'status' => 3,
                'msg' => '用户名已经存在！'
            ]);
        }

        $data['pwd'] = Hash::make($data['pwd']);
        $data['auth'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');

        //执行添加
        $id = \DB::table('aso_admin')->insertGetId($data);
        if($id){
            //注册成功
            return response()->json([
                'status' => 1,
                'msg' => '注册成功！'
            ]);
        }else{
            return response()->json([
                'status' => 2,
                'msg' => '用户名或密码错误！'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
