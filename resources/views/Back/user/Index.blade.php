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
                <th>ID</th>
                <th>用户头像</th>
                <th>用户名</th>
                <th>手机号</th>
                <th>注册时间</th>
                <th>推荐人ID</th>
                <th>访问次数</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->id }}</td>
                    <td>
                        <img src="{{ $value->user_pic }}" alt="">
                    </td>
                    <td id="{{ $value->id }}">{{ $value->user_name }}</td>
                    <td>{{ $value->phone }}</td>
                    <td>{{ $value->create_time }}</td>
                    <td>{{ empty($value->tuiUserId) ? "非推荐" : $value->tuiUserId}}</td>
                    <td>{{ $value->view_count }}</td>
                    <td>
                        <a href="{{ route('user.detail', ['id' => $value->id]) }}" class="layui-btn layui-btn-small">查看用户信息</a>
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
        /**
         * 修改分类
         * @param id
         * @param cat_name
         */

        function edit(id,cat_name) {
            layer.prompt({title: '修改分类', formType: 3,value:cat_name}, function(value, index){
                $.post('/back/product/CatAdd',{type:0,cat_name:value,id:id},function (obj) {
                    if(obj.code == 200){
                        $('#'+id).text(value);
                    }else{
                        layer.msg("数据未改动");
                    }
                });
                layer.close(index);
            });
        }

        /**
         * 删除分类
         * @param id
         * @param cat_name
         */

        function del(id,cat_name) {
            var type = 2;
            layer.confirm('确定删除分类【'+cat_name+'】？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post('/back/product/cat_del',{id:id},function (obj) {
                    if(obj.code == 200){
                        type = 1;
                        //删除对应的 tr
                    }
                    layer.msg(obj.msg,{icon:2})
                });
            });
        }
    </script>
@endsection