/**
 * Created by baimifan-pc on 2017/9/8.
 */
function layer_show(title,url,w,h,type){
    if (title == null || title == '') {
        title=false;
    };
    if (url == null || url == '') {
        url="404.html";
    };
    if (w == null || w == '') {
        w=800;
    };
    if (h == null || h == '') {
        h=($(window).height() - 50);
    };
    if(type == 1){
        layer.open({
            type: 2,
            area: [w+'px', h +'px'],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: title,
            content: url,
            end:function () {
                location.reload()
            }
        });
    }else{
        layer.open({
            type: 2,
            area: [w+'px', h +'px'],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: title,
            content: url,

        });
    }
}

/**
 * @returns {Array}
 * @constructor
 * 获取所有checkbox选中的id
 */

function ID() {
    var array = [];
    //获取layui  checkbox 选中的数据的值
    $('.layui-form-checked').parent().parent().next().find('div').each(function () {
        if($(this).text() != "ID"){
            array.push($(this).text());
        }
    });
    return array;
}

$('.msg-btn').click(function () {
    var phone= $('#phone').val();
    if(phone == ""){
        layer.msg('请输入手机号码',{icon:2});
        return false;
    }else if(!(/^1[34578]\d{9}$/.test(phone))){
        layer.msg('手机号格式错误',{icon:2})
        return false;
    }else{
        $.post('/api/getCode',{phone:phone},function (obj) {
            if(obj.code == 200){
                layer.msg('发送成功',{icon:1});
                time();
            }
        })
    }
});

var countdown= 15;
function time() {
    console.log(countdown);
    if (countdown == 0) {
        clearTimeout(t);
        $(".get-code").text('发送验证码');
        $('.get-code').removeAttr("disabled");
        countdown = 15;
    } else {
        var value="重新发送(" + countdown + ")";
        $(".get-code").text(value);
        $('.get-code').attr("disabled",'true');
        countdown--;
        var  t = setTimeout('time()',1000);
    }
}

/**
 * @param data
 * @param type
 * @returns {string}
 * 返回layui表格工具条
 */

function cols(data,type) {
    var toolbar = "";
    var col = "";
    if(type == 0){
        if(data.length != 0){
            var s = data[0].b_is_evaluate;
            if(s == 0){
                toolbar = "#noEva";
            }
            if(s == 1){
                toolbar = '#Evaed';
            }
        }
        col = {fixed: 'right',title: '操作', width:175, align:'center', toolbar: toolbar};
    }
    return col;
}


/**
 *
 * @param id 表格id
 * @param cols 表格列表
 * @param width 表格宽度
 * @constructor
 */

function UsetableData(id,cols,width) {
    layui.use('table', function(){
        var table = layui.table;
        //展示已知数据
        var tableIns = table.render({
            elem: id
            ,data: app.tableData
            ,height: 472
            ,width:width
            ,cols: cols
            ,skin: 'row' //表格风格
            ,even: true
            ,page: true //是否显示分页
            ,limits: [5, 7, 10]
            ,limit: 5 //每页默认显示的数量
        });
        var $ = layui.$, active = {
            search:function () {
                var demoReload = $(id);
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

/**
 * @param phone
 * @returns {boolean}
 * 检查手机格式
 */

function checkPhone(phone) {
    if(phone == ""){
        layer.msg("手机号不能为空",{icon:2});
        return false;
    }else if(!(/^1[34578]\d{9}$/.test(phone))){
        layer.msg("手机号格式错误",{icon:2});
        return false;
    }
}



