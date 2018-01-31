@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">账号情况</h1>
    </div>

    <div class="wrap">
        <ul class="nav nav-tabs" style="margin-bottom: 20px;">
            <li><a href="{{ URL('account/issue') }}">问题账号</a></li>
            <li class="active"><a href="{{ URL('account/repeat') }}">重复账号</a></li>
            @if (count($list))
                <li class="pull-right">
                    <a href="javascript:;" id="J-export-btn">导出重复账号</a>
                </li>
                <li class="pull-right">
                    <select name="batch" class="form-control">
                        <option>请选择批次</option>
                        @foreach( $batch as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </li>
            @endif
        </ul>

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
                <th>更新时间</th>
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
                            @if ($v->state == 1)
                                使用中
                            @elseif ($v->state == 2)
                                已使用
                            @elseif ($v->state == 3)
                                账号禁用
                            @elseif ($v->state == 4)
                                登录失败
                            @else
                                未使用
                            @endif
                        </td>
                        <td align="center">{{ $v->updated_at }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" align="center">暂无数据</td>
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
            var $exportBtn = $('#J-export-btn'),
                    $tips = $('#ID-tips'),                                   //提示框
                    $batchVal = '';

            return {
                init: function(){
                    this.bindEvents();
                },
                //事件
                bindEvents: function(){
                    var self = this;

                    //导出重复账号
                    $exportBtn.on('click', function () {
                        var _this = $(this),
                                url = "{{ URL('account/repeat/export') }}";
                        $batchVal = $.trim($('select[name=batch] option:selected').val());

                        if(self.checkBatch()){
                            _this.attr('href',url+'/'+$batchVal);
                        }
                    })
                },
                checkBatch: function () {
                    if($batchVal=='' || $batchVal=='请选择批次'){
                        $tips.html('请选择需要导出的批次');
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