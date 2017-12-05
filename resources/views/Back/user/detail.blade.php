@extends('Back.index')
@section('main')
    <img src="{{ $user->user_pic }}" alt="" width="140px">
    <p>用户名：{{ $user->user_name }}</p>
    <p>性别：{{ $user->user_sex }}</p>
    <p>身份证：{{ $user->user_idcard }}</p>
    <p>金币：{{ $user->integral }}</p>
    <p>手机号：{{ $user->phone }}</p>
    <p>是否为推荐注册：{{ $user->tui_info }}</p>
    <p>推荐人ID ：{{ $user->tuiUserId }}</p>
    <p>注册时间：{{ $user->create_time }}</p>
    <p>访问次数: {{ $user->view_count }}</p>
@endsection