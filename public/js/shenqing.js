/**
 * 申请
 * @param id
 */

function shenqing(id) {
    $.post('/api/saveApply',{product_id:id,equipment_type:1,business_id:$('#business_id').val()},function (obj) {
        if(obj.code == 400){
            var html = "";
            if(obj.data.length >= 3){
                var len = 3;
            }else{
                var len = obj.data.length;
            }
            for(var i=0;i<len;i++){

                html += obj.data[i]['title'] + "," + obj.data[i]['content'];
                if(len == 3){
                    if(i == 0 || i==1){
                        html += '-,';
                    }
                }else{
                    if(i == 0){
                        html += '-,';
                    }
                }
            }
            console.log(html);
            layer_show('产品匹配','/business/shareContent/'+id+'/'+html,450,400);
        }else{
            layer.msg('申请成功',{icon:1});
        }
    })
}

/**
 * 继续申请
 * @param id
 * @constructor
 */

function GoHead(id,user_id) {
    // alert(user_id);
    $.post('/api/abnormalSave',{product_id:id,business_id:user_id,equipment_type:1},function (obj) {
        if(obj.code == 200){
            layer.msg('申请成功',{icon:1});
        }else{
            layer.msg('申请异常',{icon:2});
        }
    });
}
