@extends('welcome')

@section('content')
    <div class="success">
        <span ></span>
        <div>
            <button class="dowloadOne"></button>
            <botton class="dowloadTow"></botton>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('.dowloadOne').click(function () {
            location.href = "https://www.pgyer.com/2wBS?from=singlemessage&isappinstalled=0";
        });
    </script>
    @endsection