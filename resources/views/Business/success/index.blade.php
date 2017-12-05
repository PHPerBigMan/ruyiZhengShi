@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">待支付</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(0)" id="0" class="layui-this">个人</a></dd>
                            <dd><a href="javascript:;" @click="change(1)" id="1">共享</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;">已支付</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(2)" id="2">个人</a></dd>
                            <dd><a href="javascript:;" @click="change(3)" id="3">共享</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;">已放款</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(4)" id="4">个人</a></dd>
                            <dd><a href="javascript:;" @click="change(5)" id="5">共享</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
                  <span class="layui-breadcrumb">
                  <a><cite>成功匹配</cite></a>
                  <a><cite v-text="title"></cite></a>
                  <a><cite v-text="little_title"></cite></a>
                  </span>
            </div>
            <hr>

            <div class="form-data">
                <div class="demoTable product_search">
                    搜索ID：
                    <div class="layui-inline">
                        <input class="layui-input" name="id" id="demoReload" autocomplete="off">
                    </div>
                    <button class="layui-btn" data-type="search">搜索</button>
                </div>
                <div class="eva-select">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <select name="cat_id" class="cat">
                                <option value="">请选择分类</option>
                                @foreach($data as $vo)
                                    <option value="{{ $vo->id }}">{{ $vo->cat_name }}</option>
                                    @endforeach
                            </select>
                        </div>
                    <button class="layui-btn" @click="find()">筛选</button>
                </div>

            </div>
                <table id="applyData"  lay-filter="edit"></table>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/html" id="noPay">
        {{--<a class="layui-btn layui-btn-mini" lay-event="pay">&nbsp; 支付 &nbsp;</a>--}}
        {{--<a class="layui-btn layui-btn-mini" lay-event="canl">&nbsp; 取消 &nbsp;</a>--}}

    </script>

    <script type="text/html" id="fk">
        <a class="layui-btn layui-btn-mini" lay-event="Fpay">&nbsp; 放款 &nbsp;</a>
        <a class="layui-btn layui-btn-mini" lay-event="Refulse">&nbsp; 拒绝 &nbsp;</a>
    </script>
    <script type="text/html" id="end">
        <a class="layui-btn layui-btn-mini" lay-event="over">&nbsp; 完结 &nbsp;</a>
    </script>
    <script type="text/html" id="readMore">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>
    <script>
        layui.use('table', function(){
            var table = layui.table;
            //监听查看按钮事件

            table.on('tool(edit)', function(obj){
                //支付
                if(obj.event === 'pay'){
                    layer.msg('支付')
                }
                //取消
                if(obj.event === 'canl'){
                    layer.msg('取消')
                }
                //放款
                if(obj.event === 'Fpay'){
                    layer.msg('放款')
                }
                //拒绝
                if(obj.event === 'Refulse'){
                    layer.msg('拒绝')
                }
                //完结
                if(obj.event === 'over'){
                    layer.msg('完结')
                }
                //查看
                if(obj.event === 'read'){
                    layer.msg('查看')
                }
            });
        });

    </script>
    <script>

        //Vue
        var app = new Vue({
            el: '#Cat',
            data: {
                tableData:  [],
                title : "待支付",
                little_title : "个人",
                apply_type:0
            },
            methods:{
                change:function (type) {
                    //左侧导航添加样式
                    $("a").removeClass('layui-this');
                    $("#"+type).addClass('layui-this');
                    app.apply_type = type;
                    var cols = "";

                    //更改面包屑
                    switch (type){
                        case 0:
                            app.title = "待支付";
                            app.little_title = "个人";
                            break;
                        case 1:
                            app.title = "待支付";
                            app.little_title = "共享";
                            break;
                        case 2:
                            app.title = "已支付";
                            app.little_title = "个人";
                            break;
                        case 3:
                            app.title = "已支付";
                            app.little_title = "共享";
                            break;
                        case 4:
                            app.title = "已放款";
                            app.little_title = "个人";
                            break;
                        default:
                            app.title = "已放款";
                            app.little_title = "共享";
                    }
                    //获取数据
                    $.post('/business/successList',{type:type,search:0},function (data) {
                        app.tableData = data;

                        tableData(cols)
                    });

                },
                find:function () {
                    //申请类型
                    var apply_type= app.apply_type;
                    //分类id
                    var cat_id = $('.cat').val();
                    $.post('/business/successList',{search:1,type:apply_type,cat_id:cat_id},function (data) {
                        app.tableData = data;

                        tableData(cols)
                    });
                }
            },
            beforeCreate:function () {
                $.post('/business/successList',{type:0,search:0},function (data) {
                    app.tableData = data;

                    tableData(cols)
                });
            }
        });

        //数据表格
        function tableData() {
            layui.use('table', function(){
                var table = layui.table;
                //展示已知数据
                var tableIns = table.render({
                    elem: '#applyData'
                    ,data: app.tableData
                    ,height: 472
                    ,cols: [[ //标题栏
                        {checkbox: true}
                        ,{field:'id', title: '订单号', width:150}
                        ,{field:'orderType', title: '类型', width:100}
                        ,{field:'name', title: '姓名', width:140 }
                        ,{field:'phone', title: '电话', width:160}
                        ,{field:'money', title: '金额', width:150}
                        ,{field:'cat', title: '分类', width:150}
                        ,{field:'read', title: '详情', width:128}
                        ,{field:'caozuo', title: '操作', width:308}
                    ]]
                    ,skin: 'row' //表格风格
                    ,even: true
                    ,page: true //是否显示分页
                    ,limits: [5, 7, 10]
                    ,limit: 10 //每页默认显示的数量
                });
                var $ = layui.$, active = {
                    search:function () {
                        var demoReload = $('#demoReload');
                        tableIns.reload(
                            {
                                where: {
                                    key: {
                                        id: demoReload.val()
                                    }
                                }
                            }
                        )
                    }
                };

                $('.layui-btn').on('click', function(){
                    var type = $(this).data('type');
                    active[type] ? active[type].call(this) : '';
                });
            });
        }

        //左侧导航点击
        $('li').bind('click',function(){
            $('li').removeClass('layui-nav-itemed');
            $(this).addClass('layui-nav-itemed')
        });


        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //常规用法
            laydate.render({
                elem: '#create_time'
            });
        });

        /**
         * 查看订单详情
         * @param id
         */

        function read(id) {
            layer.msg('');
        }

        /**
         * 支付订单
         * @param id
         * @param type
         */
        function pay(id,type) {
            layer.msg('订单支付')
        }

        /**
         * 取消订单
         * @param id
         */


    </script>


@endsection
