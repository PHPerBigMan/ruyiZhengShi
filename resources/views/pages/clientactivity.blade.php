@extends('welcome')

@section('content')
    <div class="activity">
        <span class="top"></span>
        <div class="btn"></div>
        <div class="text">《如易金服》移动金融智选平台 为小微企业和个人消费者提供专业的
            贷款在线搜索，匹配，推荐和申请服务，意在“让金融变得更加简单和普惠”</div>
        <span class="mid">
            <span></span>
            <div class="midtext">
                <p>1.注册登录即可获得100如易金币。</p>
                <p>2.每个链接不限领取人次。</p>
                <p>3.如易金币可以直接抵现,具体使用请进入如易金服APP了解。</p>
                <p>4.如有疑问,请咨询客服。</p>
            </div>
        </span>

    </div>
@endsection
@section('js')
    <script>
        $('.btn').click(function () {
            // B端分享链接跳转到b端注册
            location.href = '/register/43/0';
        })
    </script>
    @endsection()