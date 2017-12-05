@extends('Back.index')
@section('main')
    <div class="demoTable productCat">

    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="100">
                <col width="150">
                <col width="350">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>提交企业编号</th>
                <th>黑名单用户姓名</th>
                <th>黑名单用户手机号</th>
                <th>黑名单用户身份证号</th>
                <th>逾期金额</th>
                <th>提交时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr id="{{ $value->id }}">
                    <td>{{ $value->id }}</td>
                    <td>
                       {{ $value->number }}
                    </td>
                    <td id="{{ $value->id }}">{{ $value->name }}</td>
                    <td>{{ $value->user_phone }}</td>
                    <td>{{ $value->user_no  }}</td>
                    <td>{{ $value->money }}</td>

                    <td>{{ $value->create_time }}</td>
                    <td>
                        @if($type != 3)
                        <button class="layui-btn layui-btn-small" onclick="pass({{ $value->id }},7)">审核通过</button>
                        <button class="layui-btn layui-btn-small layui-btn-danger" onclick="pass({{ $value->id }},6)">审核不通过</button>
                            @endif
                            {{--<button class="layui-btn layui-btn-small" onclick="show({{ $value->id }})">查看</button>--}}
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
         *
         * @param id
         * @param cat_name
         */

        function pass(id,status) {
            var type = 2;
            var title = "";
            if(status == 6){
                title = "确定修改为不通过？";
            }else{
                title = "确定修改为已通过？";
            }
            layer.confirm(title, {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post('/back/black/change',{id:id,status:status},function (obj) {
                    if(obj.code == 200){
                        type = 1;
                        //删除对应的 tr
                        $('#'+id).remove();
                    }
                    layer.msg(obj.msg,{icon:type})
                });
            });
        }
    </script>
@endsection