@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        {{--<form action="">--}}
            {{--<div class="layui-inline">--}}
                {{--<input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="订单号">--}}
            {{--</div>--}}
            {{--<button class="layui-btn" data-type="reload">搜索</button>--}}
        {{--</form>--}}
    </div>
    <div class="layui-form table-data" style="min-width: 800px">
        <table class="layui-table">
            <thead>
            <tr>
                <th>序号</th>
                <th>公司</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr id="{{ $value->id }}">
                    <td>{{ $value->id }}</td>
                    <td>
                        {{ $value->business->companyName or '' }}
                    </td>
                    <td>{{ $value->status_info }}</td>
                    <td>{{ $value->create_time }}</td>
                    <td>
                        <a class="layui-btn layui-btn-small" href="/back/product/check/{{ $value->id }}">查看详情</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div>
            {{ $data->links() }}
        </div>

    </div>

@endsection

@section('js')
    <script>
        function read(id) {
            layer_open('订单详情');
        }

        /**
         * 用户是否通过审核
         * @param id
         * @param type
         */
        var message = "";
        var pass = "";

        function is_pass(id,type,apply_type,order_id) {
            var icon = 2;
            if(type == 0){
                message = "确定将该订单号"+order_id+"的订单状态修改为"+'<span style="color:red">不通过</span>'+"？";
                pass = "未通过";
            }else{
                message = "确定将订单号"+order_id+"的订单状态修改为"+'<span style="color:red">已通过</span>'+"？";
                pass = "已通过";
            }

            layer.confirm(message, {
                btn: ['确定','取消'] //按钮
                ,title:"状态修改"
            }, function(){
                $.post('/back/order/orderChange',{id:id,type:type,apply_type:apply_type},function (obj) {

                    if(obj.code == 200){
                        icon = 1;
                        $('#'+id).remove();
                    }
                    layer.msg(obj.msg,{icon:icon});
                })
            });
        }
    </script>
@endsection