@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">任务列表</h1>
        <form action="{{ URL('task') }}" method="get">
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" placeholder="请输入Appid或关键词" class="input-sm form-control" name="keyword" value="{{ $request['keyword'] or '' }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary">搜索</button>
                    </span>
                </div>
            </div>
        </form>
    </div>

    <div class="wrap">
        @if (!$countAccount)
            <div class="alert alert-danger" role="alert">
                没有可使用的账号了 <a href="{{ URL('account') }}" class="alert-link">导入账号</a>
            </div>
        @elseif ($countAccount < 100)
            <div class="alert alert-danger" role="alert">
                可使用的账号数少于100个 <a href="{{ URL('account') }}" class="alert-link">导入账号</a>
            </div>
        @endif

        <div class="form-group">
            <div class="pull-right">
                @if ($vpnActive)
                    <button class="btn btn-success btn-lg" type="button" id="ID-setvpn-btn" data-vpnActive="1">关闭VPN</button>
                @else
                    <button class="btn btn-success btn-lg" type="button" id="ID-setvpn-btn" data-vpnActive="0">开启VPN</button>
                @endif
            </div>
            <a href="{{ URL('task/create') }}" class="btn btn-warning btn-lg">创建任务</a>
            <button class="btn btn-primary btn-lg" type="button" id="ID-startAll-btn">一键投放</button>
        </div>

        <div class="form-group">
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
                <a class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </a>
                <p id="ID-tips"></p>
            </div>
        </div>

        <table class="table table-striped table-bordered table-h">
            <thead>
            <tr>
                <th></th>
                <th>创建时间</th>
                <th>图 标</th>
                <th>AppID</th>
                <th>任务名称</th>
                <th>成功/投放总量</th>
                <th>投放单价(元)</th>
                <th>任务状态</th>
                <th>操 作</th>
            </tr>
            </thead>
            <tbody>
            @if (count($list))
                @foreach( $list as $v)
                    <tr>
                        <td align="center">
                            @if ($v->state == 0 || $v->state == 2)
                                <input type="checkbox" value="{{ $v->tid }}">
                            @endif
                        </td>
                        <td align="center">{{ $v->created_at }}</td>
                        <td align="center"><img src="{{ $v->icon }}" width="60"></td>
                        <td align="center">{{ $v->appid }}</td>
                        <td align="center" class="J-appkey">{{ $v->appkey }}</td>
                        <td align="center">{{ $v->success_count }}/{{ $v->count }}</td>
                        <td align="center">{{ $v->price }}</td>
                        <td align="center">
                            @if ($v->state == 0)
                                未投放
                            @elseif ($v->state == 1)
                                已上线
                            @elseif ($v->state == 2)
                                已下线
                            @elseif ($v->state == 3)
                                已结束
                            @endif
                        </td>
                        <td align="center">
                            @if ($v->state == 0)
                                <button type="button" class="btn btn-success btn-start" data-id="{{ $v->tid }}">投放</button>
                            @elseif ($v->state == 1)
                                <button type="button" class="btn btn-primary btn-pause" data-id="{{ $v->tid }}">下线</button>
                            @elseif ($v->state == 2)
                                <button type="button" class="btn btn-success btn-start" data-id="{{ $v->tid }}">继续</button>
                            @elseif ($v->state == 3)
                                <button type="button" class="btn btn-default" disabled>已结束</button>
                            @endif

                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    操作 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ URL('task/detail/'.$v->tid) }}">查看任务记录</a></li>
                                    @if ($v->state != 1)
                                        <li><a href="{{ URL('task/'.$v->tid.'/edit?return_url=').Request::getRequestUri() }}" class="btn-edit">修改</a></li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="javascript:;" data-id="{{ $v->tid }}" data-state="{{ $v->state }}" class="btn-remove">删除</a>
                                        </li>
                                    @elseif ($v->state == 2 || $v->state == 3)
                                        <li class="divider"></li>
                                        <li>
                                            <a href="javascript:;" data-id="{{ $v->tid }}" data-state="{{ $v->state }}" class="btn-remove">删除</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" align="center">暂无数据</td>
                </tr>
            @endif
            </tbody>
        </table>
        <div class="row">
            <div class="dataTables_paginate">
                {!! $list->appends($request)->render() !!}
            </div>
        </div>

        <script type="text/html" id="ID-batchList">
            <label class="checkbox-inline">
                <input type="checkbox" value="0" name="batch"> 随机
            </label>
            <br>
            @foreach( $batch as $v)
            <label class="checkbox-inline">
                <input type="checkbox" value="{{ $v->batch }}" name="batch"> {{ $v->batch }} - {{ mb_substr($v->note,0,4,'utf-8') }}
            </label>
            @endforeach
        </script>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var TASK = (function(){
            var win = window,
                    doc = document;
            var $startAll = $('#ID-startAll-btn'),    //一键投放
                    $startBtn = $('.btn-start'),            //开始按钮
                    $pauseBtn = $('.btn-pause'),            //暂停按钮
                    $removeBtn = $('.btn-remove'),          //删除按钮
                    $setvpnBtn = $('#ID-setvpn-btn'),      //设置vpn按钮
                    $batchList = $('#ID-batchList'),
                    $tips = $('#ID-tips');
            var flag = 0,                               //标志位
                    $id = '',                               //任务id
                    $arr_id = [],
                    $curStartBtn = null,
                    $state =0;                              //状态

            return {
                init: function () {
                    //事件
                    this.bindEvents();
                },
                //事件
                bindEvents: function () {
                    var self = this;

                    //点击设置vpn
                    $setvpnBtn.on('click', function () {
                        var _this = $(this),
                            vpnActive = _this.attr('data-vpnActive');

                        _this.hide();
                        _this.before('<button type="button" class="btn btn-default btn-lg disabled">设置中...</button>');

                        $.ajax({
                            type: 'post',
                            url: '{{ URL('setVpn') }}',
                            data: {
                                vpnActive: vpnActive,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    $tips.html('VPN设置成功').parent().show();
                                    _this.prev().text('设置成功');
                                }else {
                                    $tips.html('VPN设置失败').parent().show();
                                    _this.prev().text('设置失败');
                                }
                            }
                        });
                    });

                    //点击投放
                    $startBtn.on('click', function () {
                        var _this = $(this);
                            $id = _this.data('id'),
                            flag = 2,
                            $curStartBtn = _this;

                        self.modalTpl('选择批次', $batchList.html(), true);
                    });

                    //一键投放
                    $startAll.on('click', function () {
                        var _this = $(this);

                        $.each( $(doc).find('input[type=checkbox]:checked'), function(i, n){
                            $arr_id.push($(n).val());
                        });

                        if($arr_id.length>0){
                            flag = 3;
                            self.modalTpl('选择批次', $batchList.html(), true);
                        }else{
                            $tips.html('请选择投放任务').parent().show();
                        }
                    });


                    //点击下线
                    $pauseBtn.on('click', function () {
                        var _this = $(this),
                                $tid = $(this).data('id');

                        _this.hide();
                        _this.after('<button type="button" class="btn btn-default disabled">下线中...</button>');

                        $.ajax({
                            type: 'post',
                            url: '{{ URL('task/pause') }}/'+$tid,
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    $tips.html('任务：'+_this.parents('.table').find('.J-appkey').html()+'已下线！').parent().show();
                                    _this.next().text('已下线');
                                    _this.parent().prev().text('已下线');
                                    _this.siblings('.btn-group').find('.btn-edit').hide();
                                    _this.siblings('.btn-group').find('.divider').hide();
                                    _this.siblings('.btn-group').find('.btn-remove').hide();
                                }else {
                                    $tips.html('任务：'+_this.parents('.table').find('.J-appkey').html()+'下线失败！').parent().show();
                                    _this.next().remove();
                                    _this.show();
                                    _this.parent().prev().text('下线失败');
                                }
                            }
                        });
                    });

                    //点击删除
                    $removeBtn.on('click', function(){
                        var _this = $(this);

                        $state = _this.data('state');
                        if($state == 1){
                            flag = 0;
                            $tips.html('任务进行中，暂时不能删除').parent().show();
                            return false;
                        }else{
                            self.modalTpl('温馨提示', "确定要删除该任务吗？", true);
                            $id = _this.data('id');
                            flag = 1;
                        }
                    });

                    //点击确认
                    $(doc).on('click', '#ID-confirm-btn', function () {
                        var that = $(this);

                        switch (flag){
                            case 1:
                                that.hide();
                                that.after('<button class="btn btn-primary" type="button" disabled>删除中...</button>');

                                $.ajax({
                                    type: 'DELETE',
                                    url: '{{ URL('task') }}/'+$id,
                                    data: {
                                        state: $state,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function (data) {
                                        if(data.status == 1){
                                            $(doc).find('#ID-modal').modal('hide');
                                            flag = 0;

                                            setTimeout(function () {
                                                win.location.href='{{ URl("task") }}';
                                            },300)
                                        }
                                        $tips.html(data.msg);
                                        $tips.parent().show();

                                        that.show();
                                        that.next().remove();
                                    }
                                });
                                break;
                            case 2:
                                var arr = [];
                                $.each( $(doc).find('input[name=batch]:checked'), function(i, n){
                                    arr.push($(n).val());
                                });

                                if(arr.length==0){
                                    alert('请选择批次');
                                    return false;
                                }
                                if(arr.length>1 && arr[0] == '0'){
                                    alert('请选择随机或指定批次');
                                    return false;
                                }

                                that.hide();
                                that.after('<button class="btn btn-primary" type="button" disabled>上线中...</button>');

                                $.ajax({
                                    type: 'post',
                                    url: '{{ URL('task/start') }}/'+$id,
                                    data: {
                                        batch: arr.join('/'),
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function (data) {
                                        if(data.status == 1){
                                            $tips.html('任务：'+$curStartBtn.parents('.table').find('.J-appkey').html()+'已上线！').parent().show();

                                            $curStartBtn.hide();
                                            $curStartBtn.after('<button type="button" class="btn btn-default disabled">已上线</button>');
                                            $curStartBtn.parent().prev().text('已上线');
                                            $curStartBtn.siblings('.btn-group').find('.btn-edit').hide();
                                            $curStartBtn.siblings('.btn-group').find('.divider').hide();
                                            $curStartBtn.siblings('.btn-group').find('.btn-remove').hide();
                                        }else {
                                            $tips.html('任务：'+$curStartBtn.parents('.table').find('.J-appkey').html()+'投放失败！').parent().show();
                                            $curStartBtn.show();
                                            $curStartBtn.parent().prev().text('投放失败');
                                        }

                                        $(doc).find('#ID-modal').modal('hide');
                                        flag = 0;
                                    }
                                });
                                break;
                            case 3:
                                var arr = [];
                                $.each( $(doc).find('input[name=batch]:checked'), function(i, n){
                                    arr.push($(n).val());
                                });

                                if(arr.length==0){
                                    alert('请选择批次');
                                    return false;
                                }
                                if(arr.length>1 && arr[0] == '0'){
                                    alert('请选择随机或指定批次');
                                    return false;
                                }

                                that.hide();
                                that.after('<button class="btn btn-primary" type="button" disabled>上线中...</button>');

                                $.ajax({
                                    type: 'post',
                                    url: '{{ URL('task/startall') }}',
                                    data: {
                                        tid: $arr_id.join('/'),
                                        batch: arr.join('/'),
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function (data) {
                                        if(data.status == 1){
                                            $tips.html('任务已上线！').parent().show();
                                            setTimeout(function () {
                                                window.location.reload();
                                            },800);
                                        }else {
                                            $tips.html('任务投放失败！').parent().show();
                                        }

                                        $(doc).find('#ID-modal').modal('hide');
                                        flag = 0;
                                    }
                                });
                                break;
                        }
                    });
                },
                modalTpl: function($title, $msg, $flag){
                    $(doc).find('#ID-modal').remove();
                    var $class = arguments[3] ? arguments[3] : '';
                    var tpl = '<div class="modal fade" id="ID-modal">' +
                            '<div class="modal-dialog '+$class+'">' +
                            '<div class="modal-content">' +
                            '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '<h4 class="modal-title">' + $title + '</h4>' +
                            '</div>' +
                            '<div class="modal-body">' + $msg + '</div>';
                    if($flag){
                        tpl += '<div class="modal-footer">' +
                                '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                                '<button type="button" class="btn btn-primary" id="ID-confirm-btn">确定</button>' +
                                '</div>';
                    }
                    tpl += '</div>' +
                            '</div>' +
                            '</div>';
                    $('body').append(tpl);
                    $(doc).find('#ID-modal').modal('show');
                }
            }
        })();

        $(function () {
            //初始化
            TASK.init();
        })
    </script>
@endsection