@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1><small class="pull-right">设备总数：{{ $count }}</small>设备列表</h1>
    </div>

    <div class="wrap">
        <table class="table table-striped table-bordered table-h">
            <thead>
            <tr>
                <th>设备ID</th>
                <th style="text-align: left">udid</th>
                <th style="text-align: left">idfa</th>
                <th>操 作</th>
            </tr>
            </thead>
            <tbody>
            @if (count($list))
                @foreach( $list as $v)
                    <tr>
                        <td align="center">{{ $v->cid }}</td>
                        <td align="left">{{ $v->udid }}</td>
                        <td align="left">{{ $v->idfa }}</td>
                        <td align="center">
                            <a class="btn btn-success" href="{{ URL('client/'.$v->cid) }}">任务记录</a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" align="center">暂无数据</td>
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