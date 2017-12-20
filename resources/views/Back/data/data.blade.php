<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="//cdn.bootcss.com/vue/1.0.24/vue.min.js"></script>
    <style>
        .am-btn-default{background: none}
    </style>
</head>
<body>
<div class="am-cf admin-main2">
    <!-- content start -->
    <div class="admin-content">
        <div class="admin-content-body">
            <div class="am-cf am-padding am-padding-bottom-0 hw-nav">
                <div class="am-fl am-cf">
                    <ol class="am-breadcrumb">
                        <li class="am-active">{{ $catName }}</li>
                    </ol>
                </div>
                <div class="am-fr am-cr">
                    <a class="am-btn am-btn-warning hw-shuaxin" href="javascript:location.replace(location.href);">
                        <i class="am-icon-refresh"></i>
                    </a>
                </div>
            </div>

            <div class="am-u-sm-12">
                <div id="container" style="height: 400px;padding: 10px;"></div>
            </div>
        </div>
    </div>
    <!-- content end -->
</div>

<script src="//cdn.bootcss.com/jquery/2.0.2/jquery.min.js"></script>
<script src="//cdn.bootcss.com/echarts/3.0.0/echarts.min.js"></script>

<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('container'));

    // 指定图表的配置项和数据
    var option = {
        title:{
            show:true,
            text: '分类成交额',
            left: 'left',
            textStyle:{
                color:'#008acd',
                fontStyle:'normal',
                fontSize:13
            }
        },
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        toolbox: {
            feature: {
                dataZoom: {
                    yAxisIndex: 'none'
                },
                restore: {},
                saveAsImage: {}
            }
        },
        legend: {
            data:['成交额(万元)']
        },
        grid: {
            top: '150px',
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                data : {!! $catData !!},
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'成交额(万元)',
                type:'bar',
                data:{!! $count !!},
            },
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>
</body>
</html>