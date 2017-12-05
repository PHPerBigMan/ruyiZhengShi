@extends('Business.header')
@section('content')
    <table class="layui-table">
        <tr>
            <td>公司{{ $data->number }}</td>
            <td>金额:{{ $data->content->money }}万</td>
        </tr>
        <tr>
            <td>产品覆盖区域:</td>
            <td>{{ $data->content->area }}</td>
        </tr>
        <tr>
            <td>担保品范围:</td>
            <td>{{ $data->content->type }}</td>
        </tr>
        <tr>
            <td>估值率:</td>
            <td>{{ $data->content->property_cut }}</td>
        </tr>
        <tr>
            <td>产品利率:</td>
            <td>{{ $data->content->accrual }}</td>
        </tr>
        <tr>
            <td>征信要求:</td>
            <td>{{ !empty($data->content->credit) ? $data->content->credit: ""}}</td>
        </tr>
        <tr>
            <td>产品周期:</td>
            <td>{{ $data->content->product_cycle }}</td>
        </tr>
        <tr>
            <td>放款时间:</td>
            <td>{{ $data->content->audit_time }}</td>
        </tr>
        <tr>
            <td>其他费用:</td>
            <td>{{ $data->content->other }}</td>
        </tr>
        <tr>
            <td>其他要求:</td>
            <td>{{ $data->content->other_need }}</td>
        </tr>
        <tr>
            <td>审核时间:</td>
            <td>{{ $data->content->audit_time }}</td>
        </tr>
        <tr>
            <td>是否上门:{{ $data->content->is_home }}</td>
            <td>是否抵押:{{ isset($data->content->mortgage_type)? $data->content->mortgage_type: $data->content->is_mortgage  }}</td>
        </tr>
        <tr>
            <td>还款方式:</td>
            <td>{{ $data->content->lending_type }}</td>
        </tr>
        </tbody>
    </table>
    <input type="hidden" id="business_id" value="{{ $user_id }}">
    <button class="share-btn layui-btn" style="margin-left: 50px" onclick="shenqing({{ $id }})">立即申请</button>
    @endsection
@section('js')
    <script src="{{ URL::asset('js/shenqing.js') }}?_v=9"></script>
    @endsection