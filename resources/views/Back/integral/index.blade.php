@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <label for="">类型：</label>
            <select name="is_gold" id="select-type" class="order-select">
                <option value="3" <?php if($is_gold == 3)echo "selected";?>>全部</option>
                <option value="2" <?php if($is_gold == 2)echo "selected";?>>如易金币</option>
                <option value="1" <?php if($is_gold == 1)echo "selected";?>>如易金券</option>
            </select>
            <label for="">用户类型：</label>
            <select name="user_type" id="select-type" class="order-select">
                <option value="3" <?php if($user_type == 3)echo "selected";?>>全部</option>
                <option value="2" <?php if($user_type == 2)echo "selected";?>>商户端</option>
                <option value="1" <?php if($user_type == 1)echo "selected";?>>客户端</option>
            </select>
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="用户Id" value="{{ Request::input('keyword') }}">
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">时间：</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="test10" placeholder=" - " style="width: 300px" name="time" value="{{$time}}">
                </div>
            </div>
            <button class="layui-btn" data-type="reload">搜索</button>
        </form>
    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <thead>
            <tr>
                <th>序号</th>
                <th>用户ID</th>
                <th>金币</th>
                <th>说明</th>
                <th>用户类型</th>
                <th>金币类型</th>
                <th>生成时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr id="{{ $value->id }}">
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->user_id }}</td>
                    <td>
                        {{ $value->change_integral }}
                    </td>
                    <td>{{ $value->desc }}</td>
                    <td>{{ $value->user_type_info }}</td>
                    <td>{{ $value->is_gold }}</td>
                    <td>{{ $value->create_time }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div>
            {{ $data->appends(['keyword'=>$keyword,'is_gold'=>$is_gold,'user_type'=>$user_type])->links() }}
        </div>
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
    </script>
    @endsection