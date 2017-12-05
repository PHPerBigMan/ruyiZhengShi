//Vue
var app = new Vue({
    el: '#Cat',
    data: {
        CatArray:[],
        cat_name:'房产类',
        url:'/business/productCat',
        tableData:  [],
        secondId : 20,
        sec_cat_name:"住宅贷",
        property: []
    },
    methods:{
        secondCat:function (id,cat_name,sec_cat_name) {
            //左侧导航添加样式
            $("a").removeClass('layui-this');
            $("#"+id).addClass('layui-this');
            app.sec_cat_name = sec_cat_name;
            $.post('/business/ProductSecondCat',{p_id:id},function (data) {
                app.secondId = data[0].id;
                app.CatArray = data;
                app.cat_name = cat_name;

            });
            $.post('/business/productList',{cat_id:id},function (data) {
                app.tableData = data;
                tableData()
            });


        },
        changeData:function (id,name) {
            app.secondId = id;
            app.sec_cat_name = name;
            $('#cat_id').val(id);
        },
        add:function () {
            var secondId = app.secondId;
//                    layer_show('填写共享信息','/business/getData/'+secondId,'660','660')
            layer.open({
                type: 1,
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 0, //不显示关闭按钮
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('.share-table'),
                area:['660px','660px'],
                end:function () {
                    $('.share-table').css({'display':"none"})
                }
            });

        },
        edit:function () {
            var product_id = ID();
            var len = product_id.length;
            if(len == 0){
                layer.msg('请选择一条需要修改的数据',{icon:2});
            }else if(len > 1){
                layer.msg('只能选择一条数据进行修改',{icon:2});
            }else{
                layer_show('查看','/business/productRead/'+product_id[0]+'/2','880','880')
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
        }
    },
    beforeCreate:function () {
        //获取初始数据
        $.post('/business/ProductSecondCat',{p_id:0},function (data) {
            app.CatArray = data;
            //二级分类id
            app.secondId = data[0].id;
        });

        $.post('/api/property',{cat_id:20},function (obj) {
            console.log(obj);
        });


    }
});


var share = new Vue({
    el: '#share',
    data: {
        money:[1,2,3,4]
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
            ,width:1660
            ,cols: [[ //标题栏
                {checkbox: true}
                ,{field:'id', title: 'ID', width:40}
                ,{field:'area', title: '覆盖区域', width:180}
                ,{field:'property', title: '担保范围', width:240 }
                ,{field:'property_cut', title: '担保品折扣率', width:160}
                ,{field:'accrual', title: '产品利率', width:160}
                ,{field:'credit', title: '征信要求', width:200}
                ,{field:'product_cycle', title: '产品周期',  width:140}
                ,{field:'lending_type', title: '还本付息方式', width:200}
                ,{field:'is_show', title: '状态', width:100}
                ,{fixed: 'right',title: '查看详情', width:180, align:'center', toolbar: '#barDemo1'}
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

layui.use('laydate', function(){
    var laydate = layui.laydate;

    //常规用法
    laydate.render({
        elem: '#test1'
    });
});


//如意共享匹配数据

$('.share-btn').click(function () {
    var html = "";
    var number = $('#number').val();
    var phone = $('#phone').val();
    var share = $('#share').val();
    var cat_id = $('#cat_id').val();
    var share_table_data = $('#share-table1').serialize()+"&"+$('#share-table2').serialize()+"&"+$('#share-table3').serialize()+"&number="+number+"&phone="+phone+"&share="+share+"&cat_id="+cat_id;
    var arr = isEmpty();
    if(arr[0] == 404){
        layer.msg(arr[1],{icon:2});
    }else{
        $.post('/business/SearchData',share_table_data,function (obj) {
            if(obj.code == 200){
                $.post('/business/sort',share_table_data,function (share) {
                    layer.load(1, {time: 1000});
                    $('.share-tbody').html("");
                    $.each(share.data,function (share,value) {
                        html += "<tr ><td>"+value.company+"</td><td>"+value.accrual+"</td><td>"+value.lending_type+"</td><td>"+value.is_home+"</td><td>"+value.matching+"</td><td><button class='layui-btn layui-btn-radius' onclick='read("+value.id+")'>查看</button></td></tr>";
                    });
                    $('.share-tbody').append(html);
                })
            }
        });
        layer.closeAll();
    }
//            layer.msg("开发中~请静候");
});

function Province() {
    var father = $(".province").find("option:selected").val();
    $.post('/business/city',{father:father},function (obj) {
        $('#city').empty();
        $('#city').append(obj.data);
    });
}

function City() {
    var father = $(".city").find("option:selected").val();
    $.post('/business/area',{father:father},function (obj) {
        $('#area').empty();
        $('#area').append(obj.data);
    });
}


layui.use('upload', function(){
    // var cardj_img = [];
    // var carxs_img = [];
    // var carjs_img = [];
    var $ = layui.jquery
        ,upload = layui.upload;
    //图片上传
    upload.render({
        elem: '#add-cardj'
        ,url: '/business/shareImg'
        ,before: function(obj){
            //懒加载过度保存时间
            layer.load(1, {time: 2*1000});
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#share-img-cardj').html("");
                $('#share-img-cardj').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img share-img">')
            });
        }
        ,done: function(res){
            //上传完毕
            $('#cardj').val(res.data);


        }
    });

    upload.render({
        elem: '#add-carxs'
        ,url: '/business/shareImg'
        ,before: function(obj){
            //懒加载过度保存时间
            layer.load(1, {time: 2*1000});
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#share-img-carxs').html("");
                $('#share-img-carxs').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img share-img">')
            });
        }
        ,done: function(res){
            //上传完毕
            $('#carxs').val(res.data);
        }
    });

    upload.render({
        elem: '#add-carjs'
        ,url: '/business/shareImg'
        ,before: function(obj){
            //懒加载过度保存时间
            layer.load(1, {time: 2*1000});
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#share-img-carjs').html("");
                $('#share-img-carjs').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img share-img">')
            });
        }
        ,done: function(res){
            //上传完毕
            $('#carjs').val(res.data);
        }
    });


    upload.render({
        elem: '#add-certificateA'
        ,url: '/business/shareImg'
        ,before: function(obj){
            //懒加载过度保存时间
            layer.load(1, {time: 2*1000});
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#share-img-certificateA').html("");
                $('#share-img-certificateA').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img share-img">')
            });
        }
        ,done: function(res){
            //上传完毕
            $('#certificateA').val(res.data);
        }
    });


    upload.render({
        elem: '#add-certificateB'
        ,url: '/business/shareImg'
        ,before: function(obj){
            //懒加载过度保存时间
            layer.load(1, {time: 2*1000});
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#share-img-certificateB').html("");
                $('#share-img-certificateB').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img share-img">')
            });
        }
        ,done: function(res){
            //上传完毕
            $('#certificateB').val(res.data);
        }
    });

    upload.render({
        elem: '#add-Shangbiao'
        ,url: '/business/shareImg'
        ,before: function(obj){
            //懒加载过度保存时间
            layer.load(1, {time: 2*1000});
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#share-img-Shangbiao').html("");
                $('#share-img-Shangbiao').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img share-img">')
            });
        }
        ,done: function(res){
            //上传完毕
            $('#Shangbiao').val(res.data);
        }
    });
});


function isEmpty() {
    var money       = $('#money').val();
    var province    = $('#province option:selected').text();
    var city        = $('#city option:selected').text();
    var area        = $('#area option:selected').text();
    var msg = "提交成功";
    var code = 200;
    console.log(money.length);
    if(money.length == 0 || !$.isNumeric(money)){
        msg = "请填写正确的借款金额";
        code = 404;
    }
    if(province == "请选择"){
        msg = "请选择省份";
        code = 404;
    }
    if(city == "请选择"){
        msg = "请选择城市";
        code = 404;
    }
    if(area == "请选择"){
        msg = "请选择地区";
        code = 404;
    }

    return [code,msg];
}



function read(id) {
    layer_show('产品详情','/business/shareRead/'+id,660,660);
}