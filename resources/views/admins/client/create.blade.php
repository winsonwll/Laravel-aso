@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">添加设备</h1>
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
                            <label class="col-sm-3 control-label">idfa：</label>
                            <div class="col-sm-5">
                                <input name="idfa" class="form-control" type="text" placeholder="请输入idfa">
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
        var CREATEDEVICE = (function(){
            var win = window;
            var $createForm = $('#ID-create-form'),                     //添加设备表单
                $tips = $('#ID-tips'),                                  //提示框
                $createBtn = $createForm.find('button'),                 //添加按钮
                $idfa = $createForm.find('input[name=idfa]'),         //idfa
                $msg = {                                                  //提示信息
                    0: 'idfa不能为空！',
                    1: 'idfa格式不正确'
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
                        var $idfaVal = $.trim($idfa.val());

                        //检测设备名称
                        if(self.checkIdfa()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="submit" disabled>添加中...</button>');

                            $.ajax({
                                type: 'POST',
                                url: '{{ URL('client') }}',
                                data: {
                                    idfa: $idfaVal,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        setTimeout(function () {
                                            win.location.href='{{ URl("client") }}';
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
                //检测idfa
                checkIdfa: function(){
                    var $val = $idfa.val();

                    if($.trim($val).length==0){
                        $tips.html($msg[0]).parent().show();
                        return false;
                    }else if($.trim($val).length<36){
                        $tips.html($msg[1]).parent().show();
                        return false;
                    }
                    return true;
                }
            }
        })();

        $(function () {
            //初始化
            CREATEDEVICE.init();
        })
    </script>
@endsection
