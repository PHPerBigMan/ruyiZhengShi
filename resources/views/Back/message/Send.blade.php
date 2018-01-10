<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}"  media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>

<form class="layui-form" action="" style="margin: 10px 0px">
    <div class="layui-form-item">
        <label class="layui-form-label">发送对象</label>
        <div class="layui-input-block">
            <input type="radio" name="type" value="0" title="客户端" checked="">
            <input type="radio" name="type" value="1" title="商户端">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-upload">
            <label class="layui-form-label">消息图片</label>
            <button type="button" class="layui-btn" id="test1">上传图片</button>
            <div class="layui-upload-list">
                <img class="layui-upload-img" id="demo1" width="100" style="margin-left: 105px">
                <input type="hidden" id="img" name="img">
                <p id="demoText"></p>
            </div>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">消息内容</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入内容" class="layui-textarea" lay-verify="content"></textarea>
        </div>
    </div>
    {{--<div class="layui-form-item layui-form-text">--}}
      {{--<label class="layui-form-label">编辑器</label>--}}
      {{--<div class="layui-input-block">--}}
        {{--<textarea class="layui-textarea layui-hide" name="content" lay-verify="content" id="LAY_demo_editor"></textarea>--}}
      {{--</div>--}}
    {{--</div>--}}
    <div class="layui-form-item">
        <div class="layui-input-block">
            <p class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</p>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
<script src="{{ URL::asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<script src="{{ URL::asset('layui/layui.all.js') }}"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;

        //创建一个编辑器
        var editIndex = layedit.build('LAY_demo_editor');

        //自定义验证规则
        form.verify({
            title: function(value){
                if(value.length == "" ){
                    return '请输入标题';
                }
            }
            ,content: function(value){
                if(value.length == ""){
                    return '请输入内容';
                }
            }
        });
        //监听提交
        form.on('submit(demo1)', function(data){
           $.post('send',data.field,function (obj) {
               if(obj == 200){
                   layer.msg("系统消息发送成功");
                   setTimeout(function () {
                       location.reload();
                   },1000)
               }
           });
        });
    });

    layui.use('upload', function(){
        var $ = layui.jquery
            ,upload = layui.upload;

        //普通图片上传
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: '/back/savePic'
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            }
            ,done: function(res){
                //如果上传失败
                if(res.code > 0){
                    return layer.msg('上传失败');
                }
                $('#img').val(res.data);
                //上传成功
            }
            ,error: function(){
                //演示失败状态，并实现重传
                var demoText = $('#demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
            }
        });
    });
</script>

</body>
</html>