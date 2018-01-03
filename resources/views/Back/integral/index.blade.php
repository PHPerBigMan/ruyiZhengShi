@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="用户Id" value="{{ Request::input('keyword') }}">
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
            {{ $data->links() }}
        </div>
    </div>
@endsection