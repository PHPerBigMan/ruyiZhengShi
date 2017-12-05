@extends('Business.header')

@section('content')
    <form class="layui-form" id="childAdd">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">子账号:</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" lay-verify="required" autocomplete="off" class="layui-input" value="@isset($data->name){{ $data->name }}@endisset">
                </div>
            </div>

        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">密码:</label>
                <div class="layui-input-inline">
                    <input type="tel" name="password" lay-verify="required" autocomplete="off" class="layui-input" value="@isset($data->password){{ $data->password }}@endisset">
                </div>
            </div>
        </div>
        <div class="layui-form-item" pane="">
            <label class="layui-form-label">权限:</label>
            <ul class="child-ul">
                @foreach($method as $v)
                <li><input lay-filter="checkbox" type="checkbox" name="method[]" lay-skin="primary" title="{!!  $v->desc !!}" value="{!! $v->id !!}"
                    <?php
                            if(!empty($data)){
                                if(in_array($v->id,$data->method)){
                                    echo "checked=''";
                                }
                            }
                        ?>>
                </li>
                    @endforeach
            </ul>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="save">确认</button>
                <input type="hidden" name="method_id" id="method_id">
                <input type="hidden" name="id" value="{{ $id }}">
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                ,layer = layui.layer

            //监听提交
            form.on('submit(save)', function(){
                $.post('/business/ChildSave',$('#childAdd').serialize(),function (obj) {
                    if(obj.code == 200){
                        layer.msg(obj.msg,{icon:1});
                        setTimeout(function () {
                            location.href = '/business/admin';
                        },1000)
                    }else{
                        layer.msg(obj.msg,{icon:2});
                    }
                });
                return false;
            });
            var array = [];
            form.on('checkbox(checkbox)', function(data){
                //indexOf 数组元素所在的下标
                if(array.indexOf(data.value) != '-1'){
                    $.each(array,function (index,item) {
                        if(item == data.value){
                            array.splice(index,1)
                        }
                    })
                }else{
                    array.push(data.value);
                }
                $('#method_id').val(array);
            });
        });
    </script>
@endsection
