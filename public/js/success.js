/**
 * Created by hongwenyang on 2017/11/3.
 */

/**
 * 支付
 * @param id
 */

function pay(id,type) {
    // alert('/business/yinlian/'+id+'/'+type);
    layer_show('支付','/business/yinlian/'+id+'/'+type)
}

/**
 * 取消
 * @param id
 */
function cancel(id) {
    layer.confirm('确定取消？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        $.post('/business/OrderCancel',{id:id,b_apply_status:8},function (obj) {
            layer.msg('订单取消成功',{icon:1});
            setTimeout(function () {
                location.reload();
            },1000);
        });
    });
}

/**
 * 放款
 * @param id
 */
function fk(id) {
    layer.confirm('确定放款？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        $.post('/business/OrderCancel',{id:id,b_apply_status:6},function (obj) {
            layer.msg('放款成功',{icon:1});
            setTimeout(function () {
                location.reload();
            },1000);
        });
    });
}
/**
 * 拒绝放款
 * @param id
 */
function jj(id) {
    layer.confirm('确定拒绝放款？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        $.post('/business/OrderCancel',{id:id,b_apply_status:5},function (obj) {
            layer.msg('拒绝放款成功',{icon:1});
            setTimeout(function () {
                location.reload();
            },1000);
        });
    });
}
/**
 * 评价
 * @param id
 */
function pj(id) {
    layer_show('评价','/business/evaluateAdd/'+id+'/0','660','540')
}