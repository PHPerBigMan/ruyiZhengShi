@extends('Back.index')
@section('main')

    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this" type="today">今日成交额</li>
            <li type="month">本月成交额</li>
            <li type="Allmonth">月度成交额</li>
            <li type="year">年度成交额</li>
            <li type="area">地区成交排行(月度统计)</li>

        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                @foreach($cat as $v)
                    <button class="layui-btn today"  onclick="total({{ $v->id }})">今日{{ $v->cat_name }}成交额</button>
                @endforeach
            </div>
            <div class="layui-tab-item">
                @foreach($cat as $v)
                    <button class="layui-btn today"  onclick="total({{ $v->id }})">本月{{ $v->cat_name }}成交额</button>
                @endforeach
            </div>
            <div class="layui-tab-item">
                <button class="layui-btn today"  onclick="total(0)">月度成交额统计</button>
            </div>
            <div class="layui-tab-item">
                <button class="layui-btn today"  onclick="total(0)">年度成交额统计</button>
            </div>
            <div class="layui-tab-item">
                @foreach($area as $v)
                    <div class="area-list" onclick="total('{{ $v->province }}')">{{ $loop->iteration }} . {{ $v->province }}</div>
                @endforeach
            </div>
        </div>
    </div>


@endsection
@section('js')
    <script>
        function total(cat_id) {
            var totalType = $('.layui-tab-title .layui-this').attr('type');
            var title ;
            switch(totalType)
            {
                case "today":
                    title = "今日成交额";
                    break;
                case "month":
                    title = "本月成交额";
                    break;
                case "Allmonth":
                    title = "月度成交额";
                    break;
                case "area":
                    title = "地区排行详情"+"【"+cat_id+"】";
                    break;
                default:
                    title = "年度成交额";
                    break;
            }
            if(totalType != "area"){
                layer_open(title,'/back/todayTotal?id='+cat_id+'&totalType='+totalType,1380,800);
            }else{
                layer_open(title,'/back/todayTotal?id=0&totalType='+cat_id,1380,800);
            }
        }


        layui.use('element', function(){
            var $ = layui.jquery
                ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

            //触发事件
            var active = {
                tabAdd: function(){
                    //新增一个Tab项
                    element.tabAdd('demo', {
                        title: '新选项'+ (Math.random()*1000|0) //用于演示
                        ,content: '内容'+ (Math.random()*1000|0)
                        ,id: new Date().getTime() //实际使用一般是规定好的id，这里以时间戳模拟下
                    })
                }
                ,tabDelete: function(othis){
                    //删除指定Tab项
                    element.tabDelete('demo', '44'); //删除：“商品管理”


                    othis.addClass('layui-btn-disabled');
                }
                ,tabChange: function(){
                    //切换到指定Tab项
                    element.tabChange('demo', '22'); //切换到：用户管理
                }
            };
        });
    </script>
@endsection