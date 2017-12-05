@extends('Business.header')

@section('content')
    <form class="layui-form" id="saveData">
        <div class="layui-form-item">
            <label class="layui-form-label">公司名称</label>
            <div class="layui-input-block">
                <input type="text" name="companyName" lay-verify="required" autocomplete="off" placeholder="请输入标题" class="layui-input" value="{{ $businessData->companyName }}" <?php if(!empty( $businessData->companyName)){echo "disabled";}?>>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">公司代码</label>
            <div class="layui-input-block">
                <input type="text" name="companyCode" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $businessData->companyCode }}" <?php if(!empty( $businessData->companyCode)){echo "disabled";}?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">公司地址</label>
            <div class="layui-input-block">
                <input type="text" name="companyAddress" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $businessData->companyAddress }}" <?php if(!empty( $businessData->companyAddress)){echo "disabled";}?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">公司法人</label>
            <div class="layui-input-block">
                <input  type="text" name="companyLegal" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $businessData->companyLegal }}" <?php if(!empty( $businessData->companyLegal)){echo "disabled";}?>>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">金融管家</label>
            <div class="layui-input-block">
                <input type="text" name="companyHouse"  placeholder="请输入" autocomplete="off" class="layui-input" value="{{ $businessData->companyHouse }}">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">管家联系电话</label>
                <div class="layui-input-inline">
                    <input type="tel" name="companyHousePhone"  autocomplete="off" class="layui-input" id="phone">
                </div>
            </div>
            <input type="button" class="layui-btn msg-btn" value="获取短信">
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">验证码</label>
                <div class="layui-input-inline">
                    <input type="tel" name="code" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">是否有金融资质 </label>
            <div class="layui-input-inline">
                <select name="qualification" lay-verify="required" <?php if(count($businessData->qualification)!= 0){echo "disabled";}?>>
                    <option value="">请选择</option>
                    <option value="1" <?php if($businessData->qualification == 1){echo "selected";}?>>是</option>
                    <option value="0" <?php if($businessData->qualification == 0){echo "selected";}?>>否</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">所属类型 </label>
            <div class="layui-input-inline">
                <select name="type" lay-verify="required" <?php if(!empty($businessData->type)){echo "disabled";}?>>
                    <option value="">请选择</option>
                    @foreach($data as $v)
                        <option value="{{ $v->id }}" <?php if($businessData->type == $v->id){echo "selected";} ?>>{{ $v->cat_name }}</option>
                        @endforeach
                </select>
            </div>
        </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" class="layui-textarea" name="remark" <?php if(!empty($businessData->remark)){echo "disabled";}?>>
                    {{ $businessData->remark }}
                </textarea>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">营业执照上传</label>
            <div class="layui-input-block">
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="admin_img" <?php if(!empty($businessData->pic)){echo 1;}?>>上传图片</button>
                    <div class="layui-upload-list">
                        @if(!empty($businessData->pic))
                            <img src="{{ $businessData->pic }}" alt="" class="admin-img">
                            @endif
                        <img class="layui-upload-img admin-img" id="img" >
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <input type="hidden" class="img" name="pic">
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
                ,layer = layui.layer

            //监听提交
            form.on('submit(demo1)', function(){
                $.post('/business/adminSave',$('#saveData').serialize(),function (obj) {
                    if(obj.code == 200){
                        layer.msg(obj.msg,{icon:1});
                    }else{
                        layer.msg(obj.msg,{icon:2})
                    }
                });
                return false;
            });
        });

        //上传图片
        layui.use('upload', function(){
            var $ = layui.jquery
                ,upload = layui.upload;

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#admin_img'
                ,url: '/business/adminImg'
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('#img').attr('src', result); //图片链接（base64）
                    });
                }
                ,done: function(res){
                    //将返回的图片路径保存至隐藏域中
                  $('.img').val(res.data)
                }
            });
        });

    </script>
@endsection
