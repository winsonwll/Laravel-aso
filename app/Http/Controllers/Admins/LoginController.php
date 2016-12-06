<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Session;
use Crypt;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * 显示注册页
     */
    public function reg()
    {
        return view('admins.reg');
    }

    /**
     * 执行注册
     */
    public function doReg(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证用户名和密码
        if($data['name'] == '' || $data['pwd'] == '') {
            return response()->json([
                'status' => 0,
                'msg' => '账号或密码不能为空！'
            ]);
        }

        $name = \DB::table('aso_admin')->where('name',$data['name'])->first();
        if($name){
            return response()->json([
                'status' => 3,
                'msg' => '用户名已存在！'
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
                'msg' => '账号或密码错误！'
            ]);
        }
    }
    
    /**
     * 显示登录页
     */
    public function login()
    {
        return view('admins.login');
    }

    /**
     * 执行登录
     */
    public function doLogin(Request $request)
    {
        //提取部分参数
        $data = $request->except('_token');

        //验证验证码
        $sessionVcode = Session::get('vcode');
        if($data['vcode'] != $sessionVcode) {
            return response()->json([
                'status' => 6,
                'msg' => '验证码错误！'
            ]);
        }

        //验证用户名和密码
        if($data['name'] == '' || $data['pwd'] == '') {
            return response()->json([
                'status' => 0,
                'msg' => '账号或密码不能为空！'
            ]);
        }

        //根据用户名获取用户信息
        $admin = \DB::table('aso_admin')->where('name', $data['name'])->first();

        if(!empty($admin) && Hash::check($data['pwd'], $admin->pwd)){
            $request->session()->put('admin', $admin);      //登录成功 则记录登录信息

            //自动登录的操作
            if($data['remember'] == 1){
                $str = $data['name'].'|'.$data['pwd'];
                //加密
                $auth_admin = Crypt::encrypt($str);
                //写入cookie
                \Cookie::queue('auth_admin', $auth_admin, 60*24*30);
            }

            return response()->json([
                'status' => 1,
                'msg' => '登录成功！'
            ]);
        }else{
            return response()->json([
                'status' => 2,
                'msg' => '账号或密码错误！'
            ]);
        }
    }

    /**
     * 验证码
     */
    public function captcha($tmp)
    {
        ob_clean();     //清除
        //生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder;
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 34, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();

        //把内容存入session
        Session::flash('vcode', $phrase);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }

    /**
     * 退出
     */
    public function logout()
    {
        session()->forget('admin'); //删除session对应的值
        $cookie = \Cookie::forget('auth_admin');    //删除cookie对应的值
        return redirect('/login')->withCookie($cookie);
    }
}
