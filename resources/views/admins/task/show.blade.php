@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">{{ $res->appkey }}</h1>
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
        <table class="table table-striped table-bordered ">
            <tbody>
            <tr>
                <th>任务ID</th>
                <td>{{ $res->tid }}</td>
            </tr>
            <tr>
                <th>AppID</th>
                <td>{{ $res->appid }}</td>
            </tr>
            <tr>
                <th>任务名称</th>
                <td>{{ $res->appkey }}</td>
            </tr>
            <tr>
                <th>投放总量</th>
                <td>{{ $res->count }}</td>
            </tr>
            <tr>
                <th>成功数</th>
                <td>{{ $res->success_count }}</td>
            </tr>
            <tr>
                <th>任务状态</th>
                <td>
                    @if ($res->state == 0)
                        未投放
                    @elseif ($res->state == 1)
                        已上线
                    @elseif ($res->state == 2)
                        已下线
                    @elseif ($res->state == 3)
                        已结束
                    @endif
                </td>
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

        @if ($res->state == 0 || $res->state == 3)
        <div>
            <a href="{{ URL('/task/'.$res->tid.'/edit') }}" class="btn btn-success btn-lg">修改</a>
            <a href="javascript:;" data-id="{{ $res->tid }}" class="btn btn-default btn-lg btn-remove" data-toggle="modal" data-target="#ID-remove-modal">删除</a>
        </div>
        @endif
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
        $(function () {
            var win = window;
            var $removeBtn = $('.btn-remove'),
                $id = $removeBtn.data('id'),
                $confirmBtn = $('#ID-confirm'),
                $tips = $('#ID-tips');
            var flag = 0;

            $removeBtn.click(function () {
                flag = 1;
                var $state = {{ $res->state }};
                if($state == 1){
                    flag = 0;
                    $tips.html('任务进行中，暂时不能删除').parent().show();
                    return false;
                }
            });

            $confirmBtn.on('click', function(){
                var _this = $(this);

                if(flag){
                    _this.hide();
                    _this.after('<button class="btn btn-primary" type="button" disabled>删除中...</button>');

                    $.ajax({
                        type: 'DELETE',
                        url: '{{ URL('/task') }}/'+$id,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function (data) {
                            if(data.state == 1){
                                $('#ID-remove-modal').modal('hide');

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
        })
    </script>
@endsection