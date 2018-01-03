@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="企业名称">
            </div>
            <button class="layui-btn" data-type="reload">搜索</button>
        </form>
    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="50">
                <col width="150">
                <col width="350">
                <col width="180">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>企业编号</th>
                <th>企业名称</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->number }}</td>
                    <td>
                        {{ $value->companyName }}
                    </td>
                    <td>
                        <a class="layui-btn layui-btn-small" href="product/cat/{{ $value->id }}">查看产品</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $data->appends(['keyword'=>$keyword])->links() }}
    </div>

@endsection

@section('js')
    <script>
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
            var select_type = $('#select-type option:selected').val();
            // 关键词
            var keyword = $('#demoReload').val();

            var time = $('#test10').val();
            location.href = "/back/excel?exl=userB&selectType="+select_type+"&keyword="+keyword+"&time="+time;
//            location.href = "/back/excel?exl=userB";
        }

        /**
         * 用户是否通过审核
         * @param id
         * @param type
         */
        var message = "";
        var pass = "";

        function is_pass(id,type) {
            var icon = 2;
            if(type == 0){
                message = "确定将该用户的状态修改为"+'<span style="color:red">不通过</span>'+"？";
                pass = "未通过";
            }else{
                message = "确定将该用户的状态修改为"+'<span style="color:red">已通过</span>'+"？";
                pass = "已通过";
            }

            layer.confirm(message, {
                btn: ['确定','取消'] //按钮
                ,title:"状态修改"
            }, function(){
                $.post('/back/user/changeStatus',{id:id,is_pass:type},function (obj) {
                    if(obj.code == 200){
                        icon = 1;
                        $('#'+id).text(pass);
                    }
                    layer.msg(obj.msg,{icon:icon});
                });
            });
        }

        /**
         * 为用户增加如易金券
         * @param id
         */
        function add(id) {
            var integral = $('#integral').val();
            var gold = $('#gold').val();
            layer.prompt({title: '输入需要充值的金币数额', formType: 3}, function (count, index) {
                layer.close(index);
                layer.confirm("确定要为该用户增加<span style='color: red'>" + count + "</span>金币", {
                    btn: ['确定', '取消'] //按钮
                }, function (index1) {
                    layer.close(index1);
                    $.post('addInt', {count: count, id: id, type: 2}, function (obj) {
//                        layer.closeAll();
                        layer.msg(obj.msg + "本页面将在2秒后自动刷新！", {time: 2000});
                        if (obj.code == 200) {
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        }
                    });
                });
            });
        }
    </script>
@endsection