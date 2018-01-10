@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="手机号" value="{{ $keyword }}">
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">时间：</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="test10" placeholder=" - " style="width: 300px" name="exTime" value="{{ $time }}">
                </div>
            </div>

            <button class="layui-btn" data-type="reload">搜索</button>
        </form>
        <button  onclick="excel()" class="layui-btn">导出用户</button>
    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>用户头像</th>
                <th>用户名</th>
                <th>手机号</th>
                <th>注册时间</th>
                <th>身份证归属地</th>
                <th>推荐人ID</th>
                <th>如易金券</th>
                <th>如易金币</th>
                {{--<th>访问次数</th>--}}
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->id }}</td>
                    <td>
                        <img src="{{ $value->user_pic }}" alt="">
                    </td>
                    <td id="{{ $value->id }}">{{ $value->user_name }}</td>
                    <td>{{ $value->phone }}</td>
                    <td>{{ $value->create_time }}</td>
                    <td>{{ $value->belonging }}</td>
                    <td>{{ empty($value->tuiUserId) ? "非推荐" : $value->tuiUserId}}</td>
                    <td id="integral">{{ $value->integral }}</td>
                    <td id="gold">{{ $value->gold }}</td>
                    {{--<td>{{ $value->view_count }}</td>--}}
                    <td>
                        <a href="{{ route('user.detail', ['id' => $value->id]) }}" class="layui-btn layui-btn-small">查看用户信息</a>
                        <p class="layui-btn layui-btn-small" onclick="add({{ $value->id }})">增加如易金币</p>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $data->appends(['exTime' => $time,'keyword' => $keyword])->links() }}
    </div>

@endsection

@section('js')
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //日期时间范围
            laydate.render({
                elem: '#test10'
                ,type: 'datetime'
                ,range: true
            });
        });
        /**
         * 修改分类
         * @param id
         * @param cat_name
         */

        function edit(id,cat_name) {
            layer.prompt({title: '修改分类', formType: 3,value:cat_name}, function(value, index){
                $.post('/back/product/CatAdd',{type:0,cat_name:value,id:id},function (obj) {
                    if(obj.code == 200){
                        $('#'+id).text(value);
                    }else{
                        layer.msg("数据未改动");
                    }
                });
                layer.close(index);
            });
        }

        /**
         * 删除分类
         * @param id
         * @param cat_name
         */

        function del(id,cat_name) {
            var type = 2;
            layer.confirm('确定删除分类【'+cat_name+'】？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post('/back/product/cat_del',{id:id},function (obj) {
                    if(obj.code == 200){
                        type = 1;
                        //删除对应的 tr
                    }
                    layer.msg(obj.msg,{icon:2})
                });
            });
        }

        /**
         * 为用户增加如易金券
         * @param id
         */
        function add(id) {
            var integral = $('#integral').val();
            var gold = $('#gold').val();
            layer.prompt({title: '输入需要充值的金币数额', formType: 3}, function(count, index){
                layer.close(index);
                layer.confirm("确定要为该用户增加<span style='color: red'>"+count+"</span>金币", {
                    btn: ['确定','取消'] //按钮
                }, function(index1){
                    layer.close(index1);
                   $.post('addInt',{count:count,id:id,type:1},function (obj) {

                       layer.msg(obj.msg+"本页面将在2秒后自动刷新！",{time:2});
                       if(obj.code == 200){
                         setTimeout(function () {
                             location.reload();
                         },2000);
                       }
                   });
                });
            });
        }

        function excel() {
            // 关键词
            var keyword = $('#demoReload').val();

            var time = $('#test10').val();
            location.href = "/back/excel?exl=userC&keyword="+keyword+"&time="+time;
        }
    </script>
@endsection