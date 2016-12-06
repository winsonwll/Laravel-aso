<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('admins/images/favicon.ico') }}">
    <title>登录</title>

    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
    <style type="text/css">
        body { background-color: #eee; font-family: "Microsoft YaHei";}
        .container { padding-top: 100px;}
        form { max-width: 330px; padding: 15px; margin: 0 auto; background-color: #fff; border-radius: 10px;}
        dl { padding-top: 0; margin: 0;}
        form dt { font-size: 30px;}
        form dd { margin-top: 15px;}
        form dd .form-control { height: auto; -webkit-box-sizing: border-box; -moz-box-sizing: border-box;  box-sizing: border-box; padding: 10px; font-size: 16px;}
        dd.alert { display: none;}
    </style>
</head>

<body>
<div class="container">
    <form role="form" method="post" action="" id="ID-login-form">
        <dl>
            <dt>登 录</dt>
            <dd class="alert alert-warning alert-dismissible fade in" role="alert">
                <a type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </a>
                <p id="ID-tips"></p>
            </dd>
            <dd>
                <input type="text" class="form-control" placeholder="请输入账号" name="name" autofocus required>
            </dd>
            <dd>
                <input type="password" class="form-control" placeholder="请输入密码" name="pwd" required>
            </dd>
            <dd class="row">
                <div class="col-xs-7">
                    <input type="text" class="form-control" placeholder="验证码" name="vcode" required>
                </div>
                <div class="col-xs-5">
                    <img src="{{ URL('captcha/'.time()) }}" onclick="this.src='{{ URL('captcha') }}/'+Math.random();" width="100" height="44" style="cursor: pointer">
                </div>
            </dd>
            <dd>
                <label>
                    <input type="checkbox" value="1" name="remember" checked> 自动登录
                </label>
            </dd>
            <dd>
                <button class="btn btn-lg btn-primary btn-block" type="submit">登 录</button>
            </dd>
        </dl>
    </form>
</div>

<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
    var LOGIN = (function(){
        var win = window;
        var $loginForm = $('#ID-login-form'),                //登录表单
            $tips = $('#ID-tips'),                           //提示框
            $loginBtn = $loginForm.find('button'),            //登录按钮
            $name = $loginForm.find('input[name=name]'),     //账号
            $pwd = $loginForm.find('input[name=pwd]'),       //密码
            $vcode = $loginForm.find('input[name=vcode]'),   //验证码
            $remember = $loginForm.find('input[name=remember]'),   //自动登录
            $msg = {                                         //提示信息
                0: '账号或密码不能为空！',
                1: '登录成功！',
                2: '账号或密码错误！',
                3: '账号为6-12位字符！',
                4: '密码为6-12位字符！',
                5: '验证码不能为空！',
                6: '验证码错误！'
            };

        return {
            init: function(){
                this.doLogin();
            },
            //执行登录
            doLogin: function(){
                var self = this;

                $loginBtn.off().on('click', function(){
                    var _this = $(this);
                    var $nameVal = $.trim($name.val()),
                        $pwdVal = $.trim($pwd.val()),
                        $vcodeVal = $.trim($vcode.val()),
                        $rememberVal = $remember.val();

                    //检测账号 密码 验证码
                    if(self.checkName() && self.checkPwd() && self.checkVcode()){
                        _this.hide();
                        _this.after('<button class="btn btn-lg btn-primary btn-block" type="button" disabled>登录中...</button>');

                        $.ajax({
                            type: 'POST',
                            url: '{{ URL('/login') }}',
                            data: {
                                name: $nameVal,
                                pwd: $pwdVal,
                                vcode: $vcodeVal,
                                remember: $rememberVal,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    var $url = decodeURI(self.getUrlParam('return_url'));
                                    if($url!='null'){
                                        win.location.href=$url;
                                    }else{
                                        win.location.href='{{ URl("/task") }}';
                                    }
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
            //检测账号
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
            },
            //检测验证码
            checkVcode: function(){
                var $val = $vcode.val();
                if($.trim($val).length==0){
                    $tips.html($msg[5]).parent().show();
                    return false;
                }
                return true;
            },
            //获取地址栏url中的指定参数
            getUrlParam: function(key){
                var reg = new RegExp("(^|&)"+key+"=([^&]*)(&|$)");  //构造一个含有目标参数的正则表达式对象
                var result = window.location.search.substr(1).match(reg);   //匹配目标参数
                return result?decodeURIComponent(result[2]):null;   //返回参数值
            }
        }
    })();

    $(function () {
        //初始化
        LOGIN.init();
    })
</script>
</body>
</html>
