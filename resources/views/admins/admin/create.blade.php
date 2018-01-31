@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">添加管理员</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form class="form-horizontal" action="" method="post" id="ID-reg-form">
                        <div class="form-group">
                            <div class="col-sm-5 col-sm-offset-3">
                                <div class="alert alert-warning alert-dismissible fade in" role="alert">
                                    <a class="close" data-dismiss="alert">
                                        <span aria-hidden="true">×</span>
                                        <span class="sr-only">关闭</span>
                                    </a>
                                    <p id="ID-tips"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">用户名：</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" placeholder="请输入用户名" name="name" autofocus required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">密 码：</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control" placeholder="请输入密码" name="pwd" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 30px">
                            <div class="col-sm-5 col-sm-offset-3">
                                <button class="btn btn-success btn-lg" type="submit">立即添加</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var REG = (function(){
            var win = window;
            var $regForm = $('#ID-reg-form'),                  //注册表单
                    $tips = $('#ID-tips'),                         //提示框
                    $regBtn = $regForm.find('button'),              //注册按钮
                    $name = $regForm.find('input[name=name]'),     //用户名
                    $pwd = $regForm.find('input[name=pwd]'),       //密码
                    $msg = {                                         //提示信息
                        0: '用户名或密码不能为空！',
                        1: '注册成功！',
                        2: '注册失败，用户名或密码错误！',
                        3: '用户名为6-12位字符！',
                        4: '密码为6-12位字符！'
                    };

            return {
                init: function(){
                    this.doReg();
                },
                //执行注册
                doReg: function(){
                    var self = this;

                    $regBtn.on('click', function(){
                        var _this = $(this);
                        var $nameVal = $.trim($name.val()),
                            $pwdVal = $.trim($pwd.val());

                        //检测用户名 密码
                        if(self.checkName() && self.checkPwd()){
                            _this.hide();
                            _this.after('<button class="btn btn-lg btn-primary" type="button" disabled>注册中...</button>');

                            $.ajax({
                                type: 'POST',
                                url: '{{ URL("admin") }}',
                                data: {
                                    name: $nameVal,
                                    pwd: $pwdVal,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        setTimeout(function () {
                                            win.location.href='{{ URL('admin') }}';
                                        },300)
                                    }
                                    $tips.html(data.msg);
                                    $tips.parent().show();

                                    _this.show();
                                    _this.next().remove();
                                }
                            });
                        }
                        return false;
                    });
                },
                //检测用户名
                checkName: function(){
                    var $val = $name.val();

                    if($.trim($val).length!=0){
                        var re = /^[0-9a-zA-z]{6,12}$/.test($val);
                        if(!re){
                            $tips.html($msg[3]).parent().show();
                            return false;
                        }
                    }else{
                        $tips.html($msg[0]).parent().show();
                        return false;
                    }
                    return true;
                },
                //检测密码
                checkPwd: function(){
                    var $val = $pwd.val();

                    if($.trim($val).length!=0){
                        var re = /^[0-9a-zA-z]{6,12}$/.test($val);
                        if(!re){
                            $tips.html($msg[4]).parent().show();
                            return false;
                        }
                    }else{
                        $tips.html($msg[0]).parent().show();
                        return false;
                    }
                    return true;
                }
            }
        })();

        $(function () {
            //初始化
            REG.init();
        })
    </script>
@endsection
