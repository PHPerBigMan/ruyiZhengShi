@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        {{--<form action="">--}}
            {{--<div class="layui-inline">--}}
                {{--<input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="用户名或手机号">--}}
            {{--</div>--}}
            {{--<button class="layui-btn" data-type="reload">搜索</button>--}}
        {{--</form>--}}
    </div>
    <div class="layui-form table-data">
        <div class="am-g">
            <div class="am-u-sm-12">
                <div id="container" style="height: 400px;padding: 20px;"></div>
            </div>

        </div>
    </div>

@endsection
<script src="https://cdn.bootcss.com/echarts/3.8.5/echarts-en.js"></script>
@section('js')
    <script>
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('container'));

        // 指定图表的配置项和数据
        var option = {
            title:{
                show:true,
                text: '会员新增趋势',
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
                data:['新增会员']
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
                    data : [1,2,3],
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'新增会员',
                    type:'bar',
                    data:[1],
                },
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
@endsection