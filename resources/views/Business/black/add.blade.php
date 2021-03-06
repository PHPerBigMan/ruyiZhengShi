@extends('Business.header')
@section('content')
    <form class="layui-form" action="" id="black-form">
        <div class="layui-form-item">
            <label class="layui-form-label">姓名:</label>
            <div class="layui-input-block">
                <input type="text" name="name"  autocomplete="off" placeholder="请输入标题" class="layui-input" value="{{ $data->name }}" lay-verify="title">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">手机:</label>
            <div class="layui-input-block">
                <input type="text" name="user_phone"  placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $data->user_phone }}" lay-verify="title">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">身份证号:</label>
            <div class="layui-input-block">
                <input type="text" name="user_no"  placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $data->user_no }}" >
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">详情:</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" class="layui-textarea" name="content">
                    {{ $data->content }}
                </textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">逾期金额:</label>
            <div class="layui-input-block">
                <input type="text" name="money"  placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $data->money }}" lay-verify="title">
            </div>
        </div>
        <div class="layui-upload" id="layui-upload-black">
            <label class="layui-form-label">凭证:</label>

            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                @if($type == 0)
                <button type="button" class="layui-btn" id="add-img">添加图片</button>
                <div class="layui-upload-list" id="black-img"></div>
                @endif
                @if(!empty($data->imgs))
                    <div class="layui-upload-list" id="black-img">
                    @foreach($data->imgs as $vo)
                            <img src="{{ $vo }}" alt="" class="black-img">
                    @endforeach
                    </div>
                    @endif
            </blockquote>

        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" id="imgs" name="imgs">
                <input type="hidden"  name="id" value="{{ $data->id }}">
                @if($type == 0)
                <button class="layui-btn black-btn" lay-submit="" lay-filter="go">确认录入</button>
                    @endif
            </div>
        </div>
    </form>
    @endsection
@section('js')
    <script>
        //图片点击放大
        $('.black-img').zoomify();

        layui.use('upload', function(){
            var img = [];
            var $ = layui.jquery
                ,upload = layui.upload;
            //多图片上传
            upload.render({
                elem: '#add-img'
                ,url: '/business/blackImg'
                ,multiple: true
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('#black-img').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img black-img">')
                    });
                }
                ,done: function(res){
                    //将返回的数据追加到数组中
                    img.push(res.data);
                    //上传完毕
                    layer.msg('图片上传成功 请点击保存！');
                    $('#imgs').val(img);
                }
            });
        });

        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                ,layer = layui.layer
            form.verify({
                title: function(value){
                    if(value.length < 5){
                        return '不能为空';
                    }
                }
            });
            //监听提交
            form.on('submit(go)', function(data){
                $.post('/business/blackSave',$('#black-form').serialize(),function (obj) {
                    if(obj.code == 200){
                        layer.msg(obj.msg,{icon:1});
                    }else{
                        layer.msg(obj.msg,{icon:2})
                    }
                });
                return false;
            });

        });


    </script>
    @endsection