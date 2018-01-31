@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1 class="col-sm-9">管理员列表</h1>
    </div>

    <div class="wrap">
        <table class="table table-striped table-bordered table-h">
            <thead>
                <tr>
                    <th>管理员ID</th>
                    <th>名 称</th>
                    <th>密 码</th>
                    <th>权 限</th>
                    <th>最近登录时间</th>
                    <th>最近登录IP</th>
                    <th>创建时间</th>
                </tr>
            </thead>
            <tbody>
            @if (count($list))
                @foreach( $list as $v)
                    <tr>
                        <td align="center">{{ $v->id }}</td>
                        <td align="center">{{ $v->name }}</td>
                        <td align="center">{{ $v->pwd }}</td>
                        <td align="center">
                            @if ($v->auth == 0)
                                普通管理员
                            @elseif ($v->auth == 1)
                                超级管理员
                            @endif
                        </td>
                        <td align="center">{{ $v->updated_at }}</td>
                        <td align="center">{{ $v->last_login_ip }}</td>
                        <td align="center">{{ $v->created_at }}</td>
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