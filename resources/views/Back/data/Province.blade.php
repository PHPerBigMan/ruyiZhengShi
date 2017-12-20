<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="//cdn.bootcss.com/vue/1.0.24/vue.min.js"></script>
    <link rel="stylesheet" href="{{ URL::asset('css/admin.css?v_6') }}">
    <style>
        .am-btn-default{background: none}
    </style>
</head>
<body>
<div class="am-cf admin-main2">
    <!-- content start -->
    <div class="admin-content">
        @foreach($data as $v)
            <div class="area-list" onclick="total('{{ $v->province }}')">{{ $loop->iteration }} . {{ $v->province }}</div>
            @endforeach
    </div>
    <!-- content end -->
</div>

<script src="//cdn.bootcss.com/jquery/2.0.2/jquery.min.js"></script>
<script src="//cdn.bootcss.com/echarts/3.0.0/echarts.min.js"></script>
<script src="https://cdn.bootcss.com/layer/3.0.3/layer.js"></script>
<script src="{{ URL::asset('dist/js/admin.js') }}"></script>
<script type="text/javascript">
    function total(area) {
        var index= parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
        layer_open_area("省份排名数据",'/back/todayTotal?id=0&totalType='+area,1380,800);
    }
</script>
</body>
</html>