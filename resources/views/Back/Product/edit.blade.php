@extends('Back.header')
@section('content')
  <div class="box-body">
    <form class="layui-form" id="product_cat">
      <div class="layui-form-item">
        <label class="layui-form-label">分类名称</label>
        <div class="layui-input-block">
          <input type="text" name="title"  placeholder="请输入标题" class="layui-input" value="{!! $data->cat_name !!}">
        </div>
      </div>
      <div class="layui-form-item">
        <div class="layui-upload">
          <label class="layui-form-label">分类图标</label>
          <button type="button" class="layui-btn" id="test1">上传图片</button>
          <div class="layui-upload-list">
            <img class="layui-upload-img" id="cat_pic">
            <p id="demoText"></p>
            <input type="hidden" name="cat_pic" id="cat_save_pic" value="">
          </div>
        </div>
      </div>
      <div class="layui-form-item">
        <div class="layui-input-block">
          {{--<button class="layui-btn" lay-filter="submit" onclick="save()">立即提交</button>--}}
          <input type="button" value="立即提交" class="layui-btn" onclick="save()" lay-filter="submit">
          <input type="hidden" name="id" value="{{ $data->id }}">
        </div>
      </div>
    </form>
  </div>
  @endsection
@section('js')
  <script>
      function save() {
          console.log( $('#cat_save_pic').val());
          $.post('/back/product/save',{'_token':"{{ csrf_token() }}",'data':$('#product_cat').serializeArray(),'pic': $('#cat_save_pic').val()},function (obj) {
              if(obj == 200){
                  layer.msg('操作成功',{icon:1,time:1000});
              }
          });

      }
    /*表单*/
    layui.use(['form', 'layedit', 'laydate'], function(){
      var form = layui.form
              ,layer = layui.layer
              ,layedit = layui.layedit
              ,laydate = layui.laydate;

      form.verify({
        title: function(value){
          if(value.length == 0){
            return '请输入分类名称';
          }
        }
      });

    });

    /*图片上传*/
    layui.use('upload', function(){
      var $ = layui.jquery
              ,upload = layui.upload;
      //普通图片上传
      var uploadInst = upload.render({
        elem: '#test1'
        ,url: '/back/product/cat_pic'
        ,data:{'_token':'{{ csrf_token() }}'}
        ,before: function(obj){
          //预读本地文件示例，不支持ie8
          obj.preview(function(index, file, result){
            $('#cat_pic').attr('src', result); //图片链接（base64）
            $('#cat_pic').css({'width':'100','height':'100','margin-left':'100px'});
          });
        }
        ,done: function(res){
          //如果上传失败
          //上传成功 保存图片路径
              console.log(res.url);
          $('#cat_save_pic').val(res.url);

        }
        ,error: function(){
          //演示失败状态，并实现重传
          var demoText = $('#demoText');
          demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
          demoText.find('.demo-reload').on('click', function(){
            uploadInst.upload();
          });
        }
      });

    });

    $(function(){
      $('.layui-form-label').css({'width':'100'})
    });


  </script>
@endsection