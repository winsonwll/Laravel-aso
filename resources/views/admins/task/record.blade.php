@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">完成情况</h1>
    </div>

    <div class="wrap">
        <table class="table table-striped table-bordered table-h">
            <thead>
            <tr>
                <th>图 标</th>
                <th>任务名称</th>
                <th>成功/投放总量</th>
                <th>任务状态</th>
                <th>首次执行时间</th>
                <th>完成时间</th>
                <th>操 作</th>
            </tr>
            </thead>
            <tbody>
            @if (count($list))
                @foreach( $list as $v)
                    <tr>
                        <td align="center"><img src="{{ $v->icon }}" width="60"></td>
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
                        <td align="center">{{ $v->first_time }}</td>
                        <td align="center">
                            @if (!empty($v->last_time))
                                {{ $v->last_time }}
                            @else
                                进行中
                            @endif
                        </td>
                        <td align="center">
                            <a class="btn btn-success" href="{{ URL('task/detail/'.$v->tid) }}">查看</a>
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
        <div class="row">
            <div class="dataTables_paginate">
                {!! $list->render() !!}
            </div>
        </div>
    </div>
@endsection