@extends('welcome')

@section('content')
    <div class="activeityB activityTow">
        <div class="activityBox"></div>
       <button class="actBtn actBtnb">立即注册</button>
    </div>
@endsection

@section('js')
    <script>
        $('.actBtnb').click(function () {
            // B端分享链接跳转到b端注册
            location.href = '/register/43/1';
        })
    </script>
@endsection()