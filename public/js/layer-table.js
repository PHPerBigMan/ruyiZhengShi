/**
 * Created by baimifan-pc on 2017/10/31.
 */
layui.use('table', function(){
    var table = layui.table;
    //监听查看按钮事件
    table.on('tool(edit)', function(obj){
        if(obj.event === 'read'){
            layer_show('查看','/business/productRead/'+obj.data.id+'/0','680','880')
        }
    });
});
layui.use(['form', 'layedit', 'laydate'], function(){
    var form = layui.form
        ,layer = layui.layer
    //日期
    laydate.render({
        elem: '#date'
    });
    laydate.render({
        elem: '#date1'
    });

});