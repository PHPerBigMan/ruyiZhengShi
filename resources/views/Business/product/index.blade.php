@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">产品分类</a>
                        <dl class="layui-nav-child">
                            @foreach($product_cat as $v)
                                <dd><a href="javascript:;" class="<?php if($v->id == 1)echo 'layui-this';?>" @click="secondCat({{ $v->id }},'{{ $v->cat_name }}','{{ $v->sec_cat_name }}')" id="{{ $v->id }}" >{{ $v->cat_name }}</a></dd>
                            @endforeach
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
                  <span class="layui-breadcrumb">
                  <a><cite>首页</cite></a>
                  <a><cite>产品分类</cite></a>
                  <a><cite v-text="cat_name"></cite></a>
                  <a><cite v-text="sec_cat_name"></cite></a>
                  </span>
            </div>
            <hr>
            <div class="product-cat-nav">
                <div class="layui-btn  cat-btn" v-for="todo in CatArray" @click="changeData(todo.id,todo.cat_name)" :id="todo.id"><span v-text="todo.cat_name" ></span></div>
            </div>
            <div class="form-data">
                {{--<div class="demoTable product_search">--}}
                    {{--搜索ID：--}}
                    {{--<div class="layui-inline">--}}
                        {{--<input class="layui-input" name="id" id="demoReload" autocomplete="off">--}}
                    {{--</div>--}}
                    {{--<button class="layui-btn" data-type="search">搜索</button>--}}
                {{--</div>--}}
                <div class="btns" lay-filter="add">
                        <button class="layui-btn layui-btn-radius" @click="add">添加</button>
                        <button class="layui-btn layui-btn-radius" @click="edit">修改</button>
                        <button class="layui-btn layui-btn-radius" @click="del">删除</button>
                        <button class="layui-btn layui-btn-radius" @click="shelves(1)">上架</button>
                        <button class="layui-btn layui-btn-radius" @click="shelves(0)">下架</button>
                </div>
                <table id="productData"  lay-filter="edit"></table>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script type="text/html" id="barDemo1">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>
    <script>
        layui.use('table', function(){
            var table = layui.table;
            //监听查看按钮事件
            table.on('tool(edit)', function(obj){
                if(obj.event === 'read'){
                    console.log(obj.data);
                    layer_show('查看','/business/productRead/'+obj.data.id+'/3','880','660')
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
                cat_name:'房产类',
                url:'/business/productCat',
                tableData:  [],
                secondId : "",
                sec_cat_name:"住宅贷"
            },
            methods:{
                secondCat:function (id,cat_name,sec_cat_name) {
                    //现金贷暂未开放
                    if(id != 6){
                        //左侧导航添加样式
                        $("a").removeClass('layui-this');
                        $("#"+id).addClass('layui-this');
                        $('.cat-btn').removeClass('layui-btn-danger');

                        app.sec_cat_name = sec_cat_name;
                        $.post('/business/ProductSecondCat',{p_id:id},function (data) {
                            app.secondId = data[0].id;
                            var arr = [20,45,51,35,37];
                            console.log($.inArray(data[0].id,arr));
                            if($.inArray(data[0].id,arr) >= 0){
                                $('#'+data[0].id).addClass('layui-btn-danger');
//                            $('#45').addClass('layui-btn-danger');
                            }
                            app.CatArray = data;
                            app.cat_name = cat_name;
                        });
                        $.post('/business/productList',{cat_id:id},function (data) {
                            app.tableData = data;
                            tableData()
                        });
                    }else{
                        layer.msg('现金贷暂未开放',{icon:2})
                    }
                },
                changeData:function (id,name) {

                    app.secondId = id;
                    app.sec_cat_name = name;
                    $('.cat-btn').removeClass('layui-btn-primary');
                    $('#'+id).addClass('layui-btn-primary');
//                    $(this).addClass('layui-btn-danger');
                    $.post('/business/productList',{cat_id:id},function (data) {
                        app.tableData = data;
                        tableData()
                    });
                },
                add:function () {
                    var secondId = app.secondId;
                    console.log(secondId);
                    layer_show('添加','/business/productRead/0/1/'+secondId,'880','660',1)
                },
                edit:function () {
                    var product_id = ID();
                    var len = product_id.length;
                    var secondId = app.secondId;
                    if(len == 0){
                        layer.msg('请选择一条需要修改的数据',{icon:2});
                    }else if(len > 1){
                        layer.msg('只能选择一条数据进行修改',{icon:2});
                    }else{
                        layer_show('查看','/business/productRead/'+product_id[0]+'/2/'+secondId,'880','660',1)
                    }
                },
                del:function () {
                    layer.confirm('确认删除？', {
                        btn: ['确认','取消'] //按钮
                    }, function(){
                        var product_id = ID();
                        var len = product_id.length;
                        if(len == 0){
                            layer.msg('请选择一项删除的数据',{icon:2})
                        }else{
                            $.post('/business/productDel',{id:product_id},function (obj) {
                                layer.msg(obj.msg);
                                setTimeout(function () {
                                    location.reload();
                                },1000)
                            });
                        }
                    });
                },
                shelves:function (is_show) {
                    var product_id = ID();
                    var len = product_id.length;
                    if(len == 0){
                        layer.msg('请选择至少一条数据',{icon:2})
                    }else{
                        var confirm = is_show == 0 ? "下架" : "提交上架审核";
                        layer.confirm('确认'+confirm+'？',{
                            btn:['确认','取消']
                        },function () {
                            $.post('/business/shelves',{is_show:is_show,id:product_id},function (obj) {
                                layer.msg(confirm+"成功!");
                                setTimeout(function () {
                                    location.reload();
                                },1000)
                            });
                        })
                    }
                }
            },
            beforeCreate:function () {
                $('#20').addClass('layui-btn-danger');
                //获取初始数据
                $.post('/business/ProductSecondCat',{p_id:0},function (data) {
                    app.CatArray = data;
                    //二级分类id
                    app.secondId = data[0].id;
                });
                $.post('/business/productList',{cat_id:20},function (data) {
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
                    elem: '#productData'
                    ,data: app.tableData
                    ,height: 472
                    ,cols: [[ //标题栏
                        {checkbox: true}
                        ,{field:'id', title: 'ID', width:55}
                        ,{field:'pNumber', title: '产品编号', width:120}
                        ,{field:'area', title: '地区范围', width:180}
                        ,{field:'type', title: '担保范围', width:200 }
                        ,{field:'property_cut', title: '估值率', width:160}
                        ,{field:'accrual', title: '利息', width:140}
                        ,{field:'credit', title: '征信要求', width:150}
                        ,{field:'product_cycle', title: '可借款周期',  width:140}
                        ,{field:'lending_type', title: '还款方式', width:180}
                        ,{field:'is_show', title: '状态', width:100}
                        ,{fixed: 'right',title: '查看详情', width:180, align:'center', toolbar: '#barDemo1'}
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
    </script>
@endsection
