@extends('Business.header')
@section('content')
    <div class="yinlian-message-1">
        <label>订单信息</label>
        <div class="dingdan-message">
            <div>
                <label for="">公司：</label>
                <span>{{ $orderData->number }}</span>
            </div>
            <div>
                <label for="">产品覆盖区域：</label>
                <span>{{ $orderData->area }}</span>
            </div>
            <div>
                <label for="">担保品范围：</label>
                <span>{{ $orderData->type }}</span>
            </div>

            <div>
                <label for="">产品利率：</label>
                <span>{{ $orderData->accrual }}</span>
            </div>
            <div>

                <label for="">订单号：</label>
                <span>{{ $orderData->order_id }}</span>
            </div>
        </div>
        <hr class="yinlian-hr">

    </div>
    <div class="yinlian-message-2">
        <label>收款账户</label>
        <div class="dingdan-message">
            <div>
                <span>{{ $yinlian->bank }}</span>
            </div>
            <div>
                <label for="">账号：</label>
                <span>{{ $yinlian->account }}</span>
            </div>
            <div>
                <label for="">姓名：</label>
                <span>{{ $yinlian->name }}</span>
            </div>

        </div>
        <hr class="yinlian-hr">

    </div>
   <div class="yinlian-message-2">
       <label>转账凭证截图</label>
       <div class="layui-upload" id="layui-upload-black">
           <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
               <button type="button" class="layui-btn" id="add-img">添加图片</button>
               <div class="layui-upload-list" id="black-img"></div>
               @if(!empty($data->imgs))
                   <div class="layui-upload-list" id="black-img">
                       @foreach($data->imgs as $vo)
                           <img src="{{ $vo }}" alt="" class="black-img">
                       @endforeach
                   </div>
               @endif
           </blockquote>
       </div>
   </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <input type="hidden" id="imgs" name="imgs">
            <input type="hidden" id="order_id" name="" value="{{ $order_id }}">
            <input type="hidden" id="type" name="" value="{{ $type }}">
            <button class="layui-btn black-btn" lay-submit="" lay-filter="go">确认提交</button>
        </div>
    </div>
@endsection
@section('js')
    <script>
        layui.use('upload', function(){
            var img = [];
            var $ = layui.jquery
                ,upload = layui.upload;
            //多图片上传
            upload.render({
                elem: '#add-img'
                ,url: '/business/blackImg'
                ,multiple: true
                ,before: function(obj){
                    layer.load(1, {time: 1000});
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('#black-img').append('<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img black-img">')
                    });
                }
                ,done: function(res){
                    //将返回的数据追加到数组中
                    img.push(res.data);
                    //上传完毕
                    $('#imgs').val(img);
                    console.log(img);
                }
            });
        });

        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                ,layer = layui.layer

            //监听提交
            form.on('submit(go)', function(data){
                var imgs = $('#imgs').val();
                var order_id =  $('#order_id').val();
                var type =  $('#type').val();
                $.post('/business/yinlianSave',{imgs:imgs,order_id:order_id,type:type},function (obj) {
                    if(obj.code == 200){
                        layer.msg(obj.msg,{icon:1});
                    }else{
                        layer.msg(obj.msg,{icon:2})
                    }
                });
                return false;
            });

        });
    </script>
    @endsection
