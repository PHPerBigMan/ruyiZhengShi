@extends('Business.index')

@section('laybar')
    <div id="Admin">

        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="layui-this" @click="change(1)" id="1">公司资料</a>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed ">
                        {{--<a class="" @click="change(2)" id="2">账户余额</a>--}}
                    </li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="ChildList">子账号管理</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <div class="b-nav-bar">
               <span class="layui-breadcrumb">
                   <a><cite>个人中心</cite></a>
                   <a><cite v-text="title"></cite></a>
               </span>
            </div>

                <div class="admin-btn">
                    <button class="layui-btn layui-btn-radius" v-show="is_type == 1" @click="addData">完善(修改)资料</button>
                    {{--<button class="layui-btn layui-btn-radius" v-show="is_type == 2" @click="tixian">提现</button>--}}
                </div>
                <div class="admin-li" v-show="is_type == 1">
                    <table >
                        <tr>
                            <td>公司名称:</td>
                            <td>{{ $data->companyName }}</td>
                        </tr>
                        <tr>
                            <td>公司编号ID:</td>
                            <td>{{ $data->number }}</td>
                        </tr>
                        <tr>
                            <td>企业代码:</td>
                            <td>{{ $data->companyCode }}</td>
                        </tr>
                        <tr>
                            <td>企业法人:</td>
                            <td>{{ $data->companyLegal }}</td>
                        </tr>
                        <tr>
                            <td>联系地址:</td>
                            <td>{{ $data->companyAddress }}</td>
                        </tr>
                        <tr>
                            <td>联系电话:</td>
                            <td>{{ $data->phone }}</td>
                        </tr>
                        <tr>
                            <td>金融管家:</td>
                            <td>{{ $data->companyHouse }}</td>
                        </tr>
                        <tr>
                            <td>管家联系电话:</td>
                            <td>{{ $data->companyHousePhone }}</td>
                        </tr>
                        <tr>
                            <td>是否有金融资质:</td>
                            <td>
                                @if($data->qualification === 1)
                                    是
                                @else
                                    否
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>所属类型:</td>
                            <td>{{ $data->type }}</td>
                        </tr>
                        <tr>
                            <td>营业执照:</td>
                            <td><img src="{{ $data->pic }}" alt="" class="admin-img"></td>
                        </tr>
                    </table>
                </div>


                <div class="admin-li-table" v-if="is_type == '2'">
                    <div>
                        账户余额: ￥{{ $data->money }}
                    </div>
                </div>

                <div class="form-data-table" v-show="is_type == '2'">
                    <table id="ListData"  lay-filter="edit"></table>
                </div>


                <div class="form-data-table" v-show="is_type == '3'">
                    <div class="child-btns" lay-filter="add" v-show="is_type == '3'">
                        <button class="layui-btn layui-btn-radius" @click="add">添加</button>
                        <button class="layui-btn layui-btn-radius" @click="edit">修改</button>
                        <button class="layui-btn layui-btn-radius" @click="del">删除</button>
                    </div>

                    <table id="ChildData"  lay-filter="edit" ></table>
                </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        //Vue
        var app = new Vue({
            el: '#Admin',
            data: {
                is_type : 1,
                title:'公司资料',
                tableData:[]
            },
            methods:{
                change:function (id) {
                    $('a').removeClass('layui-this');
                    $('#'+id).addClass('layui-this');
                    app.is_type = id;
                    //账户余额数据
                    if(id == 2){
                        $.post('/business/PayList',function (data) {
                            console.log(data);
                            app.tableData = data;
                            var  cols= [[ //标题栏
                                        {field:'id', title: 'ID', width:200}
                                        ,{field:'type', title: '事项名称', width:300}
                                        ,{field:'money', title: '金额', width:300 }
                                        ,{field:'create_time', title: '时间', width:300}
                                    ]];
                            tableData('#ListData',cols,1105)
                        });
                    }
                    //子账号管理
                    if(id == 3){
                        $.post('/business/ChildList',function (data) {
                            app.tableData = data;
                            var  cols= [[ //标题栏
                                 {checkbox: true}
                                ,{field:'id', title: 'ID', width:200}
                                ,{field:'name', title: '子账号', width:200}
                                ,{field:'password', title: '密码', width:200 }
                                ,{field:'method', title: '权限', width:450}
                            ]];

                            tableData('#ChildData',cols,1105)
                        })
                    }
                    app.title = id == 1 ?'公司资料' : id == 2 ? "账户余额" : "子账户管理";
                },
                add:function () {
                    layer_show('添加','/business/ChildShow/0',440,440,1)
                },
                edit:function () {
                    var id = ID();
                    var len = id.length;
                    if(len == 0){
                        layer.msg('请选择一条需要修改的数据',{icon:2});
                    }else if(len > 1){
                        layer.msg('只能选择一条数据进行修改',{icon:2});
                    }else{
                        layer_show('修改','/business/ChildShow/'+id[0],440,440,1)
                    }
                },
                del:function () {
                    layer.confirm('确认删除？', {
                        btn: ['确认','取消'] //按钮
                    }, function(){
                        var id = ID();
                        var len = id.length;
                        if(len == 0){
                            layer.msg('请选择一项删除的数据',{icon:2})
                        }else{
                            $.post('/business/ChildDel',{id:id},function (obj) {
                                layer.msg(obj.msg);
                                setTimeout(function () {
                                    location.reload();
                                },1000)
                            });
                        }
                    });
                },
                addData:function () {
                    layer_show('完善(修改)资料','/business/adminAdd',880,880,1)
                },
                tixian:function () {

                }
            },
        });

        //数据表格
        function tableData(elem,cols,width) {
            layui.use('table', function(){
                var table = layui.table;
                //展示已知数据
                var tableIns = table.render({
                    elem: elem
                    ,data: app.tableData
                    ,height: 472
                    ,width:width
                    ,loading:true
                    ,cols: cols
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
@endsection
