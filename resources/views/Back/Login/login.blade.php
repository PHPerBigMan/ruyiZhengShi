<!DOCTYPE html>

<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>登录</title>
  <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('business/css/login.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('css/business.css') }}">
</head>

<body class="beg-login-bg">
<div class="beg-login-box">
  <header>
    <h1>后台登录</h1>
  </header>
  <div class="beg-login-main" id="form">

    <form class="layui-form" id="form-data">
      {{ csrf_field() }}
      <div class="layui-form-item">
        <label class="beg-login-icon">
          <i class="layui-icon">&#xe612;</i>
        </label>
        <input type="text" name="admin_name"  autocomplete="off" placeholder="账号" class="layui-input" @keyup.enter="login">
      </div>
      <div class="layui-form-item">
        <label class="beg-login-icon">
          <i class="layui-icon">&#xe612;</i>
        </label>
        <input type="password" name="admin_pwd"  autocomplete="off" placeholder="密码" class="layui-input" @keyup.enter="login">
      </div>
    </form>
    <div class="layui-form-item">
      <div class="beg-pull-left beg-login-remember">
        <label class="forget_pwd">忘记密码?</label>
      </div>
      <div class="beg-pull-right">
        <button class="layui-btn layui-btn-primary" @click="login">
          <i class="layui-icon">&#xe650;</i> 登录
        </button>
      </div>
      <div class="beg-clear"></div>
    </div>

    {{--<div class="ruzhu">--}}
    {{--<a href="/business/settled">入驻申请</a>--}}
    {{--</div>--}}
  </div>

  <footer>
    <p>如易金服</p>
  </footer>
</div>
<script src="{{ URL::asset('layui/layui.js') }}"></script>
<script src="{{ URL::asset('layui/layui.all.js') }}"></script>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/vue/2.4.2/vue.js"></script>
<script>
    var form = new Vue({
        el: '#form',
        methods: {
            login: function () {
                $.post("{{ url('/back/login/check') }}",$('#form-data').serialize(),function(code){
                    if(code.code == 200){
                        layer.msg(code.msg,{icon:1,time:2000});
                        setTimeout(function(){
                            location.href = "/back/dashboard";
                        },1000);
                    }else{
                        layer.msg(code.msg,{icon:2});
                    }
                })
            },
        }
    })
</script>

</body>

</html>