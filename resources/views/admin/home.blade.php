<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="Sign">
    <title>@yield('title','首页')</title>
    @section('link')
        <link href="{{ url('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ url('default/css/admin_home.css')}}" rel="stylesheet">
        <script src="{{ url('default/js/jquery.js') }}"></script>
        <script src="{{ url('bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ url('default/js/main.js') }}"></script>
    @show
    <style>
        @section('stylesheet')
        @show
    </style>
</head>
<body>
@section('nav')
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <span class="navbar-brand">system</span>
            <span class="article active navbar-brand"><a href="{{url('admin/feature/article')}}">Article</a></span>
            <span class="product navbar-brand"><a href="{{url('admin/feature/product')}}">Product</a></span>
            <span class="style navbar-brand"><a href="javascript:void(0)">Style</a></span>
            <sapn class="zone navbar-brand"><a href="javascript:void(0)">Zone</a></sapn>
        </div>
    </div>
</nav>
@show
<h3>@yield('subject','主题未定义')</h3>
<hr/>
@section('body')
@show
</body>
<footer>
    @section('footer')
        <div class="common-footer">
        </div>
    @show
</footer>
</html>
