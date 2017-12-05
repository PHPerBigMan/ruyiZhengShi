@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed layui-this" id="1">
                        <a class="" href="javascript:;" @click="change(1)">匹配通知</a>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed" id="2">
                        <a class="" href="javascript:;" @click="change(2)">平台消息通知</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
              <span class="layui-breadcrumb">
              <a><cite>消息</cite></a>
              <a><cite v-text="cat_name"></cite></a>
              </span>
            </div>
            <div class="layui-inline message-time" id="time">
                <span class="label-title">时间：</span>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input create_time" id="create_time" placeholder="yyyy-MM-dd">
                </div>
                <button class="layui-btn" @click="find()">筛选</button>

            </div>
            <hr>
            <div class="message-list" v-show="show == 1">
                <ul>
                    <li v-for="items in data">
                        {{--<a :href="items.id|getMessage"><span v-text="items.title"></span></a>--}}
                        <a ><span v-text="items.title" @click="showMessage(items.id)"></span></a>
                        <span v-text="items.create_time" class="message-create-time"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/html" id="read">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>
    <script>
        layui.use('table', function(){
            var table = layui.table;
            //监听查看按钮事件
            table.on('tool(edit)', function(obj){
                if(obj.event === 'read'){
                    console.log(obj.data.id);
                    layer_show('黑名单录入','/business/blackAdd/'+obj.data.id+'/1','880','720')
                }

            });
        });

    </script>
    <script>
        //Vue
        var app = new Vue({
            el: '#Cat',
            data: {
                CatArray:[],
                cat_name:'匹配通知',
                data:  {!! $data !!},
                show:  1
            },
            methods:{
                change:function (id) {
                    //左侧导航添加样式
                    $("li").removeClass('layui-this');
                    $("#"+id).addClass('layui-this');
                    switch (id){
                        case 1:
                            app.cat_name = "匹配通知";
                            app.show = 1;
                            break;
                        default:
                            app.cat_name = "平台消息通知";
                            app.show = 0;
                            break;
                    }
                    app.show = id;
                },
                showMessage:function (id) {
                    layer_show('消息查看','/business/message/read/' + id,'660','460');
                }
            },
            filters:{
                getMessage:function(val){
//                    return '/business/message/read/' + val
//                    layer_show('消息查看','/business/message/read/' + val,'660','660');
                }
            },
            beforeCreate:function () {

            }
        });
        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //常规用法
            laydate.render({
                elem: '#create_time'
            });
        });

    </script>
@endsection
