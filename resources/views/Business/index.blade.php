@extends('Business.header')
@section('content')

<body>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo">如易金服</div>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-left">

      <li class="layui-nav-item <?php  if($title == 'index') echo 'layui-this';?>">
        <a href="/business/back ">首页</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'success') echo 'layui-this';?>">
        <a href="/business/success/0">成功匹配</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'message') echo 'layui-this';?>">
        <a href="/business/message">消息</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'list') echo 'layui-this';?>">
        <a href="/business/apply">匹配列表</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'evaluate') echo 'layui-this';?>">
        <a href="/business/evaluate">评价管理</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'share') echo 'layui-this';?>">
        <a href="/business/share/1/20">如易共享</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'integral') echo 'layui-this';?>">
        <a href="/business/integral">如易金币</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'black') echo 'layui-this';?>">
        <a href="/business/black">如易黑名单</a>
      </li>
      <li class="layui-nav-item <?php  if($title == 'feedback') echo 'layui-this';?>">
        <a href="/business/feedback">意见反馈</a>
      </li>
    </ul>
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item">
        <a href="javascript:;">
          <?php echo session('business_name');?>
        </a>
        <dl class="layui-nav-child">
          <dd><a href="/business/admin">个人中心</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="/business/loginout">退出</a></li>
    </ul>
  </div>
  @yield('laybar')
</div>
  @yield('main')

  <script>
  </script>
</div>
<div class="layui-footer footer footer-demo" id="admin-footer">
  <div class="layui-main">
    <p>2017 &copy;
      <a href="http://www.baimifan.cn/" target="_blank"></a>
    </p>
  </div>
</div>
</body>
</html>
@endsection