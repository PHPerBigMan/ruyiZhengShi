@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <select name="type" id="select-type" class="order-select">
                <option value="1" <?php if($searchType == 1)echo "selected";?>>订单号</option>
                <option value="2" <?php if($searchType == 2)echo "selected";?>>地区</option>
                <option value="3" <?php if($searchType == 3)echo "selected";?>>产品分类</option>
            </select>
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="" value="{{ $keyword }}">
            </div>

            <div class="layui-inline">
                <label class="layui-form-label">时间：</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="test10" placeholder=" - " style="width: 300px" name="exTime" value="{{$time}}">
                </div>
            </div>
            <button class="layui-btn" data-type="reload">搜索</button>
        </form>

        <button  onclick="excel()" class="layui-btn">导出订单</button>
    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="100">
                <col width="150">
                <col width="100">
                <col width="80">
                <col width="150">
                <col width=80">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>订单号</th>
                <th>产品编号</th>
                <th>订单总额</th>
                <th>订单类型</th>
                <th>下单用户</th>
                <th>产品所属企业</th>
                <th>产品所属企业编号</th>
                <th>产品分类</th>
                @if($type ==4)
                    <th>订单状态</th>
                @endif
                @if($type != 8)
                    <th>支付凭证</th>
                @endif
                @if($type == 8)
                    <th>客户端支付凭证</th>
                    <th>商户端支付凭证</th>
                    <th>客户端服务费</th>
                    <th>商户端服务费</th>
                    @endif
                <th>创建时间</th>
                @if($type == 7)
                    <th>退款状态</th>
                    <th>操作</th>
                    @endif
                    @if($type <= 4)
                        <th>操作</th>
                    @endif

            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr id="{{ $value->id }}">
                    <td onclick="readMore({{ $value->id }})" style="cursor: pointer">{{ $value->order_id }}</td>
                    <td>
                        {{ $value->pNumber }}
                    </td>
                    <td>{{ $value->order_count }}</td>
                    <td>{{ $value->order_type == 0 ? "普通订单" : "共享订单" }}</td>
                    <td>{{ $value->userName }}</td>
                    <td>{{ $value->companyName }}</td>
                    <td>{{ $value->number }}</td>
                    <td>{{ $value->cat_name }}</td>
                    @if($type ==4)
                    <td class="red-font">
                        @if($value->c_apply_status == 0 || $value->c_apply_status == 1  || $value->c_apply_status == 3 || $value->c_apply_status == 4)
                            未完成
                            @elseif($value->c_apply_status == 2)
                            客户端用户已取消
                            @elseif($value->c_apply_status == 5)
                            退款中
                            @elseif($value->c_apply_status == 6)
                            已退款
                            @elseif($value->c_apply_status == 7)
                            等待放款
                            @elseif($value->c_apply_status == 8)
                            放款成功
                            @elseif($value->c_apply_status == 9)
                            C端支付驳回
                            @elseif($value->c_apply_status == 10 )
                            总后台取消订单
                            @endif
                    </td>
                    @endif
                    @if($type != 8)
                    <td>
                        @if($type == 0 || $type ==1 || $type == 4)
                        <img src="{{ !empty($value->img) ? $value->img :'' }}" alt="" class="img">
                            @endif
                    </td>
                    @endif
                    @if($type == 8)
                        <td>
                            <img src="{{ !empty($value->CPayimg) ? $value->CPayimg[0] :'' }}" alt="" class="img">
                        </td>
                        <td>
                            <img src="{{ !empty($value->BPayimg) ? $value->BPayimg[0] :'' }}" alt="" class="img">
                        </td>
                        <td>
                            {{ $value->c_serve }}
                        </td>
                        <td>
                            {{ $value->b_serve }}
                        </td>
                        @endif
                    <td>{{ date("Y-m-d H:i:s",$value->create_time) }}</td>

                    @if($type <= 4)
                    <td>
                        {{--<button class="layui-btn layui-btn-small" onclick="read({{ $value->id }})">查看详情</button>--}}

                        <button class="layui-btn layui-btn-small" onclick="is_pass({{ $value->id }},1,{{ $type }},{{ $value->order_id }})">通过审核</button>
                        <button class="layui-btn layui-btn-small layui-btn-danger" onclick="is_pass({{ $value->id }},0,{{ $type }},{{ $value->order_id }})">审核不通过</button>
                        @if($type == 4)
                            <button class="layui-btn layui-btn-small  layui-btn-danger" onclick="cancel({{ $value->id }})">取消订单</button>
                        @endif
                    </td>
                    @endif
                    @if($type == 7)
                        <td>{{ $value->c_apply_status == 5 ? "退款成功" : "退款中"}}</td>
                        <td>
                            @if($value->c_apply_status == 5)
                                <button class="layui-btn layui-btn-small layui-btn-normal" >已退款</button>
                                @else
                                <button class="layui-btn layui-btn-small" onclick="is_pass({{ $value->id }},2,0,{{ $value->order_id }})">退款</button>
                                @endif

                            <button class="layui-btn layui-btn-small layui-btn-danger" onclick="is_pass({{ $value->id }},3,0,{{ $value->order_id }})">取消退款</button>
                        </td>
                    @endif
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
        $('.img').zoomify();


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
                message = "确定将该订单号"+"<span style='color:red'>"+order_id+"</span>"+"的订单状态修改为"+'<span style="color:red">不通过</span>'+"？";
                pass = "未通过";
            }else if(type == 1){
                message = "确定将订单号"+"<span style='color:red'>"+order_id+"</span>"+"的订单状态修改为"+'<span style="color:red">已通过</span>'+"？";
                pass = "已通过";
            }else if(type == 2){
                message = "确定将订单号"+"<span style='color:red'>"+order_id+"</span>"+"的订单状态修改为"+'<span style="color:red">退款成功</span>'+"？";
                pass = "已退款";
            }else if(type == 3){
                message = "确定驳回订单号"+"<span style='color:red'>"+order_id+"</span>"+"的退款要求?";
                pass = "已取消";
            }

            layer.confirm(message, {
                btn: ['确定','取消'] //按钮
                ,title:"状态修改"
            }, function(){
                $.post('/back/order/orderChange',{id:id,type:type,apply_type:apply_type},function (obj) {

                    if(obj.code == 200){
                        icon = 1;
                        if(type<2){
                            $('#'+id).remove();
                        }else{
                          setTimeout(function () {
                              location.reload()
                          },1000)
                        }
                    }
                    layer.msg(obj.msg,{icon:icon});
                })
            });
        }


        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //日期时间范围
            laydate.render({
                elem: '#test10'
                ,type: 'datetime'
                ,range: true
            });
        });
        
        function excel() {

            // 查询条件类型
            var select_type = $('#select-type option:selected').val();
            // 关键词
            var keyword = $('#demoReload').val();
            // 时间
            var time = $('#test10').val();
            location.href = "/back/excel?type={{ $type }}&exl=order&selectType={{ $searchType }}&keyword="+keyword+"&time="+time;
        }

        function cancel(id) {
            layer.confirm('确认取消该订单？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post('checkOrderStatus',{id:id},function (obj1) {
                    if(obj1.code == 403){
                       layer.confirm('该订单用户端已支付，确认取消？',{
                           btn:['确定','取消']
                       },function () {
                           doIt(id);
                       },function () {
                           layer.msg('取消成功');
                       });
                    }else{
                        doIt(id);
                    }
                });

            });
        }

        function doIt(id) {
            $.post('orderCancel',{id:id},function (obj) {
                layer.closeAll();
                if(obj.code == 200){
                    layer.msg(obj.msg,{type:1});
                    setTimeout(function () {
                        location.reload()
                    },1000);
                }else{
                    layer.msg(obj.msg,{type:2});
                }
            })
        }

        function readMore(id) {
            layer_open("订单详情",'/back/orderRead/readMore/'+id,880,660);
        }
    </script>
@endsection