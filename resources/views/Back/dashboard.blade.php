@extends('Back.index')
@section('main')
    <style>
        .main-content{
            width: 80%;
        }
        .v-card{
            margin: 0 15px;
            border: 1px solid #dddee1;
            background: #fff;
            border-radius: 4px;
            font-size: 14px;
            position: relative;
            transition: all .2s ease-in-out;
            padding: 15px 30px;
            text-align: center;
            border-left: 5px solid #009688;
        }
        .v-card p span{
            font-size: 30px;
            color: rgb(45, 140, 240);
        }
    </style>
    <div class="layui-row">
        <div class="layui-col-xs3">
            <div class="grid-demo grid-demo-bg1">
                <div class="v-card">
                    <p>
                        <span>{{ $user_count }}</span>
                    </p>
                    <p>C端用户数</p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo grid-demo-bg1">
                <div class="v-card">
                    <p>
                        <span>{{ $business_count }}</span>
                    </p>
                    <p>B端用户入驻总数</p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo">
                <div class="v-card">
                    <p>
                        <span>{{ $order->orders_count }}</span>
                    </p>
                    <p>订单总数</p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo">
                <div class="v-card">
                    <p>
                        <span>{{ $order->total_money }}</span>
                    </p>
                    <p>成交总金额（万元）</p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo"></div>
        </div>
    </div>
    <div class="layui-row" style="margin-top: 50px">
        <h1 style="margin-left: 25px">成交金额榜</h1>
        <div class="layui-form table-data">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>排名</th>
                    <th>企业编号</th>
                    <th>公司名</th>
                    <th>成交额(万元)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tops as $value)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $value->number }}</td>
                        <td>{{ $value->companyName }}</td>
                        <td>{{ $value->total_money }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="layui-row" style="margin-top: 50px">
        <h1 style="margin-left: 25px">产品成交榜</h1>
        <div class="layui-form table-data">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="150">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>排名</th>
                    <th>企业编号</th>
                    <th>公司名</th>
                    <th>产品编号</th>
                    <th>产品分类</th>
                    <th>成交数量</th>
                    <th>成交额(万元)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ProductTops as $value)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $value->number }}</td>
                        <td>{{ $value->companyName }}</td>
                        <td><?php $pNumber = json_decode($value->content);echo $pNumber->pNumber;?></td>
                        <td>{{ $value->cat_name }}</td>
                        <td>{{ $value->pCount }}</td>
                        <td>{{ $value->total_money }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $ProductTops->links() }}
        </div>
    </div>
@endsection