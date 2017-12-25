@extends('Back.index')
@section('main')
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
        <legend>商户端用户详细信息</legend>
    </fieldset>
    <form id="userData">
        <table class="layui-table" lay-skin="line">
            <colgroup>
                <col width="150">
                <col width="450">
            </colgroup>
            <tbody>
            <tr>
                <td>企业编号</td>
                <td>{{ $user->number }}</td>
            </tr>
            <tr>
                <td>企业名称</td>
                <td><input type="text" value="{{ $user->companyName }}" class="layui-input" name="companyName"></td>
            </tr>
            <tr>
                <td>企业代码</td>
                <td><input type="text" value="{{ $user->companyCode }}" class="layui-input" name="companyCode"></td>
            </tr>
            <tr>
                <td>企业法人</td>
                <td><input type="text" value="{{ $user->companyLegal }}" class="layui-input" name="companyLegal"></td>
            </tr>
            <tr>
                <td>法人联系电话</td>
                <td><input type="text" value="{{ $user->phone }}" class="layui-input" name="phone"></td>
            </tr>
            <tr>
                <td>是否通过审核</td>
                <td>{{ $user->is_pass == 0 ? "未通过" :$user->is_pass == 1 ? "已通过" : "审核中" }}</td>
            </tr>
            <tr>
                <td>注册时间</td>
                <td>{{ $user->create_time }}</td>
            </tr>
            <tr>
                <td>金融管家</td>
                <td><input type="text" value="{{ $user->companyHouse }}" class="layui-input" name="companyHouse"></td>
            </tr>
            <tr>
                <td>管家电话</td>
                <td><input type="text" value="{{ $user->companyHousePhone }}" class="layui-input" name="companyHousePhone"></td>
            </tr>
            <tr>
                <td>是否拥有金融资质</td>
                <td>{{ $user->qualification }}</td>
            </tr>
            <tr>
                <td>企业执照</td>
                <td>
                    <div class="layui-upload">
                        <button type="button" class="layui-btn" id="test1">上传图片</button>
                        <div class="layui-upload-list">
                            <img src="{{ $user->pic }}" alt="" width="400px" class="img" id="pic">
                            <p id="demoText"></p>
                            <input type="hidden" id="savePic" name="pic" value="{{ $user->pic }}">
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <td>成交单数</td>
                <td>{{ $order->orders_count }}</td>
            </tr>
            <tr>
                <td>总成交金额</td>
                <td>{{ $order->total_money or 0}}</td>
            </tr>
            </tbody>
        </table>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name="id" value="{{ $user->id }}">
                <p class="layui-btn submit">立即提交</p>
            </div>
        </div>
    </form>

@endsection
@section('js')
    <script>
        $('.img').zoomify();

        $('.submit').click(function () {
            layer.confirm('确认修改该企业信息？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post('/back/saveUserInfo',$('#userData').serialize(),function (obj) {
                    layer.closeAll();
                    if(obj.code == 200){
                        layer.msg(obj.msg,{type:1});
                        setTimeout(function () {
                            location.reload()
                        },1000);
                    }else{
                        layer.msg(obj.msg,{type:2});
                    }
                })
            });
        });


        layui.use('upload', function(){
            var $ = layui.jquery
                ,upload = layui.upload;

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#test1'
                ,url: '/back/savePic'
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('#pic').attr('src', result); //图片链接（base64）
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.code > 0){
                        return layer.msg('上传失败');
                    }
                    //上传成功
                    $('#savePic').val(res.data);
                }
                ,error: function(){
                    //演示失败状态，并实现重传
                    var demoText = $('#demoText');
                    demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                    demoText.find('.demo-reload').on('click', function(){
                        uploadInst.upload();
                    });
                }
            })

        });
    </script>
    @endsection