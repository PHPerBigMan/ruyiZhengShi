@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <div class="layui-btn-group">
            <button class="layui-btn layui-btn-primary layui-btn-sm addNew"><i class="layui-icon"></i>发送消息</button>
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
                <th>标题</th>
                <th>内容</th>
                <th>图片</th>
                <th>发送对象</th>
                <th>发送信息</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td >{{ $value->id }}</td>
                    <td >{{ $value->title }}</td>
                    <td >{{ $value->content }}</td>
                    <td >
                        <img src="{{ $value->img }}" alt="">
                    </td>
                    <td>
                        @if($value->equipment_type == 1)
                            商户端
                            @else
                            客户端
                            @endif
                    </td>
                    <td >{{ $value->create_time }}</td>
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
           layer_open("发送系统消息","JPushSend",880,660);
       });
    </script>
@endsection