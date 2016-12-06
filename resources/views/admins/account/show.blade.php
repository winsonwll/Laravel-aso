@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">账号信息</h1>
    </div>

    <div class="wrap">
        <div class="form-group">
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
                <a class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">关闭</span>
                </a>
                <p id="ID-tips"></p>
            </div>
        </div>
        <table class="table table-striped table-bordered">
            <tbody>
            <tr>
                <th>账号ID</th>
                <td>{{ $res->aid }}</td>
            </tr>
            <tr>
                <th>苹果账号</th>
                <td>{{ $res->apple_id }}</td>
            </tr>
            <tr>
                <th>密 码</th>
                <td>{{ $res->apple_pwd }}</td>
            </tr>
            <tr>
                <th>创建时间</th>
                <td>{{ $res->created_at }}</td>
            </tr>
            @if ($res->updated_at)
                <tr>
                    <th>更新时间</th>
                    <td>{{ $res->updated_at }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        <div style="margin-bottom: 40px">
            <a href="javascript:;" data-id="{{ $res->aid }}" class="btn btn-default btn-lg btn-remove" data-toggle="modal" data-target="#ID-remove-modal">删除</a>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h3>任务记录：<small>{{ count($list) }}</small></h3></div>
            <table class="table table-striped table-bordered table-h">
                <thead>
                <tr>
                    <th>AppID</th>
                    <th>任务名称</th>
                    <th>成功/投放总量</th>
                    <th>设 备</th>
                    <th>开始任务时间</th>
                    <th>完成任务时间</th>
                    <th>完成状态</th>
                </tr>
                </thead>
                <tbody>
                @if (count($list))
                    @foreach( $list as $v)
                        <tr>
                            <td align="center">{{ $v->appid }}</td>
                            <td align="center">{{ $v->appkey }}</td>
                            <td align="center">{{ $v->success_count }}/{{ $v->count }}</td>
                            <td align="center">{{ $v->idfa }}</td>
                            <td align="center">{{ $v->start_time }}</td>
                            <td align="center">{{ $v->end_time }}</td>
                            <td align="center">
                                @if ($v->status == 0)
                                    未开始
                                @elseif ($v->status == 1)
                                    已开始
                                @elseif ($v->status == 2)
                                    成功
                                @elseif ($v->status == 3)
                                    失败
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" align="center">暂无数据</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="dataTables_paginate">
                {!! $list->render() !!}
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
                    确定要删除吗？<br>删除后任务记录将不存在哦~
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
        $(function () {
            var win = window;
            var $removeBtn = $('.btn-remove'),
                $id = $removeBtn.data('id'),
                $confirmBtn = $('#ID-confirm'),
                $tips = $('#ID-tips');

            $confirmBtn.on('click', function(){
                var _this = $(this);

                _this.hide();
                _this.after('<button class="btn btn-primary" type="button" disabled>删除中...</button>');

                $.ajax({
                    type: 'DELETE',
                    url: '{{ URL('/account') }}/'+$id,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data.status == 1){
                            $('#ID-remove-modal').modal('hide');

                            setTimeout(function () {
                                win.location.href='{{ URl("/account") }}';
                            },300)
                        }
                        $tips.html(data.msg);
                        $tips.parent().show();

                        _this.show();
                        _this.next().remove();
                    }
                });
            });
        })
    </script>
@endsection