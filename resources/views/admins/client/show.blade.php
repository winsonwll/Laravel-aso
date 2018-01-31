@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">设备信息</h1>
    </div>

    <div class="wrap">
        <table class="table table-striped table-bordered ">
            <tbody>
            <tr>
                <th>设备ID</th>
                <td>{{ $res->cid }}</td>
            </tr>
            <tr>
                <th>udid</th>
                <td>{{ $res->udid }}</td>
            </tr>
            <tr>
                <th>idfa</th>
                <td>{{ $res->idfa }}</td>
            </tr>
            <tr>
                <th>创建时间</th>
                <td>{{ $res->created_at }}</td>
            </tr>
            <tr>
                <th>更新时间</th>
                <td>{{ $res->updated_at }}</td>
            </tr>
            </tbody>
        </table>

        <div class="panel panel-default">
            <div class="panel-heading"><h3>任务记录：<small>{{ count($list) }}</small></h3></div>
            <table class="table table-striped table-bordered table-h">
                <thead>
                    <tr>
                        <th>AppID</th>
                        <th>任务名称</th>
                        <th>成功/投放总量</th>
                        <th>苹果账号</th>
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
                            <td align="center">{{ $v->apple_id }}</td>
                            <td align="center">{{ $v->start_time }}</td>
                            <td align="center">{{ $v->end_time }}</td>
                            <td align="center">
                                {{--任务完成状态码  0:未开始 1:进行中  200:成功  205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错--}}
                                @if ($v->status == 0)
                                    未开始
                                @elseif ($v->status == 1)
                                    进行中
                                @elseif ($v->status == 200)
                                    成功
                                @elseif ($v->status == 205)
                                    账号禁用
                                @elseif ($v->status == 207)
                                    该关键词没有搜索到指定app
                                @elseif ($v->status == 209)
                                    超时
                                @elseif ($v->status == 211)
                                    不能连接ItunesStore
                                @elseif ($v->status == 212)
                                    登录失败
                                @elseif ($v->status == 213)
                                    没查到app详情
                                @elseif ($v->status == 214)
                                    数据格式错
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
@endsection