@extends('welcome')

@section('content')
    <div class="register">
        <ul class="register-top">
            <h4 class="reg_titel">如意金服，您的借贷神器</h4>
            <li>
                <h4>手机号</h4>
                <input class="li-inp" type="text" placeholder="请输入手机号" id="phone">
            </li>
            <li>
                <div class="codeBox">
                    <h4>验证码</h4>
                    <input type="text"  class="codeBox-input code-text" placeholder="请输入验证码" >
                </div>
                <button class="code">获取验证码</button>

            </li>
            <li>
                <h4>登陆密码</h4>
                <input class="li-inp password1" type="password" placeholder="6到18位数字、字母">
            </li>
            <li style="border-bottom:1px solid #f1f1f1">
                <h4>再次输入</h4>
                <input  class="li-inp password2" type="password" placeholder="请再次输入密码" >
            </li>
        </ul>
        <div class="contentbox">
            <div>
                <h3>
                    {{ $article->title }}
                </h3>
                <p>
                    {{ $article->content }}
                </p>
            </div>

        </div>
        <div class="p-checkbox">
            <input type="checkbox" hidden id="group" class="checkbox-input">
            <label class="checkbox-inner" for="group"></label>
            <input type="hidden" value="0" id="is_checked">
            <label for="group" id="latow">我已阅读并同意平台《交易协议》</label>
        </div>

        {{--<a href="/success" class="register">注册</a>--}}
        <input type="hidden"  id="tuiUserId" value="{{ $user_id }}">

        <input type="hidden" id="userType" value="{{ $type }}">

        <a  class="register logbtn" id="register">注册并登陆</a>
    </div>
@endsection
@section('js')
    <script>
        $('.checkbox-inner').click(function () {
            var is_checked = $('#is_checked');
            if(is_checked.val() == 0){
                is_checked.val(1);
            }else{
                is_checked.val(0);
            }
        });
        //注册
        $('#register').click(function () {
            var phone = $('#phone').val();
            var code = $('.code-text').val();
            var password1 = $('.password1').val();
            var password2 = $('.password2').val();
            var tuiUserId = $('#tuiUserId').val();
            var userType = $('#userType').val();

            console.log(password1);
            console.log(password2);
            if(phone == ""){
                layer.open({
                    content: '手机号不能为空'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else if(!(/^1[34578]\d{9}$/.test(phone))){
                layer.open({
                    content: '手机号格式不正确'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else if(code == ""){
                layer.open({
                    content: '验证码不能为空'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else if(password1 == "" || password2 == ""){
                layer.open({
                    content: '密码不能空'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else if(password1 != password2){
                layer.open({
                    content: '前后密码不一致'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else if($('#is_checked').val() == '0'){
                layer.open({
                    content: '请同意平台交易协议'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else{
                $.post('/registerAdd',{code:code,phone:phone,password:password1,tuiUserId:tuiUserId,userType:userType,"_token":"{{csrf_token()}}"},function (obj) {
                    if(obj.code == 200){
                        layer.open({
                            content: '注册成功'
                            ,skin: 'msg'
                            ,time: 1 //2秒后自动关闭
                        });
                        setTimeout(function () {
                            if({{ $type }}){
                                location.href = '/dowonloadpage';
                            }else{
                                location.href = '/clientpage';
                            }
                        },1000);
                    }else{
                        layer.open({
                            content: obj.msg
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }
                });
            }
        });


        //获取手机验证码
        $('.code').click(function () {
            var phone = $('#phone').val();
            if(phone == ""){
                layer.open({
                    content: '手机号不能为空'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else if(!(/^1[34578]\d{9}$/.test(phone))){
                layer.open({
                    content: '手机号格式不正确'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else{
                $.post('/getCodetest',{phone:phone,"_token":"{{csrf_token()}}"},function (obj) {
                    if(obj.code == 200){
                        time($('.code'));
                    }else{
                        layer.open({
                            content: obj.msg
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }
                })
            }
        });

        var wait=30;
        function time(o) {
            if (wait == 0) {
                o.attr("disabled", false);
                o.text("获取验证码");
                wait = 30;
            } else { // www.jbxue.com
                o.attr("disabled", true);
                o.text("重新发送(" + wait + ")");
                wait--;
                setTimeout(function() {
                        time(o)
                    },
                    1000)
            }
        }
    </script>
@endsection