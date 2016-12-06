@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">任务列表</h1>
        <form action="{{ URL('/task') }}" method="get">
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" placeholder="请输入Appid或关键词" class="input-sm form-control" name="keyword" value="{{ $request['keyword'] or '' }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                    </span>
                </div>
            </div>
        </form>
    </div>

    <div class="wrap">
        <div class="form-group">
            <a href="{{ URL('/task/create') }}" class="btn btn-warning btn-lg" id="ID-import-btn">创建任务</a>
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
            <th>任务ID</th>
            <th>AppID</th>
            <th>任务名称</th>
            <th>成功/投放总量</th>
            <th>任务状态</th>
            <th>操 作</th>
        </tr>
        </thead>
        <tbody>
        @if (count($list))
            @foreach( $list as $v)
                <tr>
                    <td align="center">{{ $v->tid }}</td>
                    <td align="center">{{ $v->appid }}</td>
                    <td align="center">{{ $v->appkey }}</td>
                    <td align="center">{{ $v->success_count }}/{{ $v->count }}</td>
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
                                <li><a href="{{ URL('/task/'.$v->tid) }}">查看</a></li>
                                @if ($v->state != 1)
                                    <li><a href="{{ URL('/task/'.$v->tid.'/edit') }}" class="btn-edit">修改</a></li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="javascript:;" data-id="{{ $v->tid }}" data-state="{{ $v->state }}" class="btn-remove" data-toggle="modal" data-target="#ID-remove-modal">删除</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" align="center">暂无数据</td>
            </tr>
        @endif
        </tbody>
    </table>
        <div class="row">
        <div class="dataTables_paginate">
            {!! $list->appends($request)->render() !!}
        </div>
    </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ID-remove-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">温馨提示</h4>
                </div>
                <div class="modal-body">
                    确定要删除吗？
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" id="ID-confirm">确认删除</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var TASK = (function(){
            var win = window;
            var $startBtn = $('.btn-start'),            //开始按钮
                $pauseBtn = $('.btn-pause'),            //暂停按钮
                $removeBtn = $('.btn-remove'),          //删除按钮
                $confirmBtn = $('#ID-confirm'),         //确认删除按钮
                $tips = $('#ID-tips');
            var flag = 0,                               //标志位
                $id = '',                               //任务id
                $state =0;                              //状态

            return {
                init: function () {
                    //事件
                    this.bindEvents();
                },
                //事件
                bindEvents: function () {
                    //点击开始
                    $startBtn.on('click', function () {
                        var _this = $(this),
                             $tid = $(this).data('id');

                        _this.hide();
                        _this.after('<button type="button" class="btn btn-default disabled">上线中...</button>');

                        $.ajax({
                            type: 'post',
                            url: '{{ URL('/task/start') }}',
                            data: {
                                tid: $tid,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                $tips.html(data.msg).parent().show();

                                _this.next().text('已上线');
                                _this.parent().prev().text('已上线');
                                _this.next('.btn-group').find('.btn-edit').hide();
                                _this.next('.btn-group').find('.divider').hide();
                                _this.next('.btn-group').find('.btn-remove').hide();
                            }
                        });
                    });

                    //点击暂停
                    $pauseBtn.on('click', function () {
                        var _this = $(this),
                            $tid = $(this).data('id');

                        _this.hide();
                        _this.after('<button type="button" class="btn btn-default disabled">下线中...</button>');

                        $.ajax({
                            type: 'post',
                            url: '{{ URL('/task/pause') }}',
                            data: {
                                tid: $tid,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                $tips.html(data.msg).parent().show();

                                _this.next().text('已下线');
                                _this.parent().prev().text('已下线');
                                _this.next('.btn-group').find('.btn-edit').hide();
                                _this.next('.btn-group').find('.divider').hide();
                                _this.next('.btn-group').find('.btn-remove').hide();
                            }
                        });
                    });

                    //点击删除
                    $removeBtn.on('click', function(){
                        var _this = $(this);

                        flag = 1;
                        $id = _this.data('id');

                        $state = _this.data('state');
                        if($state == 1){
                            flag = 0;
                            $tips.html('任务进行中，暂时不能删除').parent().show();
                            return false;
                        }
                    });

                    //点击确认删除
                    $confirmBtn.on('click', function(){
                        var _this = $(this);

                        if(flag){
                            _this.hide();
                            _this.after('<button class="btn btn-primary" type="button" disabled>删除中...</button>');

                            $.ajax({
                                type: 'DELETE',
                                url: '{{ URL('/task') }}/'+$id,
                                data: {
                                    state: $state,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == 1){
                                        $('#ID-remove-modal').modal('hide');
                                        flag = 0;

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
                    });
                }
            }
        })();

        $(function () {
            //初始化
            TASK.init();
        })
    </script>
@endsection