@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">修改任务</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form class="form-horizontal" action="" method="post" id="ID-update-form">
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
                                <p class="form-control-static">{{ $res->appid }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">任务名称：</label>
                            <div class="col-sm-5">
                                <input name="appkey" class="form-control" type="text" placeholder="请输入关键词" value="{{ $res->appkey }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投放总量：</label>
                            <div class="col-sm-5">
                                <input name="count" class="form-control" type="text" placeholder="请输入投放数量" value="{{ $res->count }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">任务状态：</label>
                            <div class="col-sm-5">
                                <p class="form-control-static">
                                    @if ($res->state == 0)
                                        未投放
                                    @elseif ($res->state == 1)
                                        已上线
                                    @elseif ($res->state == 2)
                                        已下线
                                    @elseif ($res->state == 3)
                                        已结束
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 30px">
                            <div class="col-sm-5 col-sm-offset-3">
                                <button class="btn btn-success btn-lg" type="submit">提交修改</button>
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
        var UPDATETASK = (function(){
            var win = window;
            var $updateForm = $('#ID-update-form'),                     //创建任务表单
                $tips = $('#ID-tips'),                                  //提示框
                $updateBtn = $updateForm.find('button'),                 //创建按钮
                $appkey = $updateForm.find('input[name=appkey]'),       //任务名称
                $count = $updateForm.find('input[name=count]'),         //执行总数
                $msg = {                                                  //提示信息
                    0: '任务名称不能为空！',
                    2: '请输入正确的数量',
                    3: '任务进行中，暂时不能修改'
                };

            return {
                init: function(){
                    this.doUpdate();
                },
                //执行修改
                doUpdate: function(){
                    var self = this;

                    $updateBtn.on('click', function(){
                        var _this = $(this);
                        var $appkeyVal = $.trim($appkey.val()),
                            $countVal = $.trim($count.val());

                        //检测任务状态  任务名称  数量
                        if(self.checkState() && self.checkAppkey() && self.checkCount()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="submit" disabled>修改中...</button>');

                            $.ajax({
                                type: 'PUT',
                                url: '{{ URL('/task/'.$res->tid) }}',
                                data: {
                                    appkey: $appkeyVal,
                                    count: $countVal,
                                    state: {{ $res->state }},
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
                },
                //检测任务状态
                checkState: function () {
                    var $state = {{ $res->state }};
                    if($state == 1){
                        $tips.html($msg[3]).parent().show();
                        return false;
                    }
                    return true;
                }
            }
        })();

        $(function () {
            //初始化
            UPDATETASK.init();
        })
    </script>
@endsection
