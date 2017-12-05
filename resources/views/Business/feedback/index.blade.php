@extends('Business.index')

@section('laybar')
<div id="feedback">
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <li class="layui-nav-item layui-nav-itemed">
                    <a class="layui-this">意见反馈</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="layui-body">
        <div class="b-nav-bar">
               <span class="layui-breadcrumb">
                   <a><cite>意见反馈</cite></a>
               </span>
        </div>
        <div class="admin-btn">
            <button class="layui-btn layui-btn-radius"  @click="add">添加</button>
        </div>

        <div class="feedback-list">
            <table class="layui-table feedback-table">
                <colgroup>
                    <col width="150">
                    <col width="20">
                </colgroup>
                <tbody>
                @foreach($data as $v)
                <tr>
                    <td>{{ $v->content }}</td>
                    <td>{{ $v->create_time }}</td>
                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        //Vue
        var app = new Vue({
            el: '#feedback',
            data: {
            },
            methods:{
                add:function () {
                    layer.prompt({title: '输入您的反馈，并确认', formType: 2}, function(content, index){
                        layer.close(index);
                        $.post('/business/FeedbackSave',{content:content},function (obj) {
                            layer.msg(obj.msg,{icon:1});
                            setTimeout(function () {
                                location.reload();
                            },1000);
                        })
                    });
                },
            },
        });
    </script>
@endsection
