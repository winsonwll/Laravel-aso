@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">创建任务</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form class="form-horizontal" action="" method="POST" id="ID-create-form">
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
                            <label class="col-sm-3 control-label">AppID：</label>
                            <div class="col-sm-5">
                                <input name="appid" class="form-control" type="text" placeholder="请输入AppID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">任务名称：</label>
                            <div class="col-sm-5">
                                <input name="appkey" class="form-control" type="text" placeholder="请输入关键词">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投放总量：</label>
                            <div class="col-sm-5">
                                <input name="count" class="form-control" type="text" placeholder="请输入投放数量">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 30px">
                            <div class="col-sm-5 col-sm-offset-3">
                                <button class="btn btn-success btn-lg" type="submit">立即创建</button>
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
        var CREATETASK = (function(){
            var win = window;
            var $createForm = $('#ID-create-form'),                     //创建任务表单
                    $tips = $('#ID-tips'),                                  //提示框
                    $createBtn = $createForm.find('button'),                 //创建按钮
                    $appid = $createForm.find('input[name=appid]'),         //appid
                    $appkey = $createForm.find('input[name=appkey]'),       //任务名称
                    $count = $createForm.find('input[name=count]'),         //执行总数
                    $msg = {                                                  //提示信息
                        0: 'AppID和任务名称不能为空！',
                        1: 'AppID格式不正确！',
                        2: '请输入正确的数量'
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
                        var $appidVal = $.trim($appid.val()),
                            $appkeyVal = $.trim($appkey.val()),
                            $countVal = $.trim($count.val());

                        //检测AppID   任务名称  数量
                        if(self.checkAppid() && self.checkAppkey() && self.checkCount()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="submit" disabled>创建中...</button>');

                            $.ajax({
                                type: 'POST',
                                url: '{{ URL('/task') }}',
                                data: {
                                    appid: $appidVal,
                                    appkey: $appkeyVal,
                                    count: $countVal,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        setTimeout(function () {
                                            win.location.href='{{ URl("/task") }}';
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
                checkAppid: function(){
                    var $val = $appid.val();

                    if($.trim($val).length!=0){
                        var re = /^[0-9]{1,15}$/.test($val);
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
                checkAppkey: function(){
                    var $val = $appkey.val();

                    if($.trim($val).length==0){
                        $tips.html($msg[0]).parent().show();
                        return false;
                    }
                    return true;
                },
                //检测数量
                checkCount: function(){
                    var $val = $count.val();

                    if($.trim($val).length!=0){
                        var re = /^[0-9]{1,15}$/.test($val);
                        if(!re){
                            $tips.html($msg[2]).parent().show();
                            return false;
                        }
                    }else{
                        $tips.html($msg[2]).parent().show();
                        return false;
                    }
                    return true;
                }
            }
        })();

        $(function () {
            //初始化
            CREATETASK.init();
        })
    </script>
@endsection
