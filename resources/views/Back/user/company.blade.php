@extends('Back.index')
@section('main')
    <p>企业编号：{{ $user->number }}</p>
    <p>企业名称：{{ $user->companyName }}</p>
    <p>企业代码：{{ $user->companyCode }}</p>
    <p>企业法人：{{ $user->companyLegal }}</p>
    <p>法人联系电话：{{ $user->phone }}</p>
    <p>是否通过审核：{{ $user->is_pass == 0 ? "未通过" :$user->is_pass == 1 ? "已通过" : "审核中" }}</p>
    <p>注册时间：{{ $user->create_time }}</p>
    <p>金融管家：{{ $user->companyHouse }}</p>
    <p>管家电话：{{ $user->companyHousePhone }}</p>
    <p>是否拥有金融资质：{{ $user->qualification }}</p>
    <p>
        企业执照：
        <img src="{{ $user->pic }}" alt="" width="400px" class="img">
    </p>
    <p>成交单数： {{ $order->orders_count }}</p>
    <p>总成交金额：{{ $order->total_money or 0}}</p>
@endsection
@section('js')
    <script>
        $('.img').zoomify();
    </script>
    @endsection