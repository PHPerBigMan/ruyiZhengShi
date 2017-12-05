@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">评价</a>
                        <dl class="layui-nav-child">
                            <dd><a href="javascript:;" @click="change(0)" id="0" class="layui-this">待评价</a></dd>
                            <dd><a href="javascript:;" @click="change(1)" id="1">已评价</a></dd>

                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
                  <span class="layui-breadcrumb">
                  <a><cite>评价管理</cite></a>
                  <a><cite v-text="evaluate_name"></cite></a>
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
                    <div class="layui-input-inline">
                        <select name="cat_id" class="selector">
                            <option value="">请选择分类</option>
                            @foreach($data as $v)
                                <option value="{{ $v->id }}">{{ $v->cat_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="layui-btn" @click="find()">筛选</button>
                </div>
                <table id="evaluateData"  lay-filter="edit"></table>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script type="text/html" id="barDemo1">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>

    <script type="text/html" id="noEva">
        <a class="layui-btn layui-btn-mini" lay-event="evaluate">&nbsp; 评价 &nbsp;</a>
    </script>
    <script type="text/html" id="Evaed">
        <a class="layui-btn layui-btn-mini" lay-event="Evaed">&nbsp; 查看评价 &nbsp;</a>
    </script>
    <script>
        layui.use('table', function(){
            var table = layui.table;
            //监听查看按钮事件
            table.on('tool(edit)', function(obj){
                if(obj.event === 'evaluate'){
                    layer_show('评价','/business/evaluateAdd/'+obj.data.id+'/0','500','440')
                }
                if(obj.event === 'Evaed'){
                    layer_show('评价','/business/evaluateAdd/'+obj.data.id+'/1','500','680')
//                    console.log(obj.data);
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
                evaluate_name:'待评价',
                url:'/business/evaluateData',
                tableData:  [],
                secondId : "",
                b_is_evaluate : 0
            },
            methods:{
                change:function (b_is_evaluate) {
                    //左侧导航添加样式
                    $("a").removeClass('layui-this');
                    $("#"+b_is_evaluate).addClass('layui-this');
                    //更改面包屑
                    b_is_evaluate == 0 ? app.evaluate_name = "待评价" : app.evaluate_name = "已评价";

                    app.b_is_evaluate = b_is_evaluate;
                    $.post('/business/evaluateList',{b_is_evaluate:b_is_evaluate},function (data) {

                        app.tableData = data;
                        var col = cols(data,0);
                        console.log(col);
                        tableData(col)
                    });
                },
                add:function () {
                    var secondId = app.secondId;
                    layer_show('添加','/business/productRead/0/1/'+secondId,'880','880')
                },
                find:function () {
                    //筛选
                    var b_is_evaluate= app.b_is_evaluate;
                    var cat_id = $('.selector').val();
                    $.post('/business/evaluateList',{b_is_evaluate:b_is_evaluate,cat_id:cat_id},function (data) {
                        app.tableData = data;
                        var col = cols(data,0);

                        tableData(col)
                    });
                }
            },
            beforeCreate:function () {
                $.post('/business/evaluateList',{b_is_evaluate:0},function (data) {
                    app.tableData = data;
                    var col = cols(data,0);

                    tableData(col)
                });

            }
        });



        //数据表格
        function tableData(col) {
            layui.use('table', function(){
                var table = layui.table;
                //展示已知数据
                var tableIns = table.render({
                    elem: '#evaluateData'
                    ,data: app.tableData
                    ,height: 472
//                    ,width:398
                    ,cols: [[ //标题栏
                        {checkbox: true}
                        ,{field:'id', title: 'ID', width:40}
                        ,{field:'orderType', title: '类型', width:180}
                        ,{field:'name', title: '姓名', width:240 }
                        ,{field:'phone', title: '电话', width:260}
                        ,{field:'money', title: '金额', width:260}
                        ,{field:'cat', title: '分类', width:260}
                        ,{fixed:'right',title: '查看详情', width:380, align:'center', toolbar: '#barDemo1'}
//                        ,col
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
    </script>

    {{--layTableCheckbox--}}

@endsection
