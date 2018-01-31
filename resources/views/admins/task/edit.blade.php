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
                                <p class="form-control-static">{{ $res->appkey }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投放总量：</label>
                            <div class="col-sm-5">
                                <input name="count" class="form-control" type="text" placeholder="请输入投放总量" value="{{ $res->count }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投放单价：</label>
                            <div class="col-sm-5">
                                <input name="price" class="form-control" type="text" placeholder="请输入投放单价" value="{{ $res->price }}">
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
                                <input name="timeout" class="form-control" type="text" placeholder="请输入任务超时时间" value="{{ $res->timeout }}" style="display: inline-block; width: 90%"> 秒
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否下载App：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="download" value="1" @if ($res->download == 1) checked @endif> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="download" value="0" @if ($res->download == 0) checked @endif> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否激活App：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="active" value="1" @if ($res->active == 1) checked @endif> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="active" value="0" @if ($res->active == 0) checked @endif> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否后登录：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="postLoginStore" value="1" @if ($res->postLoginStore == 1) checked @endif> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="postLoginStore" value="0" @if ($res->postLoginStore == 0) checked @endif> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否滑动：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="openDetailDirectly" value="0" @if ($res->openDetailDirectly == 1) checked @endif> 正常模式
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="openDetailDirectly" value="1" @if ($res->openDetailDirectly == 0) checked @endif> 简约模式
                                </label>
                            </div>
                        </div>

                        {{--<div class="form-group">
                            <label class="col-sm-3 control-label">是否开启VPN：</label>
                            <div class="col-sm-5">
                                <label class="radio-inline">
                                    <input type="radio" name="vpnActive" value="0" @if ($res->vpnActive == 1) checked @endif> 开启
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="vpnActive" value="1" @if ($res->vpnActive == 0) checked @endif> 不开启
                                </label>
                            </div>
                        </div>--}}

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
                $tips = $('#ID-tips'),                                   //提示框
                $updateBtn = $updateForm.find('button'),                 //创建按钮
                $count = $updateForm.find('input[name=count]'),         //投放总量
                $price = $updateForm.find('input[name=price]'),         //投放单价

                $timeout = $updateForm.find('input[name=timeout]'),             //超时时间
                $download = $updateForm.find('input[name=download]'),          //是否下载app
                $active = $updateForm.find('input[name=active]'),               //是否激活app
                $postLoginStore = $updateForm.find('input[name=postLoginStore]'),       //是否后登录
                $openDetailDirectly = $updateForm.find('input[name=openDetailDirectly]'),       //是否滑动
                //$vpnActive = $updateForm.find('input[name=vpnActive]'),       //是否开启VPN
                $msg = {                                                  //提示信息
                    0: '任务名称不能为空！',
                    2: '请输入正确的投放总量',
                    3: '任务进行中，暂时不能修改',
                    4: '请输入正确的投放单价'
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
                        var $countVal = $.trim($count.val()),
                            $priceVal = $.trim($price.val()),

                            $timeoutVal = $.trim($timeout.val()),
                            $downloadVal = $updateForm.find('input[name=download]:checked').val(),
                            $activeVal = $updateForm.find('input[name=active]:checked').val(),
                            $postLoginStoreVal = $updateForm.find('input[name=postLoginStore]:checked').val(),
                            $openDetailDirectlyVal = $updateForm.find('input[name=openDetailDirectly]:checked').val();
                            //$vpnActiveVal = $updateForm.find('input[name=vpnActive]:checked').val();

                        var return_url = self.getQueryString('return_url');

                        //检测任务状态  任务名称  数量
                        if(self.checkState() && self.checkCount() && self.checkPrice()){
                            _this.hide();
                            _this.after('<button class="btn btn-success btn-lg" type="button" disabled>修改中...</button>');

                            $.ajax({
                                type: 'PUT',
                                url: '{{ URL('task/'.$res->tid) }}',
                                data: {
                                    count: $countVal,
                                    price: $priceVal,
                                    state: {{ $res->state }},
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
                                            win.location.href='http://118.178.94.0'+return_url;
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
                            $tips.html($msg[4]).parent().show();
                            return false;
                        }
                    }else{
                        $tips.html($msg[4]).parent().show();
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
                },
                //获取地址栏url中的指定参数
                getQueryString: function(key){
                    var reg = new RegExp("(^|&)"+key+"=([^&]*)(&|$)");  //构造一个含有目标参数的正则表达式对象
                    var result = window.location.search.substr(1).match(reg);   //匹配目标参数
                    return result?decodeURIComponent(result[2]):null;   //返回参数值
                }
            }
        })();

        $(function () {
            //初始化
            UPDATETASK.init();
        })
    </script>
@endsection
