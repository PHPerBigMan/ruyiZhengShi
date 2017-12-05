/**
 * Created by baimifan-pc on 2017/9/11.
 */
tds=document.getElementsByTagName("td");
for(var i=0;i<tds.length;i++){
    tds[i].onmouseover=test;
}
var index;
function  test(){

    for(var i=0;i<tds.length;i++){
        if(tds[i]==this)
        {
            index=i;
        }
    }
    //选中的设置成红色 没选中的设置成黑色
    for(var i=0;i<=index;i++) {
        tds[i].style.color = "red";
    }
    for(var i=index+1;i<tds.length;i++){
        tds[i].style.color="black";
    }
}

function save() {
    var score = (index+1);

    if(isNaN(score)){
      layer.msg('请评分',{icon:2});
      return false;
    }else{
        var user_id = $('#user_id').val();
        var product_id = $('#product_id').val();
        var content = $('#content').val();
        console.log(content);
        $.post('/business/evaluateSave',{user_id:user_id,product_id:product_id,content:content,score:score},function (obj) {
            if(obj.code == 200){
                layer.msg(obj.msg);
            }
        })
    }
}