@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
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
                    <td id="{{ $value->id }}">{{ $value->admin_name }}</td>
                    <td id="{{ $value->id }}">
                        @if($value->roleid == 1)
                            超级管理员
                            @else
                            子账号
                            @endif
                    </td>
                    <td>
                        <button  class="layui-btn layui-btn-small admin_edit" onclick="edit('{{ $value->admin_name }}','{{ $value->admin_pwd_before }}',{{ $value->id }})">修改登录信息</button>
                        <button  class="layui-btn layui-btn-small layui-btn-danger" onclick="del({{$value->id}})">删除账号</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
{{--        {{ $data->links() }}--}}
    </div>

@endsection

@section('js')
    <script>
       function del(id) {
           var type = 2;
           layer.confirm('确认删除账号?', {
               btn: ['确定','取消'] //按钮
           }, function(){
               $.post('admin/del',{id:id},function (obj) {
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
                       $.post('admin/add',{admin_name:admin_name,admin_pwd:admin_pwd},function (obj) {
                           layer.msg(obj.msg);
                           setTimeout(function () {
                              location.reload();
                           },1000);
                       });
                   }, function(){

                   });
               });
           });
       })

        function edit(admin_name,admin_pwd,id) {
            layer.prompt({title: '输入登录账号', formType: 3,value:admin_name}, function(admin_name, index){
                layer.close(index);
                layer.prompt({title: '输入登录密码', formType: 1,value:admin_pwd}, function(admin_pwd, index){
                    layer.close(index);
                    layer.confirm('确定修改:'+admin_name+'信息?', {
                        btn: ['确定','取消'] //按钮
                    }, function(){
                        $.post('admin/edit',{admin_name:admin_name,admin_pwd_before:admin_pwd,id:id},function (obj) {
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
    </script>
@endsection