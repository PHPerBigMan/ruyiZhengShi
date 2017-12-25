@extends('Back.index')
@section('main')
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px;">
        <legend>用户详细信息</legend>
    </fieldset>

    <table class="layui-table" lay-skin="line">
        <colgroup>
            <col width="150">
            <col width="450">
        </colgroup>
        <tbody>
        <tr>
            <td>头像</td>
            <td> <img src="{{ $user->user_pic }}" alt="" width="140px"></td>
        </tr>
        <tr>
            <td>用户名</td>
            <td>{{ $user->user_name }}</td>
        </tr>
        <tr>
            <td>性别</td>
            <td>{{ $user->user_sex }}</td>
        </tr>
        <tr>
            <td>身份证</td>
            <td>{{ $user->user_idcard }}</td>
        </tr>
        <tr>
            <td>金币</td>
            <td>{{ $user->integral }}</td>
        </tr>
        <tr>
            <td>手机号</td>
            <td>{{ $user->phone }}</td>
        </tr>
        <tr>
            <td>是否为推荐注册</td>
            <td>{{ $user->tui_info }}</td>
        </tr>
        <tr>
            <td>推荐人ID</td>
            <td>{{ $user->tuiUserId }}</td>
        </tr>
        <tr>
            <td>注册时间</td>
            <td>{{ $user->create_time }}</td>
        </tr>
        <tr>
            <td>访问次数</td>
            <td>{{ $user->view_count }}</td>
        </tr>
        </tbody>
    </table>
@endsection