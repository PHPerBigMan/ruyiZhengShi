@extends('Business.header')
@section('content')
    <form class="layui-form" action="" >
        <span style="font-size:24px;font-weight: 600;width: 200px;margin-left: 20px">当前匹配值低于{{ $score }}</span>

        <div style="font-size:16px;font-weight: 400;width: 200px;margin-left: 20px;margin-top: 20px">
            @foreach($content as $v)
                <span style="line-height: 50px">
                    {{ $v[0] }}:{{ $v[1] }}
                </span>
                <br />
            @endforeach
        </div>
    </form>
    <button class="layui-btn" style="margin-left: 170px!important;" onclick="GoHead({{ $id }},{{ $user_id }})">继续申请</button>
    @endsection
@section('js')
    <script src="{{ URL::asset('js/shenqing.js') }}?_v=10"></script>
    @endsection