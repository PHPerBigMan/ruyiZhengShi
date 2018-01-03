@extends('Back.index')
@section('main')
    <div class="layui-form table-data">
        <label for="" class="productCatList">{{ $companyName }}</label>
        <table class="layui-table">
            <colgroup>
                <col width="150">
                <col width="150">
                <col width="350">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>分类名称</th>
                <th>产品总数</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->cat_name }}</td>
                    <td> <span class="layui-badge">{{ $value->count }}</span></td>
                    <td>
                        <a class="layui-btn layui-btn-small" href="/back/product/catMore/{{ $id }}/{{ $value->id }}">查看产品列表</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $data->links() }}
    </div>

@endsection

@section('js')
    <script>

    </script>
@endsection