@extends('welcome')

@section('content')
    <div class="successTow">
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
            location.href = "https://www.pgyer.com/yJRp?from=singlemessage&isappinstalled=0";
        });

        $('.dowloadTow').click(function () {
            location.href = "{{ URL }}{{ $datac }}";
        });
    </script>
    @endsection