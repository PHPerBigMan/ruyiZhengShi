@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="用户名或手机号">
            </div>
            <button class="layui-btn" data-type="reload">搜索</button>
        </form>
    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="50">
                <col width="150">
                <col width="150">
                <col width="80">
                <col width="50">
                <col width=80">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>企业编号</th>
                <th>企业名称</th>
                <th>企业代码</th>
                <th>企业法人</th>
                <th>法人联系电话</th>
                <th>是否通过审核</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->number }}</td>
                    <td>
                        {{ $value->companyName }}
                    </td>
                    <td>{{ $value->companyCode }}</td>
                    <td>{{ $value->companyLegal }}</td>
                    <td>{{ $value->phone }}</td>
                    <td id="{{ $value->id }}">{{ $value->is_pass == 0 ? "未通过" :$value->is_pass == 1 ? "已通过" : "审核中" }}</td>
                    <td>{{ $value->create_time }}</td>
                    <td>
                        <a href="{{ route('company.detail', ['id' => $value->id]) }}" class="layui-btn layui-btn-small">查看用户信息</a>
                        <button class="layui-btn layui-btn-small" onclick="is_pass({{ $value->id }},1)">通过审核</button>
                        <button class="layui-btn layui-btn-small layui-btn-danger" onclick="is_pass({{ $value->id }},0)">审核不通过</button>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $data->links() }}
    </div>

@endsection

@section('js')
    <script>
        function sCat(id) {

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
    </script>
@endsection