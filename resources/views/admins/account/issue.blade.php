@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">账号情况</h1>
    </div>

    <div class="wrap">
        <ul class="nav nav-tabs" style="margin-bottom: 20px;">
            <li class="active"><a href="{{ URL('account/issue') }}">问题账号</a></li>
            <li><a href="{{ URL('account/repeat') }}">重复账号</a></li>
            @if (count($list))
            <li class="pull-right">
                <a href="javascript:;" id="J-export-btn">导出问题账号</a>
            </li>
            <li class="pull-right">
                <select name="batch" class="form-control">
                    <option>选择批次</option>
                    @foreach( $batch as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                </select>
            </li>
            <li class="pull-right">
                <select name="state" class="form-control">
                    <option>选择状态</option>
                    <option value="3">账号禁用</option>
                    <option value="4">登录失败</option>
                </select>
            </li>
            @endif
        </ul>

        @if (count($list))
        <div class="form-group">
            <button type="button" id="ID-removeall-btn" class="btn btn-default pull-right" style="margin-left: 10px;">全部删除</button>
            <button type="button" id="ID-changeall-btn" class="btn btn-warning pull-right">全部改为正常</button>
            问题账号已修改？ <button type="button" id="ID-import-btn" class="btn btn-warning">重新导入</button>
        </div>
        @endif

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
                <th>账号ID</th>
                <th style="text-align: left">苹果账号</th>
                <th>导入批次</th>
                <th>状 态</th>
                <th>创建时间</th>
                <th>操 作</th>
            </tr>
            </thead>
            <tbody>
            @if (count($list))
                @foreach( $list as $v)
                    <tr>
                        <td align="center">{{ $v->aid }}</td>
                        <td align="left">{{ $v->apple_id }}</td>
                        <td align="center">{{ $v->batch }}</td>
                        <td align="center">
                            {{--账号状态 0 未使用  1 使用中  2 已使用  3 账号禁用  4 登录失败--}}
                            @if ($v->state == 3)
                                账号禁用
                            @elseif ($v->state == 4)
                                登录失败
                            @endif
                        </td>
                        <td align="center">{{ $v->created_at }}</td>
                        <td align="center">
                            <button class="btn btn-primary J-change-btn" type="button" data-id="{{ $v->aid }}">改为正常</button>
                            <button class="btn btn-default J-remove-btn" type="button" data-id="{{ $v->aid }}">删 除</button>
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
                {!! $list->render() !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var ACCOUNT = (function(){
            var win = window,
                    doc = document;
            var $importBtn = $('#ID-import-btn'),
                    $changeallBtn = $('#ID-changeall-btn'),
                    $removeallBtn = $('#ID-removeall-btn'),
                    $changeBtn = $('.J-change-btn'),
                    $removeBtn = $('.J-remove-btn'),
                    $exportBtn = $('#J-export-btn'),
                    $tips = $('#ID-tips'),                                   //提示框
                    $batchVal = '',
                    $stateVal = '',
                    $span = null;

            var flag = 0;

            return {
                init: function(){
                    this.bindEvents();
                },
                //事件
                bindEvents: function(){
                    var self = this;

                    //导入账号
                    $importBtn.on('click', function () {
                        var html = '<form class="form-horizontal" id="ID-file-form" action="" method="post" enctype="multipart/form-data">' +
                                '<div class="file-form">' +
                                '<div class="file-form-wrap">' +
                                '<button type="button" class="btn btn-warning" id="ID-upload">选择文件</button>' +
                                '<span>请上传txt格式文件</span>' +
                                '<input type="file" name="accounts" accept=".txt" class="hide">' +
                                '{{ csrf_field() }}' +
                                '<button class="btn btn-warning" type="submit" id="ID-import">立即导入</button>' +
                                '</div>' +
                                '<div class="progress">' +
                                '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">' +
                                '<span>账号导入中...</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</form>';

                        $(doc).find('#ID-modal').remove();
                        self.modalTpl('导入已修改的问题账号', html, false);
                        $(doc).find('#ID-modal').modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });

                        var $fileForm = $(doc).find('#ID-file-form'),
                                $file = $(doc).find('input[name=accounts]'),
                                $uploadBtn = $(doc).find('#ID-upload'),
                                $importBtn = $(doc).find('#ID-import'),
                                $progress = $(doc).find('#ID-file-form .progress');

                        $span = $(doc).find('.file-form-wrap span');

                        $uploadBtn.on('click', function () {
                            $file.trigger('click');
                        });

                        $file.change(function () {
                            var $val = $.trim($file.val());

                            if(!$val.length){
                                $span.html('<b>上传文件不能为空！</b>');
                                return false;
                            }
                            var $ext=$val.substr($val.lastIndexOf(".")).toLowerCase();//获得文件后缀名
                            if($ext!='.txt'){
                                $span.html('<b>请上传txt格式的文件！</b>');
                                return false;
                            }

                            $span.html($val);
                            $uploadBtn.hide();
                            $file.addClass('active');
                        });

                        //点击立即导入
                        $importBtn.on('click', function () {
                            if($file.val() == ''){
                                $span.html('<b>上传文件不能为空！</b>');
                                return false;
                            }

                            flag = 1;
                            $progress.show();

                            var formdata=new FormData($fileForm[0]);
                            formdata.append("file" , $file[0].files[0]);
                            formdata.append("action" , 'issue');

                            $.ajax({
                                type : 'post',
                                url: "{{ URL('account/import') }}",
                                data : formdata,
                                cache : false,
                                processData : false, // 不处理发送的数据，因为data值是Formdata对象，不需要对数据做处理
                                contentType : false, // 不设置Content-type请求头
                                success : function(data){
                                    var arr = data.data.listSuccess;
                                    var $len = data.data.len;
                                    var html = '<p>导入账号总数：'+$len+'&nbsp;&nbsp;&nbsp;&nbsp;成功数：'+arr.length+'</p>' +
                                            '<table class="table table-striped table-bordered">' +
                                            '<thead>' +
                                            '<tr>' +
                                            '<th>苹果账号</th>' +
                                            '<th>密码</th>' +
                                            '<th>导入状态</th>' +
                                            '</tr>' +
                                            '</thead>' +
                                            '<tbody>';
                                    for(var i=0,len=arr.length;i<len;i++){
                                        html += '<tr>' +
                                                '<td>'+arr[i][0]+'</td>' +
                                                '<td>'+arr[i][1]+'</td>' +
                                                '<td>成功</td>' +
                                                '</tr>';
                                    }

                                    if(data.status == 3){
                                        var arrEr = data.data.listError;
                                        for(var i=0,len=arrEr.length;i<len;i++){
                                            html += '<tr>' +
                                                    '<td colspan="2">'+arrEr[i]+'</td>' +
                                                    '<td>失败</td>' +
                                                    '</tr>';
                                        }
                                    }

                                    html += '</tbody>' +
                                            '</table>';

                                    $(doc).find('.modal-body').css({
                                        maxHeight: 424,
                                        marginBottom: 10,
                                        overflowX: 'auto'
                                    });
                                    $(doc).find('.modal-body').html(html);

                                    flag = 2;
                                }
                            });

                            return false;
                        });

                        $(doc).find('#ID-modal, #ID-modal .close').on('click', function () {
                            if(flag==1){
                                return false;
                            }else if(flag==2){
                                win.location.reload();

                                //win.location.href='/account';
                                return false;
                            }
                        });
                    });

                    //全部改为正常
                    $changeallBtn.on('click', function () {
                        var _this = $(this);

                        _this.hide();
                        _this.after('<button class="btn btn-default" type="button" disabled>修改中...</button>');
                        $.ajax({
                            type: 'POST',
                            url: '{{ URL('account/changeall') }}',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    win.location.reload();
                                }
                                $tips.html(data.msg);
                                $tips.parent().show();
                            }
                        });
                    });

                    //改为正常状态
                    $changeBtn.on('click', function () {
                        var _this = $(this),
                            $aid = _this.attr('data-id');

                        _this.hide();
                        _this.after('<button class="btn btn-default" type="button" disabled>修改中...</button>');

                        $.ajax({
                            type: 'POST',
                            url: '{{ URL('account/change') }}'+'/'+$aid,
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    _this.parents('tr').remove();
                                }
                                $tips.html(data.msg);
                                $tips.parent().show();
                            }
                        });
                    });

                    //全部删除
                    $removeallBtn.on('click', function () {
                        var _this = $(this);

                        _this.hide();
                        _this.after('<button class="btn btn-default" type="button" disabled>删除中...</button>');
                        $.ajax({
                            type: 'POST',
                            url: '{{ URL('account/removeall') }}',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    win.location.reload();
                                }
                                $tips.html(data.msg);
                                $tips.parent().show();
                            }
                        });
                    });

                    //删除
                    $removeBtn.on('click', function () {
                        var _this = $(this),
                                $aid = _this.attr('data-id');

                        _this.hide();
                        _this.after('<button class="btn btn-default" type="button" disabled>删除中...</button>');

                        $.ajax({
                            type: 'DELETE',
                            url: '{{ URL('account') }}/'+$aid,
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if(data.status == 1){
                                    _this.parents('tr').remove();
                                }
                                $tips.html(data.msg);
                                $tips.parent().show();
                            }
                        });
                    });

                    //导出问题账号
                    $exportBtn.on('click', function () {
                        var _this = $(this),
                                url = "{{ URL('account/issue/export') }}";
                        $batchVal = $.trim($('select[name=batch] option:selected').val());
                        $stateVal = $.trim($('select[name=state] option:selected').val());

                        if(self.checkBatch() && self.checkState()){
                            _this.attr('href',url+'?batch='+$batchVal+'&state='+$stateVal);
                        }
                    })
                },
                modalTpl: function($title, $msg, $flag){
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
                },
                checkBatch: function () {
                    if($batchVal=='' || $batchVal=='选择批次'){
                        $tips.html('请选择需要导出的批次');
                        $tips.parent().show();
                        return false;
                    }
                    return true;
                },
                checkState: function () {
                    if($stateVal=='' || $stateVal=='选择状态'){
                        $tips.html('请选择需要导出的账号状态');
                        $tips.parent().show();
                        return false;
                    }
                    return true;
                }
            }
        })();

        $(function () {
            //初始化
            ACCOUNT.init();
        });
    </script>
@endsection