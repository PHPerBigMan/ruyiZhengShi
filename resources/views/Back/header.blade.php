<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>如易金服后台</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ URL::asset('bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  {{--<link rel="stylesheet" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.csss">--}}
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="{{ URL::asset('css/AdminLTE.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('css/admin.css?v_5') }}">
  <link rel="stylesheet" href="{{ URL::asset('dist/css/skins/_all-skins.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('plugins/iCheck/flat/blue.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('plugins/morris/morris.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('plugins/datepicker/datepicker3.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
{{--  <link rel="stylesheet" href="{{ URL::asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">--}}
  <link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}"  media="all">
  <link rel="stylesheet" href="{{ URL::asset('css/zoomify.min.css') }}">
  <!-- 引入样式 -->

  <!-- 引入组件库 -->

  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
@yield('content')

<!-- SlimScroll -->
{{--<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>--}}



<script src="{{ URL::asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.js"></script>


<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

<script src="{{ URL::asset('plugins/morris/morris.min.js') }}"></script>
<script src="{{ URL::asset('plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ URL::asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ URL::asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ URL::asset('dist/js/app.min.js') }}"></script>
{{--<script src="{{ URL::asset('dist/js/pages/dashboard.js') }}"></script>--}}
<script src="{{ URL::asset('dist/js/demo.js') }}"></script>
<script src="{{ URL::asset('dist/js/admin.js') }}"></script>
<script src="https://cdn.bootcss.com/layer/3.0.3/layer.js"></script>
<script src="{{ URL::asset('layui/layui.js') }}"></script>
<script src="{{ URL::asset('layui/layui.all.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/zoomify.min.js') }}"></script>
{{--<script src="http://res.layui.com/layui/dist/layui.js"></script>--}}
{{--<script src="https://cdn.bootcss.com/jquery/2.2.1/jquery.js"></script>--}}
<script>
    $.extend($.fn.dataTable.defaults, {
        language: {
            "sProcessing": "处理中...",
            "sLengthMenu": "显示 _MENU_ 项结果",
            "sZeroRecords": "没有匹配结果",
            "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
            "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
            "sInfoPostFix": "",
            "sSearch": "搜索:",
            "sUrl": "",
            "sEmptyTable": "表中数据为空",
            "sLoadingRecords": "载入中...",
            "sInfoThousands": ",",
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "上页",
                "sNext": "下页",
                "sLast": "末页"
            },
            "oAria": {
                "sSortAscending": ": 以升序排列此列",
                "sSortDescending": ": 以降序排列此列"
            }
        },
        buttons: true
    });
</script>
@yield('js')