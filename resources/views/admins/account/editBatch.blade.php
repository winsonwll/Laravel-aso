@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1>修改批次</h1>
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
                            <label class="col-sm-3 control-label">批次：</label>
                            <div class="col-sm-5">
                                <p class="form-control-static">{{ $res->batch }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">备注：</label>
                            <div class="col-sm-5">
                                <input name="note" class="form-control" type="text" placeholder="请输入备注" value="{{ $res->note }}">
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
        var UPDATEBATCH = (function(){
            var win = window;
            var $updateForm = $('#ID-update-form'),                     //修改批次表单
                $tips = $('#ID-tips'),                                   //提示框
                $updateBtn = $updateForm.find('button'),                 //修改按钮
                $note = $updateForm.find('input[name=note]'),           //备注

                $msg = {                                                  //提示信息
                    0: '备注信息不能为空！'
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
                        var $noteVal = $.trim($note.val());

                        //检测备注
                        if(self.checkNote()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="button" disabled>修改中...</button>');

                            $.ajax({
                                type: 'post',
                                url: '{{ URL('account/batch/'.$res->batch) }}',
                                data: {
                                    note: $noteVal,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        setTimeout(function () {
                                            win.location.href='{{ URl("account/batch") }}';
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
                //检测备注
                checkNote: function(){
                    var $val = $note.val();

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
            UPDATEBATCH.init();
        })
    </script>
@endsection
