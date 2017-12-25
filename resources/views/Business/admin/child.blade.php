@extends('Business.index')

@section('laybar')
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <li class="layui-nav-item layui-nav-itemed">
                    <a href="admin"  id="1">公司资料</a>
                </li>
                <li class="layui-nav-item layui-nav-itemed ">
                    {{--<a class="" @click="change(2)" id="2">账户余额</a>--}}
                </li>
                <li class="layui-nav-item layui-nav-itemed">
                    <a class="layui-this" href="ChildList">子账号管理</a>
                </li>
            </ul>
        </div>
    </div>
    <div style="margin-left:300px">
        <div class="demoTable productCat" >
            <div class="layui-btn-group">
                <button class="layui-btn layui-btn-primary layui-btn-sm addNew"><i class="layui-icon"></i>新增子账号</button>
            </div>
        </div>
        <div class="layui-form table-data">
            <table class="layui-table">
                <colgroup>
                    <col width="300">
                    <col width="300">
                    <col width="300">
                    <col width="350">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>登录账号</th>
                    <th>账号角色</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $value)
                    <tr>
                        <td id="{{ $value->id }}">{{ $value->id }}</td>
                        <td id="{{ $value->id }}">{{ $value->name }}</td>
                        <td id="{{ $value->id }}">
                            子账号
                        </td>
                        <td>
                            <button  class="layui-btn layui-btn-small admin_edit" onclick="edit('{{ $value->name }}','{{ $value->pwd_before }}',{{ $value->id }})">修改登录信息</button>
                            <button  class="layui-btn layui-btn-small layui-btn-danger" onclick="del({{$value->id}})">删除账号</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{--        {{ $data->links() }}--}}
        </div>
    </div>


@endsection
@section('js')
    <script>
        /**
         * 新增子账号
         *
         */
        $('.addNew').click(function () {
            layer.prompt({title: '输入登录账号', formType: 3}, function(admin_name, index){
                layer.close(index);
                layer.prompt({title: '输入登录密码', formType: 3}, function(admin_pwd, index){
                    layer.close(index);
                    layer.confirm('确定新增子账号:'+admin_name+'?', {
                        btn: ['确定','取消'] //按钮
                    }, function(){
                        $.post('add',{name:admin_name,password:admin_pwd},function (obj) {
                            layer.msg(obj.msg);
                            setTimeout(function () {
                                location.reload();
                            },1000);
                        });
                    }, function(){

                    });
                });
            });
        });

        /**
         * 编辑
         * @param admin_name
         * @param admin_pwd
         * @param id
         */
        function edit(admin_name,admin_pwd,id) {
            layer.prompt({title: '输入登录账号', formType: 3,value:admin_name}, function(admin_name, index){
                layer.close(index);
                layer.prompt({title: '输入登录密码', formType: 1,value:admin_pwd}, function(admin_pwd, index){
                    layer.close(index);
                    layer.confirm('确定修改:'+admin_name+'账号信息?', {
                        btn: ['确定','取消'] //按钮
                    }, function(){
                        $.post('edit',{name:admin_name,password:admin_pwd,id:id},function (obj) {
                            layer.msg(obj.msg);
                            setTimeout(function () {
                                location.reload();
                            },1000);
                        });
                    }, function(){

                    });
                });
            });
        }

        /**
         * 删除账号
         * @param id
         */

        function del(id) {
            var type = 2;
            layer.confirm('确认删除账号?', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post('ChildDel',{id:id},function (obj) {
                    if(obj.code == 200){
                        type = 1;
                    }
                    layer.msg(obj.msg,{icon:type});
                    setTimeout(function () {
                        location.reload();
                    },1000);
                });
            });
        }
    </script>
@endsection
