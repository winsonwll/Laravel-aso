<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * 显示管理员列表页
     */
    public function index()
    {
        //读取数据 并且分页
        $list = Admin::paginate(10);
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
        $data = $request->except(['_token']);
        $pattern = '/^[0-9a-zA-z]{6,12}$/';

        //验证账号
        if(empty($data['name'])) {
            return response()->json([
                'status' => 201,
                'msg' => '账号不能为空！'
            ]);
        }else{
            if(!preg_match($pattern, $data['name'])){
                return response()->json([
                    'status' => 202,
                    'msg' => '账号为6-12位字符！'
                ]);
            }
        }

        //验证密码
        if(empty($data['pwd'])) {
            return response()->json([
                'status' => 203,
                'msg' => '密码不能为空！'
            ]);
        }else{
            if(!preg_match($pattern, $data['pwd'])){
                return response()->json([
                    'status' => 204,
                    'msg' => '密码为6-12位字符！'
                ]);
            }
        }

        $name = Admin::where('name', $data['name'])->first();
        if($name){
            return response()->json([
                'status' => 205,
                'msg' => '用户名已经存在，请重新输入！'
            ]);
        }

        $data['pwd'] = Hash::make($data['pwd']);

        //执行添加
        $res = Admin::create($data);
        if($res){
            //注册成功
            return response()->json([
                'status' => 1,
                'msg' => '注册成功！'
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'msg' => '注册失败，账号或密码错误！'
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
