@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">任务记录：{{ count($list) }}</h1>
    </div>

    <div class="wrap">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>AppID</th>
                <th>任务名称</th>
                <th>成功/投放总量</th>
                <th>任务状态</th>
                <th>苹果账号</th>
                <th>密 码</th>
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
                        <td>{{ $v->appid }}</td>
                        <td>{{ $v->appkey }}</td>
                        <td>{{ $v->success_count }}/{{ $v->count }}</td>
                        <td>
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
                        <td>{{ $v->apple_id }}</td>
                        <td>{{ $v->apple_pwd }}</td>
                        <td>{{ $v->idfa }}</td>
                        <td>{{ $v->start_time }}</td>
                        <td>{{ $v->end_time }}</td>
                        <td>
                            @if ($v->status == 0)
                                未开始
                            @elseif ($v->status == 1)
                                已开始
                            @elseif ($v->status == 2)
                                成功
                            @elseif ($v->status == 3)
                                失败<br>
                                @if ($v->code == 204)
                                    苹果账号或密码错误
                                @elseif ($v->code == 205)
                                    账号禁用
                                @elseif ($v->code == 206)
                                    ip错误
                                @elseif ($v->code == 207)
                                    搜索错误
                                @elseif ($v->code == 208)
                                    购买错误
                                @elseif ($v->code == 209)
                                    超时
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10" align="center">暂无数据</td>
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