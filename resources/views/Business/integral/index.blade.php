@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed layui-this" id="1">
                        <a class="" href="javascript:;" @click="change(1)">我的金币</a>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed" id="2">
                        <a class="" href="javascript:;" @click="change(2)">金币兑换</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
              <span class="layui-breadcrumb">
              <a><cite>如易金币</cite></a>
              <a><cite v-text="cat_name"></cite></a>
              </span>
            </div>
            <hr>
            <div class="integral">
                <span>金币总额:</span>
                <span>{{ $data }}</span>
            </div>
            <div class="form-data" v-show="show == 1">
                <table id="integralData"  lay-filter="edit"></table>
            </div>

            <div class="form-data" v-show="show == 2">
                asdasdasdasd
                <table id="integralChange"  lay-filter="edit"></table>
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
                cat_name:'我的金币',
                tableData:  [],
                show:1
            },
            methods:{
                change:function (id) {
                    //左侧导航添加样式
                    $("li").removeClass('layui-this');
                    $("#"+id).addClass('layui-this');
                    switch (id){
                        case 1:
                            app.cat_name = "我的金币";
                            break;
                        default:
                            app.cat_name = "金币兑换";
                            break;
                    }
                    app.show = id;
                },
                add:function () {
                    layer_show('黑名单录入','/business/blackAdd/0/0','880','720',1)
                },
                edit:function () {
                    var black_id = ID();
                    var len = black_id.length;
                    console.log(black_id);
                    if(len == 0){
                        layer.msg('请选择一条需要修改的数据',{icon:2});
                    }else if(len > 1){
                        layer.msg('只能选择一条数据进行修改',{icon:2});
                    }else{
                        layer_show('查看','/business/blackAdd/'+black_id[0]+'/0','880','720',1)
                    }
                },
                del:function () {
                    layer.confirm('确认删除？', {
                        btn: ['确认','取消'] //按钮
                    }, function(){
                        var black_id = ID();
                        var len = black_id.length;
                        if(len == 0){
                            layer.msg('请选择一项删除的数据',{icon:2})
                        }else{
                            $.post('/business/blackDel',{id:black_id},function (obj) {
                                layer.msg(obj.msg);
                                setTimeout(function () {
                                    location.reload();
                                },1000)
                            });
                        }
                    });
                },
                search:function () {
                    layer.open({
                        type: 1,
                        title:false,
                        shadeClose: false,
                        shade: false,
                        area: ['893px', '600px'],
                        content: $('.search-alert'),
                        end:function () {
                            $('.search-alert').css({'display':'none'});
                        }
                    });
                },
                searchData:function () {
                    $.post('/business/blackSearch',$('#black-form').serialize(),function (obj) {
                    });
                    layer.closeAll()
                }
            },
            beforeCreate:function () {
                //获取积分消耗列表
                $.post('/business/integralList',function (data) {
                    app.tableData = data;
                    var cols = [[
                        {field:'id', title: 'ID', width:62}
                        ,{field:'name', title: '事由', width:300}
                        ,{field:'integral', title: '金币数量', width:280 }
                        ,{field:'create_time', title: '时间', width:360}
                        ,{field:'integraling', title: '目前金币', width:360}
                    ]];
                    UsetableData('#integralData',cols,1370)
                });

                //获取可兑换醒目
                $.post('/business/integralChange',function (data) {
                    app.tableData = data;
                    var cols = [[
                        {field:'id', title: 'ID', width:62}
                        ,{field:'name', title: '可兑换项目', width:300}
                        ,{field:'need', title: '所需金币', width:280 }
                        ,{field:'integraling', title: '目前金币', width:360}
                    ]];
                    UsetableData('#integralData',cols,1370)
                });
            }
        });


        //数据表格



    </script>
@endsection
