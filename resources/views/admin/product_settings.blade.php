@extends('admin.base')
@section('title','商品分类设置')
@section('link')
    @parent
@stop
@section('stylesheet')
@stop
@section('nav')
    @parent
@stop
@section('subject','商品分类设置')
@section('body')
    @parent
    <div class="product-sort"></div>
    <script>
        Sort.init({dom: $(".product-sort"), url: "{{url('admin/sort')}}", app: "product"});
    </script>
@stop
