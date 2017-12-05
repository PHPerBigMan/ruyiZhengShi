@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;">待支付</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/business/success/0" >个人</a></dd>
                            <dd><a href="/business/success/1" >共享</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;">已支付</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/business/success/2">个人</a></dd>
                            <dd><a href="/business/success/3">共享</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">已放款</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/business/success/4">个人</a></dd>
                            <dd><a href="/business/success/5" class="layui-this">共享</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
                  <span class="layui-breadcrumb">
                  <a><cite>成功匹配</cite></a>
                  <a><cite >待支付</cite></a>
                  <a><cite >个人</cite></a>
                  </span>
            </div>
            <hr>
            <div class="layui-form">
                <table class="layui-table" style="width:1366px;margin-left: 20px">

                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>姓名</th>
                        <th>电话</th>
                        <th>金额</th>
                        <th>分类</th>
                        <th>详情</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $v)
                    <tr>
                        <td>{{ $v->order_id }}</td>
                        <td>{{ isset($v->data->name) ?  $v->data->name : $v->data->companyName}}</td>
                        <td>{{ isset($v->data->phone) ?  $v->data->phone : "" }}</td>
                        <td>{{ $v->order_count }}</td>
                        <td>{{ $v->cat_name }}</td>
                        <td>
                            <button class="layui-btn layui-btn-normal layui-btn-small">查看</button>
                        </td>
                        <td>
                            @if($v->b_is_evaluate == 0)
                                <button class="layui-btn layui-btn-small" onclick="pj({{ $v->id }})">评价</button>
                            @else
                                <button class="layui-btn layui-btn-normal layui-btn-small">完结</button>
                            @endif
                        </td>
                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('js/success.js') }}"></script>
@endsection
