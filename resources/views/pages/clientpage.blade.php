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
            location.href = "http://pgyer.com/2wBS";
        })
    </script>
    @endsection