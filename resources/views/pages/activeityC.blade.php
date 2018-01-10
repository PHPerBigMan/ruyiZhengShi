@extends('welcome')

@section('content')
    <div class="activeityC activityTow">
        <div class="activityBox"></div>
        <button class="actBtn actBtnc">立即加入</button>
    </div>
@endsection
@section('js')
    <script>
        $('.actBtnc').click(function () {
            // C端分享链接跳转到c端注册
            location.href = '/register/43/0';
        })
    </script>
@endsection()