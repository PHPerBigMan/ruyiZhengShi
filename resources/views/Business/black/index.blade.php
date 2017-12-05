@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed layui-this" id="1">
                        <a class="" href="javascript:;" @click="change(1)">黑名单录入</a>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed" id="2">
                        <a class="" href="javascript:;" @click="change(2)">黑名单查询</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
              <span class="layui-breadcrumb">
              <a><cite>如易黑名单</cite></a>
              <a><cite v-text="cat_name"></cite></a>
              </span>
            </div>
            <hr>
            <div class="form-data" v-show="show == 1">
                <div class="demoTable product_search">
                    搜索ID：
                    <div class="layui-inline">
                        <input class="layui-input" name="id" id="demoReload" autocomplete="off">
                    </div>
                    <button class="layui-btn" data-type="search">搜索</button>
                </div>
                <div class="black-btns" lay-filter="add">
                    <button class="layui-btn layui-btn-radius" @click="add">录入</button>
                    <button class="layui-btn layui-btn-radius" @click="edit">修改</button>
                    <button class="layui-btn layui-btn-radius" @click="del">删除</button>
                </div>
                <table id="blackData"  lay-filter="edit"></table>
            </div>


            <div class="form-data" v-show="show == 2">

                <div class="black-btns" lay-filter="add">
                    <button class="layui-btn layui-btn-radius" @click="search">查询</button>
                </div>
                <table id="search-table"  lay-filter="edit"></table>
            </div>

            <div class="search-alert">
                <form class="layui-form" action="" id="black-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">姓名:</label>
                        <div class="layui-input-block">
                            <input type="text" name="name"  autocomplete="off" placeholder="请输入标题" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">手机:</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_phone"  placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证:</label>
                        <div class="layui-input-block">
                            <input type="text" name="user_no"  placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <span class="layui-btn black-btn"  @click="searchData">查询</span>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="shenming">
                            @if(empty($data))
                                <span>暂无声明</span>
                                @else
                                <span style="overflow: hidden">{{ $data->content }}</span>
                                @endif
                            <span></span>
                        </div>
                    </div>
                </form>
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
                    layer_show('黑名单查看','/business/blackAdd/'+obj.data.id+'/1','880','460')
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
                cat_name:'黑名单录入',
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
                            app.cat_name = "黑名单录入";
                            break;
                        default:
                            app.cat_name = "黑名单查询";
                            break;
                    }
                    app.show = id;
                },
                add:function () {
                    layer_show('黑名单录入','/business/blackAdd/0/0','880','460',1)
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
                        layer_show('查看','/business/blackAdd/'+black_id[0]+'/0','880','460',1)
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
                        area: ['893px', '460'],
                        content: $('.search-alert'),
                        end:function () {
                            $('.search-alert').css({'display':'none'});
                        }
                    });
                },
                searchData:function () {
                    $.post('/business/blackSearch',$('#black-form').serialize(),function (obj) {
                        app.tableData = obj;
                        var cols = [[
                            {checkbox: true}
                            ,{field:'id', title: 'ID', width:40}
                            ,{field:'name', title: '姓名', width:180}
                            ,{field:'user_phone', title: '手机', width:240 }
                            ,{field:'user_no', title: '身份证号', width:255}
                            ,{field:'money', title: '逾期金额', width:160}
                            ,{field:'companyName', title: '发生机构', width:258}
                            ,{fixed: 'right',title: '查看详情', width:225, align:'center', toolbar: '#read'}
                        ]];
                        UsetableData('#search-table',cols,1420)
                    });
                    layer.closeAll()
                }
            },
            beforeCreate:function () {
                $.post('/business/blackList',function (data) {
                    app.tableData = data;
                    var cols = [[
                        {checkbox: true}
                        ,{field:'id', title: 'ID', width:40}
                        ,{field:'name', title: '姓名', width:180}
                        ,{field:'phone', title: '手机', width:240 }
                        ,{field:'cardNo', title: '身份证号', width:160}
                        ,{field:'money', title: '逾期金额', width:160}
                        ,{field:'time', title: '有效期', width:200}
                        ,{field:'status', title: '状态', width:208}
                        ,{fixed: 'right',title: '查看详情', width:205, align:'center', toolbar: '#read'}
                    ]];
                    UsetableData('#blackData',cols,1420)
                });

            }
        });


        //数据表格



    </script>
@endsection
