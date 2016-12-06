<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Config;

class AccountController extends Controller
{
    /**
     * 显示账号列表页
     */
    public function index()
    {
        //读取数据 并且分页
        $list = \DB::table('aso_account')
            ->orderBy('aid', 'desc')
            ->paginate(10);
        return view('admins.account.index',['list'=>$list]);
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
                foreach($arr as $k=>$v){
                    if(!empty(strpos($v,','))){
                        $arr[$k]=explode(',',$v);
                    }else{
                        $listError[] = [array_splice($arr, $k, 1, '')];
                        continue;
                    }
                }

                //二维数组去重
                $arr = $this->assoc_unique($arr, '0');

                foreach($arr as $k=>$v){
                    if(empty($v)){
                        array_splice($arr, $k, 1);
                        continue;
                    }
                    //验证苹果账号 密码
                    if($v[0] == '' || $v[1] == '') {
                        $listError[] = $v;
                        continue;
                    }

                    $data['apple_id']=$v[0];
                    $data['apple_pwd']=$v[1];

                    //执行添加
                    $hasAppleid = \DB::table('aso_account')->where('apple_id',$v[0])->first();
                    if(empty($hasAppleid)){
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $res = \DB::table('aso_account')->insert($data);
                    }else{
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $res = \DB::table('aso_account')->where('apple_id',$v[0])->update($data);
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

        //验证苹果账号 密码
        if($data['apple_id'] == '' || $data['apple_pwd'] == '') {
            return response()->json([
                'status' => 0,
                'msg' => '苹果账号和密码不能为空！'
            ]);
        }

        $apple_id = \DB::table('aso_account')->where('apple_id',$data['apple_id'])->first();
        if($apple_id){
            return response()->json([
                'status' => 2,
                'msg' => '苹果账号已经存在！'
            ]);
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        //执行添加
        $res = \DB::table('aso_account')->insert($data);
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
     * 显示单条
     */
    public function show($id)
    {
        $res = \DB::table('aso_account')->where('aid',$id)->first();
        //读取数据 并且分页
        $list = \DB::table('aso_task_record')
            ->join('aso_task', 'aso_task_record.tid', '=', 'aso_task.tid')
            ->join('aso_client', 'aso_task_record.cid', '=', 'aso_client.cid')
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
     * 删除
     */
    public function destroy($id)
    {
        $res = \DB::table('aso_account')->where('aid',$id)->delete();

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
}
