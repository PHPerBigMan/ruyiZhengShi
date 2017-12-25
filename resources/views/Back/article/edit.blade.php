@extends('Back.index')
@section('main')
    <form class="layui-form" id="data">
        <div class="layui-form-item">
            <label class="layui-form-label">标题</label>
            <div class="layui-input-block ">
                <input type="text" name="title" value="{{ $data->title }}" autocomplete="off" placeholder="请输入标题" class="layui-input article">
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">内容</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" class="layui-textarea article" style="height: 500px;" name="content">{{ $data->content }}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name="id" value="{{ $data->id }}">
                <p class="layui-btn submit" >立即提交</p>
            </div>
        </div>
@endsection
        @section('js')
            <script>
                $('.submit').click(function () {
                    $.post('saveArticle',$('#data').serialize(),function (obj) {
                        layer.msg(obj.msg);
                        if(obj.code == 200){
                            setTimeout(function () {
                                location.reload();
                            },1500)
                        }
                    })
                })
            </script>
        @endsection