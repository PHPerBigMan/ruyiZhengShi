@extends('Back.index')
@section('main')

    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col width="250">
                <col width="250">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>内容</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->id }}</td>
                    <td>
                        服务费比列
                    </td>
                    <td id="{{ $value->id }}">{{ $value->rate }}</td>
                    <td>
                        <button class="layui-btn layui-btn-small" onclick="edit({{ $value->id }},{{ $value->rate }})">修改</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('js')
    <script>
        function sCat(id) {

        }

        /**
         * 修改数据
         * @param id
         * @param data
         */

        function edit(id,data) {
            icon = 2;
            layer.prompt({title: '修改数据', formType: 3,value:data}, function(value, index){
                $.post('/back/setting/Sedit',{value:value,id:id},function (obj) {
                    if(obj.code == 200){
                        $('#'+id).text(value);
                        icon = 1;
                        setTimeout(function () {
                            location.reload();
                        },1000);
                    }
                    layer.msg(obj.msg,{icon:icon});
                });
                layer.close(index);
            });
        }

    </script>
@endsection