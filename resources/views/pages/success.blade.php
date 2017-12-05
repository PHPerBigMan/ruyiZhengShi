@extends('welcome')

@section('content')
    <div class="success">
        <div class="suc-top">
            <img src="/img/icon_done@2x.png" alt="">
            <h2>恭喜！注册成功</h2>
            <p>请点击下方按钮，下载如易金融App</p>
        </div>
        <div class="suc-bot" style="position: relative">
            <img src="/img/icon_tuwen@2x.png" class="Suc-Img-one" alt="">
            <img src="/img/icon_tuwen@2x.png" alt=""class="Suc-Img-tow">
        </div>

        <div class="suc-btn">点击下载</div>
    </div>
@endsection