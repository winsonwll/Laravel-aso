@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">添加账号</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form class="form-horizontal" action="" method="post" id="ID-create-form">
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
                            <label class="col-sm-3 control-label">苹果账号：</label>
                            <div class="col-sm-5">
                                <input name="apple_id" class="form-control" type="text" placeholder="请输入AppleID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">密 码：</label>
                            <div class="col-sm-5">
                                <input name="apple_pwd" class="form-control" type="text" placeholder="请输入苹果账号密码">
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
        var CREATEACCOUNT = (function(){
            var win = window;
            var $createForm = $('#ID-create-form'),                     //添加账号表单
                $tips = $('#ID-tips'),                                  //提示框
                $createBtn = $createForm.find('button'),                 //添加按钮
                $appleId = $createForm.find('input[name=apple_id]'),         //苹果账号
                $applePwd = $createForm.find('input[name=apple_pwd]'),       //密码
                $msg = {                                                  //提示信息
                    0: '苹果账号和密码不能为空！',
                    1: '苹果账号格式不正确！'
                };

            return {
                init: function(){
                    this.doCreate();
                },
                //执行创建
                doCreate: function(){
                    var self = this;

                    $createBtn.on('click', function(){
                        var _this = $(this);
                        var $appleidVal = $.trim($appleId.val()),
                            $applepwdVal = $.trim($applePwd.val());

                        //检测苹果账号 密码
                        if(self.checkAppleid() && self.checkApplepwd()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="submit" disabled>添加中...</button>');

                            $.ajax({
                                type: 'POST',
                                url: '{{ URL('account') }}',
                                data: {
                                    apple_id: $appleidVal,
                                    apple_pwd: $applepwdVal,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        setTimeout(function () {
                                            win.location.href='{{ URl("account") }}';
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
                //检测AppID
                checkAppleid: function(){
                    var $val = $appleId.val();

                    if($.trim($val).length!=0){
                        var re = /[@]/.test($val);
                        if(!re){
                            $tips.html($msg[1]).parent().show();
                            return false;
                        }
                    }else{
                        $tips.html($msg[0]).parent().show();
                        return false;
                    }
                    return true;
                },
                //检测任务名称
                checkApplepwd: function(){
                    var $val = $applePwd.val();

                    if($.trim($val).length==0){
                        $tips.html($msg[0]).parent().show();
                        return false;
                    }
                    return true;
                }
            }
        })();

        $(function () {
            //初始化
            CREATEACCOUNT.init();
        })
    </script>
@endsection
