@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">财务报表</h1>
        <form action="{{ URL('report') }}" method="get">
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" placeholder="请输入任务名称" class="input-sm form-control" name="keyword" value="{{ $request['keyword'] or '' }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                    </span>
                </div>
            </div>
        </form>
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

        <table class="table table-striped table-bordered table-h">
        <thead>
        <tr>
            <th>AppID</th>
            <th>任务名称</th>
            <th>成功/投放总量</th>
            <th>投放单价(元)</th>
            <th>执行时间</th>
            {{--<th>使用账号数</th>--}}
            <th>完成状态</th>
            <th>操 作</th>
        </tr>
        </thead>
        <tbody>
        @if (count($list))
            @foreach( $list as $v)
                <tr>
                    <td align="center">{{ $v->appid }}</td>
                    <td align="center">{{ $v->appkey }}</td>
                    <td align="center">{{ $v->success_count }}/{{ $v->count }}</td>
                    <td align="center">{{ $v->price }}</td>
                    <td align="center">
                        @if (!empty($v->last_time))
                            耗时：
                            {{ floor((strtotime($v->last_time) - strtotime($v->first_time))/86400) }} 天
                            {{ floor((strtotime($v->last_time) - strtotime($v->first_time))%86400/3600) }} 时
                            {{ floor((strtotime($v->last_time) - strtotime($v->first_time))%86400/60) }} 分
                            {{ floor((strtotime($v->last_time) - strtotime($v->first_time))%86400%60) }} 秒
                        @else
                            开始时间：{{ $v->first_time }}
                        @endif
                    </td>
                    {{--<td align="center">{{ $v->count(apple_id) }}</td>--}}
                    <td align="center">
                        @if (!empty($v->last_time))
                            执行完毕
                        @else
                            执行中
                        @endif
                    </td>
                    <td align="center">
                        <a href="{{ URL('report/export/'.$v->tid) }}" class="btn btn-success btn-start">导出报表</a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" align="center">暂无数据</td>
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
@endsection