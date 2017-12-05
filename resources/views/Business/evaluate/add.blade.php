<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<link rel="stylesheet" href="{{ URL::asset('css/business.css') }}">
<link rel="stylesheet" href="{{ URL::asset('layui/css/layui.css') }}" media="all" />
<link rel="stylesheet" href="{{ URL::asset('business/css/global.css') }}" media="all">
<link rel="stylesheet" href="{{ URL::asset('plugins/font-awesome/css/font-awesome.min.css') }}">
<body  onload="test()">
<form id="eva-data">
    @if($type == 0)
    <div class="star">
        <span for="">评分:</span>
        <table id ="czy">
            <tr class="star-tr">
                <td>★</td><td>★</td><td>★</td><td>★</td><td>★</td>
            </tr>
        </table>
    </div>
    <div class="eva-content">
        <span>内容:</span>
        <textarea name="content"  cols="30" rows="10" id="content"></textarea>
    </div>
    <div class="eva-btn" onclick="save()"><p>提交</p></div>
    <input type="hidden" name="user_id" value="{{ $data->user_id }}" id="user_id">
    <input type="hidden" name="product_id" value="{{ $data->product_id }}" id="product_id">
        @else
        <div>
            <label for="" class="label-eva">我对他的评价:</label>
            <div class="stared">
                <span for="">评分:</span>
                @for($i=0;$i<$bEvaluate->score;$i++)
                    <div class="eva-div">
                        ★
                    </div>
                @endfor
            </div>
            <div class="eva-content">
                <span>内容:</span>
                <textarea name="content"  cols="30" rows="10" id="content">{{ $bEvaluate->content }}</textarea>
            </div>
            @if(!empty($cEvaluate))
            <label for="" class="label-eva">他对我的评价:</label>
            <div class="stared">
                <span for="">评分:</span>
                @for($i=0;$i<$cEvaluate->score;$i++)
                    <div class="eva-div">
                        ★
                    </div>
                @endfor
            </div>
            <div class="eva-content">
                <span>内容:</span>
                <textarea name="content"  cols="30" rows="10" id="content">{{ $cEvaluate->content }}</textarea>
            </div>
                @endif
        </div>
    @endif
</form>

<script src="{{ URL::asset('js/star.js') }}"></script>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="{{ URL::asset('layui/layui.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('layui/layui.all.js') }}"></script>
@section('js')
    <script>

    </script>
    @endsection
</body>
</html>