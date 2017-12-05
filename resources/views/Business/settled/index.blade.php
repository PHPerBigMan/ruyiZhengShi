<!DOCTYPE html>

<html>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>入驻申请</title>
		<link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('business/css/login.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('css/admin.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('css/business.css') }}">
	</head>

	<body class="beg-login-bg">
		<div class="beg-settled-box">
			<header>
				<h1>入驻申请</h1>
			</header>
			<div class="beg-login-main" id="form">

				<form class="layui-form" id="form-data">
					{{ csrf_field() }}
					<div class="layui-form-item">
						<label class="beg-login-icon">
							<i class="layui-icon">&#xe612;</i>
						</label>
						<input type="text" name="account"  autocomplete="off" placeholder="手机号" class="settled-input settled-phone"  id="phone">
						<a class="layui-btn get-code" @click="Getcode">发送验证码</a>
					</div>
					<div class="layui-form-item">
						<label class="beg-login-icon">
							<i class="layui-icon">&#xe612;</i>
						</label>
						<input type="password" name="code"  autocomplete="off" placeholder="验证码" class="settled-input" >
					</div>
					<div class="layui-form-item">
						<label class="beg-login-icon">
							<i class="layui-icon">&#xe612;</i>
						</label>
						<input type="password" name="password"  autocomplete="off" placeholder="密码" class="settled-input" >
					</div>
					<div class="layui-form-item">
						<label class="beg-login-icon">
							<i class="layui-icon">&#xe612;</i>
						</label>
						<input type="password" name="repassword"  autocomplete="off" placeholder="确认密码" class="settled-input">
					</div>
				</form>
				<div class="layui-form-item">
					<div class="beg-pull-right">
						<button class="layui-btn settled-btn-primary">
							<i class="layui-icon"></i> 立即入驻
						</button>
					</div>
					<div class="beg-clear"></div>
				</div>
			</div>

			<footer>
				<p>如易金服</p>
			</footer>
		</div>
		<script src="{{ URL::asset('layui/layui.js') }}"></script>
		<script src="{{ URL::asset('layui/layui.all.js') }}"></script>
		<script src="{{ URL::asset('js/business.js') }}"></script>
		<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdn.bootcss.com/vue/2.4.2/vue.js"></script>
		<script>
            var form = new Vue({
                el: '#form',
                methods: {
                    Getcode:function () {
						var flag = checkPhone($('#phone').val());
						if(flag != false){
                            $.post("/api/getCode",{phone:$('#phone').val()},function (data) {
                                console.log(data);
                                if(data.code == 200){
                                    layer.alert('短信发送成功',{icon:1});
                                    time(15);
                                }else{
                                    layer.alert('短信发送失败',{icon:2})
                                }
                            });
						}
                    }
                }
            });


		</script>
	</body>

</html>