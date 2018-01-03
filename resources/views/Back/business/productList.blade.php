@extends('Back.index')
@section('main')
    <label for="" class="productCatList">{{ $catName }}:</label>
    <div class="layui-form table-data">
        <table class="layui-table">
            <thead>
            <tr>
                <th>产品编号</th>
                <th>地区范围</th>
                <th>额度</th>
                <th>利息</th>
                <th>征信要求</th>
                <th>可借款周期</th>
                <th>其他费用</th>
                <th>审核周期</th>
                <th>抵押范围</th>
                <th>还款方式</th>
                <th>其他要求</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->content->pNumber }}</td>
                    <td>{{ $value->content->area or "" }}</td>
                    <td>{{ $value->content->money or "" }}</td>
                    <td>{{ $value->content->accrual or "" }}</td>
                    <td>{{ $value->content->credit or "" }}</td>
                    <td>{{ $value->content->product_cycle or "" }}</td>
                    <td>{{ $value->content->other_need or "" }}</td>
                    <td>{{ $value->content->audit_time or "" }}</td>
                    <td>{{ $value->content->is_mortgage or "" }}</td>
                    <td>{{ $value->content->lending_type or "" }}</td>
                    <td>{{ $value->content->other or "" }}</td>
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