@extends('Back.index')
@section('main')
    <p>公司：{{ $product->business->companyName or ''}}</p>
    <p>状态：{{ $product->status_info }}</p>
    <p>产品详情：</p>
    <table class="layui-table">
        <colgroup>
            <col width="150">
            <col width="200">
            <col>
        </colgroup>
        <thead>
        </thead>
        <tbody>
        @foreach($product->content_arr as $key =>$item)
            @if(isset($arr[$key]))
                <tr>
                    <td>{{ $arr[$key]}}</td>
                    <td>{{ $item }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <button class="layui-btn layui-btn-small" onclick="editStatus(1)">通过审核</button>
    <button class="layui-btn layui-btn-small layui-btn-danger" onclick="editStatus(0)">审核不通过</button>
@endsection
@section('js')
    <script>
        function editStatus(type) {
            $.post('/back/product/change/status',{
                id:{{ $product->id }},
                type:type
            },function (obj) {
                if(obj.code == 200){
                    layer.open({
                        content: '更新成功',
                        yes: function(layero, index){
                            window.location.reload()
                        }
                    });
                }else{
                    layer.alert('更新失败')
                }
            })
        }
    </script>
@endsection