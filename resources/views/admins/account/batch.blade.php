@extends('admins.master.base')

@section('content')
    <div class="page-header row">
        <h1>批次列表</h1>
    </div>

    <div class="wrap">
        <table class="table table-striped table-bordered table-h">
            <thead>
            <tr>
                <th>批 次</th>
                <th>备 注</th>
                <th>创建时间</th>
                <th>操 作</th>
            </tr>
            </thead>
            <tbody>
            @if (count($list))
                @foreach( $list as $v)
                    <tr>
                        <td align="center">{{ $v->batch }}</td>
                        <td align="center">{{ $v->note }}</td>
                        <td align="center">{{ $v->created_at }}</td>
                        <td align="center">
                            <a class="btn btn-success" href="{{ URL('account/batch/'.$v->batch) }}">编辑</a>
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