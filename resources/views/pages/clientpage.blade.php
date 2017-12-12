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
            location.href = "https://www.pgyer.com/yJRp";
        });

        $('.dowloadTow').click(function () {
            location.href = "{{ URL }}{{ $datac }}";
        });
    </script>
    @endsection