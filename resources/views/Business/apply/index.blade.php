@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">今日匹配</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(0)" id="0" class="layui-this">接收</a></dd>
                            <dd><a href="javascript:;" @click="change(1)" id="1">拒绝</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;">历史匹配</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(2)" id="2">接收</a></dd>
                            <dd><a href="javascript:;" @click="change(3)" id="3">拒绝</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a class="" href="javascript:;">已完结</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(4)" id="4">接收</a></dd>
                            <dd><a href="javascript:;" @click="change(5)" id="5">拒绝</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
                  <span class="layui-breadcrumb">
                  <a><cite>匹配列表</cite></a>
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
                        <div class="layui-inline time" id="time">
                            <span class="label-title">时间：</span>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input create_time" id="create_time" placeholder="yyyy-MM-dd">
                            </div>
                        </div>
                        <div class="layui-input-inline">
                            <select name="apply_status" class="apply_status layui-input">
                                <option value="">状态</option>
                                <option value="2">待支付</option>
                                <option value="1">已拒绝</option>
                                <option value="3">已支付</option>
                                <option value="4">拒绝放款</option>
                                <option value="5">已放款</option>
                                <option value="6">已完结</option>
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
    <script type="text/html" id="barDemo1">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>

    <script type="text/html" id="barDemo2">
        <a class="layui-btn layui-btn-mini" lay-event="evaluate">&nbsp; 评价 &nbsp;</a>
    </script>

    <script>
        layui.use('table', function(){
            var table = layui.table;
            //监听查看按钮事件
            table.on('tool(edit)', function(obj){
                if(obj.event === 'evaluate'){
                    layer_show('评价','/business/evaluateAdd/'+obj.data.id,'500','440')
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
                title : "今日匹配",
                little_title : "接收",
                apply_type:0
            },
            methods:{
                change:function (type) {
                    //左侧导航添加样式
                    $("a").removeClass('layui-this');
                    $("#"+type).addClass('layui-this');
                    app.apply_type = type;
                    //更改面包屑
                    switch (type){
                        case 0:
                            app.title = "今日匹配";
                            app.little_title = "接收";
                            $('#time').addClass('time');
                            break;
                        case 1:
                            app.title = "今日匹配";
                            app.little_title = "拒绝";
                            $('#time').addClass('time');
                            break;
                        case 2:
                            app.title = "历史匹配";
                            app.little_title = "接收";
                            $('#time').removeClass('time');
                            break;
                        case 3:
                            app.title = "历史匹配";
                            app.little_title = "拒绝";
                            $('#time').removeClass('time');
                            break;
                        case 4:
                            app.title = "已完结";
                            app.little_title = "接收";
                            $('#time').removeClass('time');
                            break;
                        default:
                            app.title = "已完结";
                            app.little_title = "拒绝";
                            $('#time').removeClass('time');
                    }
                    //获取数据
                    $.post('/business/applyList',{type:type,search:0},function (data) {
                        app.tableData = data;
                        tableData()
                    });

                },
                add:function () {
                    var secondId = app.secondId;
                    layer_show('添加','/business/productRead/0/1/'+secondId,'880','880')
                },
                find:function () {
                    //申请类型
                    var apply_type= app.apply_type;
                    //分类id
                    var cat_id = $('.cat').val();
                    //创建时间
                    var create_time = $('.create_time').val();
                    //申请状态
                    var apply_status = $('.apply_status').val();
                    $.post('/business/applyList',{search:1,type:apply_type,cat_id:cat_id,create_time:create_time,apply_status:apply_status},function (data) {
                        app.tableData = data;
                        tableData()
                    });
                }
            },
            beforeCreate:function () {
                $.post('/business/applyList',{type:0,search:0},function (data) {
                    app.tableData = data;
                    tableData()
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
                        ,{field:'id', title: 'ID', width:190}
                        ,{field:'orderType', title: '类型', width:180}
                        ,{field:'name', title: '姓名', width:200 }
                        ,{field:'phone', title: '电话', width:160}
                        ,{field:'money', title: '金额', width:150}
                        ,{field:'cat', title: '分类', width:150}
                        ,{field:'b_apply_status', title: '状态', width:155}
                        ,{field:'create_time', title: '时间', width:155}
                        ,{field:'caozuo', title: '操作', width:255}
                    ]]
                    ,skin: 'row' //表格风格
                    ,even: true
                    ,page: true //是否显示分页
                    ,limits: [5, 7, 10]
                    ,limit: 5 //每页默认显示的数量
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



        //进行操作
        function pay(id,type) {
            var change_type = type;
            var text = "";
            var title = "";
            var success = "";
            if(type == 2){
                layer_show('上传支付凭证','/business/yinlian/'+id,'800','660')
            }else{
                switch (type){
                    case 0:
                        //修改为待支付
                        change_type = 2;
                        title = "确认接单";
                        success = "接单成功";
                        break;

                }
                if(type == 3){
                    layer.msg("支付审核中请耐心等待");
                }else{
                    layer.confirm(title+"?", {
                        btn: ['确定','取消'] //按钮
                    }, function(){
                        $.post("/business/changeOrder",{b_apply_status:change_type,id:id},function (obj) {
                            layer.closeAll();
                            layer.msg(success,{icon:1});
                            setTimeout(function () {
                                location.reload();
                            },1000)
                        })
                    });
                }
            }


        }

        function cancel(id) {

            layer.confirm("确定取消订单?", {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post("/business/changeOrder",{b_apply_status:8,id:id},function (obj) {

                })
            });

        }
    </script>


@endsection
