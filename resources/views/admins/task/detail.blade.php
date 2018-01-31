@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9"><img src="{{ $res->icon }}" width="60"> {{ $res->appkey }}</h1>
    </div>

    <div class="wrap">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#ID-task-record" aria-controls="ID-task-record" role="tab" data-toggle="tab">任务记录</a>
            </li>
            <li role="presentation">
                <a href="#ID-task-Info" aria-controls="ID-task-Info" role="tab" data-toggle="tab">任务信息</a>
            </li>
        </ul>

        <div class="tab-content" style="margin-top: 20px;">
            <div role="tabpanel" class="tab-pane active" id="ID-task-record">
                @if (count($list))
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3>统计信息</h3></div>
                        <table class="table table-striped table-bordered">
                            <tbody>
                            <tr>
                                <th>请求数</th>
                                <td>{{ $res->vcnt }}</td>
                            </tr>
                            <tr>
                                <th>成功数</th>
                                <td>{{ $res->success_count }}</td>
                            </tr>
                            <tr class="warning">
                                <th colspan="2" style="font-weight: 400">失败信息</th>
                            </tr>
                            @if ($res->state == 2 || $res->state == 3)
                                <tr>
                                    <th>人工干预过</th>
                                    <td>{{ $status[10] }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>账号或密码错误</th>
                                <td>{{ $status[0] }}</td>
                            </tr>
                            <tr>
                                <th>账号禁用数</th>
                                <td>{{ $status[1] }}</td>
                            </tr>
                            <tr>
                                <th>关键词没有搜索到指定app数</th>
                                <td>{{ $status[2] }}</td>
                            </tr>
                            <tr>
                                <th>此时无法购买</th>
                                <td>{{ $status[3] }}</td>
                            </tr>
                            <tr>
                                <th>超时数</th>
                                <td>{{ $status[4] }}</td>
                            </tr>
                            <tr>
                                <th>不能连接ItunesStore数</th>
                                <td>{{ $status[5] }}</td>
                            </tr>
                            <tr>
                                <th>登录失败数</th>
                                <td>{{ $status[6] }}</td>
                            </tr>
                            <tr>
                                <th>没查到app详情数</th>
                                <td>{{ $status[7] }}</td>
                            </tr>
                            <tr>
                                <th>数据格式错数</th>
                                <td>{{ $status[8] }}</td>
                            </tr>
                            <tr>
                                <th>服务器验证失败</th>
                                <td>{{ $status[9] }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-heading"><h3 style="margin-top: 10px">任务记录：<small>{{ $count }}</small></h3></div>
                    <table class="table table-striped table-bordered table-h">
                        <thead>
                        <tr>
                            <th>开始时间</th>
                            <th>udid</th>
                            <th>idfa</th>
                            <th>苹果账号</th>
                            <th>执行时间</th>
                            <th>IP</th>
                            <th>完成状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($list))
                            @foreach( $list as $v)
                                <tr>
                                    <td align="center">{{ $v->start_time }}</td>
                                    <td align="center">{{ $v->udid }}</td>
                                    <td align="center">{{ $v->idfa }}</td>
                                    <td align="center">{{ $v->apple_id }}</td>
                                    <td align="center">
                                        @if (!empty($v->end_time))
                                            耗时：
                                            {{ floor((strtotime($v->end_time) - strtotime($v->start_time))/86400) }} 天
                                            {{ floor((strtotime($v->end_time) - strtotime($v->start_time))%86400/3600) }} 时
                                            {{ floor((strtotime($v->end_time) - strtotime($v->start_time))%86400/60) }} 分
                                            {{ floor((strtotime($v->end_time) - strtotime($v->start_time))%86400%60) }} 秒
                                        @else
                                            进行中
                                        @endif
                                    </td>
                                    <td align="center">{{ $v->ip }}</td>
                                    <td align="center">
                                        {{--任务完成状态码  0:未开始 1:进行中  200:成功  204:账号或密码错误   205:账号禁用  207:该关键词没有搜索到指定app  209:超时  211:不能连接ItunesStore 212:登录失败    213:没查到app详情    214:数据格式错--}}
                                        @if ($v->status == 0)
                                            未开始
                                        @elseif ($v->status == 1)
                                            进行中
                                        @elseif ($v->status == 200)
                                            成功
                                        @elseif ($v->status == 204)
                                            账号或密码错误
                                        @elseif ($v->status == 205)
                                            账号禁用
                                        @elseif ($v->status == 207)
                                            该关键词没有搜索到指定app
                                        @elseif ($v->status == 208)
                                            此时无法购买
                                        @elseif ($v->status == 209)
                                            超时
                                        @elseif ($v->status == 211)
                                            不能连接itunesStore
                                        @elseif ($v->status == 212)
                                            登录失败
                                        @elseif ($v->status == 213)
                                            没查到app详情
                                        @elseif ($v->status == 214)
                                            数据格式错
                                        @elseif ($v->status == 216)
                                            服务器验证失败
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
            <div role="tabpanel" class="tab-pane" id="ID-task-Info">
                <table class="table table-striped table-bordered">
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
                        <th>投放单价</th>
                        <td>{{ $res->price }} 元</td>
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
                    <tr>
                        <th>更新时间</th>
                        <td>{{ $res->updated_at }}</td>
                    </tr>
                    <tr class="warning">
                        <th colspan="2" style="font-weight: 400">参数信息</th>
                    </tr>
                    <tr>
                        <th>超时时间</th>
                        <td>{{ $res->timeout }}</td>
                    </tr>
                    <tr>
                        <th>是否下载App</th>
                        <td>
                            @if ($res->download == 1)
                                是
                            @else
                                否
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>是否激活App</th>
                        <td>
                            @if ($res->active == 1)
                                是
                            @else
                                否
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>是否后登录</th>
                        <td>
                            @if ($res->postLoginStore == 1)
                                是
                            @else
                                否
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>是否滑动</th>
                        <td>
                            @if ($res->openDetailDirectly == 1)
                                简约模式
                            @else
                                正常模式
                            @endif
                        </td>
                    </tr>
                    {{--<tr>
                        <th>是否开启VPN</th>
                        <td>
                            @if ($res->vpnActive == 1)
                                开启
                            @else
                                不开启
                            @endif
                        </td>
                    </tr>--}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection