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
                                <input name="appid" class="form-control" type="text" placeholder="请输入AppID" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">任务名称：</label>
                            <div class="col-sm-5">
                                <input name="appkey" class="form-control" type="text" placeholder="请输入关键词" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投放总量：</label>
                            <div class="col-sm-5">
                                <input name="count" class="form-control" type="text" placeholder="请输入投放任务总量" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投放单价：</label>
                            <div class="col-sm-5">
                                <input name="price" class="form-control" type="text" placeholder="请输入投放任务单价" style="display: inline-block; width: 90%" required> 元
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-5">
                                <p class="form-control-static">参数设置</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">任务超时时间：</label>
                            <div class="col-sm-5">
                                <input name="timeout" class="form-control" type="text" placeholder="请输入任务超时时间" value="180" style="display: inline-block; width: 90%"> 秒
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否下载App：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="download" value="1"> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="download" value="0" checked> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否激活App：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="active" value="1"> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="active" value="0" checked> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否后登录：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="postLoginStore" value="1" checked> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="postLoginStore" value="0"> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">{{--是否滑动  0 模式一：正常模式 滑动  1 模式二：简约模式  只滑动到60多位--}}
                            <label class="col-sm-3 control-label">是否滑动：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="openDetailDirectly" value="0"> 正常模式
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="openDetailDirectly" value="1" checked> 简约模式
                                </label>
                            </div>
                        </div>
                        {{--<div class="form-group">--}}{{--是否开启VPN  0 ：不开启  1 ：开启--}}{{--
                            <label class="col-sm-3 control-label">是否开启VPN：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="vpnActive" value="0"> 不开启
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="vpnActive" value="1" checked> 开启
                                </label>
                            </div>
                        </div>--}}

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
            var $createForm = $('#ID-create-form'),                         //创建任务表单
                    $tips = $('#ID-tips'),                                   //提示框
                    $createBtn = $createForm.find('button'),                 //创建按钮
                    $appid = $createForm.find('input[name=appid]'),         //appid
                    $appkey = $createForm.find('input[name=appkey]'),       //任务名称
                    $count = $createForm.find('input[name=count]'),         //投放总量
                    $price = $createForm.find('input[name=price]'),         //投放单价

                    $timeout = $createForm.find('input[name=timeout]'),                     //超时时间
                    $download = $createForm.find('input[name=download]'),                   //是否下载app
                    $active = $createForm.find('input[name=active]'),                       //是否激活app
                    $postLoginStore = $createForm.find('input[name=postLoginStore]'),       //是否后登录
                    $openDetailDirectly = $createForm.find('input[name=openDetailDirectly]'),       //是否滑动
                    //$vpnActive = $createForm.find('input[name=vpnActive]'),       //是否开启VPN
                    $msg = {                                                  //提示信息
                        0: 'AppID和任务名称不能为空！',
                        1: 'AppID格式不正确！',
                        2: '请输入正确的投放总量',
                        3: '请输入正确的投放单价'
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
                            $countVal = $.trim($count.val()),
                            $priceVal = $.trim($price.val()),

                            $timeoutVal = $.trim($timeout.val()),
                            $downloadVal = $createForm.find('input[name=download]:checked').val(),
                            $activeVal = $createForm.find('input[name=active]:checked').val(),
                            $postLoginStoreVal = $createForm.find('input[name=postLoginStore]:checked').val(),
                            $openDetailDirectlyVal = $createForm.find('input[name=openDetailDirectly]:checked').val();
                            //$vpnActiveVal = $createForm.find('input[name=vpnActive]:checked').val();

                        //检测AppID   任务名称  数量   单价
                        if(self.checkAppid() && self.checkAppkey() && self.checkCount() && self.checkPrice()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="button" disabled>创建中...</button>');

                            $.ajax({
                                type: 'POST',
                                url: '{{ URL('task') }}',
                                data: {
                                    appid: $appidVal,
                                    appkey: $appkeyVal,
                                    count: $countVal,
                                    price: $priceVal,
                                    timeout: $timeoutVal,
                                    download: $downloadVal,
                                    active: $activeVal,
                                    postLoginStore: $postLoginStoreVal,
                                    openDetailDirectly: $openDetailDirectlyVal,
                                    //vpnActive: $vpnActiveVal,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        setTimeout(function () {
                                            win.location.href='{{ URl("task") }}';
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
                //检测投放总量
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
                //检测投放单价
                checkPrice: function(){
                    var $val = $price.val();

                    if($.trim($val).length!=0){
                        var re = /^[0-9]+([.]{1}[0-9]{1,2})?$/.test($val);
                        if(!re){
                            $tips.html($msg[3]).parent().show();
                            return false;
                        }
                    }else{
                        $tips.html($msg[3]).parent().show();
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
