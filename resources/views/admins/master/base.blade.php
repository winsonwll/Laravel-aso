<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('admins/images/favicon.ico') }}">
    <title>ASO后台管理系统</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
    <link href="{{ asset('admins/css/dashboard.css') }}" rel="stylesheet">
    @yield('css')
</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="javascript:;">ASO</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{{ URL('task') }}">任务列表</a></li>
                <li><a href="{{ URL('task/create') }}">创建任务</a></li>
                <li><a href="{{ URL('task/record') }}">完成情况</a></li>
                <li><a href="{{ URL('account') }}">账号列表</a></li>
                <li><a href="{{ URL('account/issue') }}">账号情况</a></li>
                <li><a href="{{ URL('report') }}">财务报表</a></li>
                <li><a href="{{ URL('logout') }}">退出</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <h3>任务管理</h3>
            <ul class="nav nav-sidebar">
                <li @if (Request::is('task') || Request::is('task/{id}')) class="active" @endif><a href="{{ URL('task') }}">任务列表</a></li>
                <li @if (Request::is('task/record') || Request::is('task/detail/*')) class="active" @endif><a href="{{ URL('task/record') }}">完成情况</a></li>
                <li @if (Request::is('task/create')) class="active" @endif><a href="{{ URL('task/create') }}">创建任务</a></li>
            </ul>
            <h3>账号管理</h3>
            <ul class="nav nav-sidebar">
                <li @if (Request::is('account')) class="active" @endif><a href="{{ URL('account') }}">账号列表</a></li>
                {{--<li @if (Request::is('account/create')) class="active" @endif><a href="{{ URL('account/create') }}">添加账号</a></li>--}}
                <li @if (Request::is('account/issue') || Request::is('account/repeat')) class="active" @endif><a href="{{ URL('account/issue') }}">账号情况</a></li>
                <li @if (Request::is('account/batch')) class="active" @endif><a href="{{ URL('account/batch') }}">批次情况</a></li>
            </ul>
            <h3>设备管理</h3>
            <ul class="nav nav-sidebar">
                <li @if (Request::is('client')) class="active" @endif><a href="{{ URL('client') }}">设备列表</a></li>
                {{--<li @if (Request::is('client/create')) class="active" @endif><a href="{{ URL('client/create') }}">添加设备</a></li>--}}
            </ul>
            <h3>报表管理</h3>
            <ul class="nav nav-sidebar">
                <li @if (Request::is('report')) class="active" @endif><a href="{{ URL('report') }}">财务报表</a></li>
            </ul>
            @if (Session::get('admin')->auth == 1)
                <h3>管理员管理</h3>
                <ul class="nav nav-sidebar">
                    <li @if (Request::is('admin')) class="active" @endif><a href="{{ URL('admin') }}">管理员列表</a></li>
                    <li @if (Request::is('admin/create')) class="active" @endif><a href="{{ URL('admin/create') }}">添加管理员</a></li>
                    <li><a href="">权限分配</a></li>
                </ul>
            @endif
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            @yield('content')
        </div>
    </div>
</div>

<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="{{ URL('admins/js/docs.min.js') }}"></script>
@yield('js')
</body>
</html>