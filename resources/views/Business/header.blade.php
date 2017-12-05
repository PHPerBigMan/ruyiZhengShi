<!DOCTYPE html>

<html>

<head>
  <meta charset="utf-8">
  <title>后台管理</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">

  <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}" media="all" />
  <link rel="stylesheet" href="{{ URL::asset('business/css/global.css') }}" media="all">
  <link rel="stylesheet" href="{{ URL::asset('plugins/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('css/business.css?_v20') }}">
  <link rel="stylesheet" href="{{ URL::asset('css/zoomify.min.css') }}">
</head>
@yield('content')



<script src="{{ URL::asset('js/index.js') }}"></script>

<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/vue/2.4.2/vue.js"></script>
<script src="{{ URL::asset('js/business.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('layui/layui.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('layui/layui.all.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/zoomify.min.js') }}"></script>
@yield('js')