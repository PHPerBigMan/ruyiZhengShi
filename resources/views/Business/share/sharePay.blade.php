@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">产品分类</a>
                        <dl class="layui-nav-child">
                            @foreach($product_cat as $v)
                                <dd><a href="/business/share/{{ $v->id }}/{{ $v->sec_id }}">{{ $v->cat_name }}</a></dd>
                            @endforeach
                        </dl>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">共享订单</a>

                        <dl  class="layui-nav-child">
                            <dd><a href="/business/sharePay" class="layui-this">订单列表</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <div class="form-data">
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>产品分类</th>
                        <th>借款金额(万元)</th>
                        <th>下单时间</th>
                        <th>操作</th>
                    </tr>
                    @foreach($data as $v)
                        <tr>
                            <th>{{ $v->order_id }}</th>
                            <th>{{ $v->cat_name }}</th>
                            <th>{{ $v->data->money }}</th>
                            <th>{{ date('Y-m-d',$v->create_time) }}</th>
                            <th>
                                @if($v->c_apply_status == 0)
                                    <button class="layui-btn layui-btn-primary layui-btn-small" onclick="pay({{ $v->id }},0)">支付</button>
                                    @elseif($v->c_apply_status == 1)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">支付审核中</button>
                                @elseif($v->c_apply_status == 2)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">已取消</button>
                                @elseif($v->c_apply_status == 4)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">等待确认</button>
                                @elseif($v->c_apply_status == 5)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">退款中</button>
                                @elseif($v->c_apply_status == 6)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">已退款</button>
                                @elseif($v->c_apply_status == 7)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">等待放款</button>
                                @elseif($v->c_apply_status == 8)
                                    <button class="layui-btn layui-btn-primary layui-btn-small">放款成功</button>
                                @elseif($v->c_apply_status == 9)
                                    <button class="layui-btn layui-btn-danger layui-btn-small">审核未通过</button>
                                    @endif
                            </th>
                        </tr>
                        @endforeach
                    </thead>
                    <tbody class="share-tbody">

                    </tbody>
                </table>


            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('js/success.js') }}?_v=17"></script>
@endsection
