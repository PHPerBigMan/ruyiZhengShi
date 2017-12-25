@extends('Back.index')
@section('main')
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col width="150">
                <col width="150">
                <col width="750">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>消息类型</th>
                <th>消息标题</th>
                <th>消息内容</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->id }}</td>

                    <td>{{ $value->type }}</td>
                    <td>{{ $value->title }}</td>
                    <td>{{ mb_substr($value->content,0,80) }}</td>
                    <td>
                        <a href="ArticleEdit?id={{ $value->id }}" class="layui-btn layui-btn-small">编辑</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $data->links() }}
    </div>

@endsection

