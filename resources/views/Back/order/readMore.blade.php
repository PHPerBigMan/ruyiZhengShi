<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="//cdn.bootcss.com/vue/1.0.24/vue.min.js"></script>
    <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}"  media="all">
</head>
<body>
    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this">订单信息</li>
            <li>借款用户基本信息</li>
            <li>产品信息</li>
            {{--<li>需求资料</li>--}}
            {{--<li>担保品资料</li>--}}
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <table class="layui-table" lay-skin="line">
                    <colgroup>
                        <col width="150">
                        <col width="150">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>{{ $data->order_id }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>订单总额(万元)</td>
                        <td>{{ $data->order_count }}</td>
                    </tr>
                    <tr>
                        <td>订单类型</td>
                        <td>
                            @if($data->order_type == 1)
                                共享订单
                                @else
                                普通订单
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>订单创建时间</td>
                        <td>{{ date('Y-m-d H:i:s',$data->create_time) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tab-item">

                <table class="layui-table" lay-skin="line">
                    <colgroup>
                        <col width="150">
                        <col width="150">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>借款人</th>
                        <th>{{ $userInfo['name'] or ""}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>借款人身份证</td>
                        <td>{{ $userInfo['card_no'] or "" }}</td>
                    </tr>
                    <tr>
                        <td>借款人联系方式</td>
                        <td>
                            {{ $userInfo['phone'] or "" }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tab-item">
                <table class="layui-table" lay-skin="line">
                    <colgroup>
                        <col width="150">
                        <col width="150">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>产品编号</th>
                        <th>{{ $product->content->pNumber or ""}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>产品地区</td>
                        <td>{{ $product->content->area or "" }}</td>
                    </tr>
                    <tr>
                        <td>借款额度</td>
                        <td>
                            {{ $product->content->money or ""  }}
                        </td>
                    </tr>
                    <tr>
                        <td>还款方式</td>
                        <td>
                            {{ $product->content->lending_type or ""  }}
                        </td>
                    </tr>
                    <tr>
                        <td>利息</td>
                        <td>
                            {{ $product->content->accrual or ""  }}
                        </td>
                    </tr>
                    <tr>
                        <td>产品周期</td>
                        <td>
                            {{ $product->content->product_cycle or ""  }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            {{--<div class="layui-tab-item layui-show">--}}
                {{--<table class="layui-table" lay-skin="line">--}}
                    {{--<colgroup>--}}
                        {{--<col width="150">--}}
                        {{--<col width="150">--}}
                        {{--<col>--}}
                    {{--</colgroup>--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>借款金额(万元)</th>--}}
                        {{--<th>{{ $ApplyUser->need_data->money }}</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                    {{--<tr>--}}
                        {{--<td>借款周期</td>--}}
                        {{--<td>{{ $ApplyUser->need_data->product_cycle }}</td>--}}
                    {{--</tr>--}}
                    {{--<tr>--}}
                        {{--<td>利息</td>--}}
                        {{--<td>--}}
                            {{--{{ $ApplyUser->need_data->accrual    }}--}}
                        {{--</td>--}}
                    {{--</tr>--}}
                    {{--<tr>--}}
                        {{--<td>还款方式</td>--}}
                        {{--<td>{{ $ApplyUser->need_data->lending_type  }}</td>--}}
                    {{--</tr>--}}
                    {{--</tbody>--}}
                {{--</table>--}}
            {{--</div>--}}
            {{--<div class="layui-tab-item layui-show">--}}
                {{--<table class="layui-table" lay-skin="line">--}}
                    {{--<colgroup>--}}
                        {{--<col width="150">--}}
                        {{--<col width="150">--}}
                        {{--<col>--}}
                    {{--</colgroup>--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>订单号</th>--}}
                        {{--<th>{{ $data->order_id }}</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                    {{--<tr>--}}
                        {{--<td>订单总额(万元)</td>--}}
                        {{--<td>{{ $data->order_count }}</td>--}}
                    {{--</tr>--}}
                    {{--<tr>--}}
                        {{--<td>订单类型</td>--}}
                        {{--<td>--}}
                            {{--@if($data->order_type == 1)--}}
                                {{--共享订单--}}
                            {{--@else--}}
                                {{--普通订单--}}
                            {{--@endif--}}
                        {{--</td>--}}
                    {{--</tr>--}}
                    {{--<tr>--}}
                        {{--<td>订单创建时间</td>--}}
                        {{--<td>{{ date('Y-m-d H:i:s',$data->create_time) }}</td>--}}
                    {{--</tr>--}}
                    {{--</tbody>--}}
                {{--</table>--}}
            {{--</div>--}}
        </div>
    </div>
</body>

<script src="//cdn.bootcss.com/jquery/2.0.2/jquery.min.js"></script>
<script src="//cdn.bootcss.com/echarts/3.0.0/echarts.min.js"></script>
<script src="https://cdn.bootcss.com/layer/3.0.3/layer.js"></script>
<script src="{{ URL::asset('layui/layui.js') }}"></script>
<script src="{{ URL::asset('layui/layui.all.js') }}"></script>
<script type="text/javascript">

</script>
</body>
</html>