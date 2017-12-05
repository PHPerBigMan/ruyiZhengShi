@extends('Back.index')
@section('content')

  <div class="layui-form table-data">
      <table class="layui-table">
          <colgroup>
              <col width="150">
              <col width="150">
              <col width="150">
              <col width="200">
              <col>
          </colgroup>
          <thead>
          <tr>
              <th>ID</th>
              <th>分类名称</th>
              <th>分类等级</th>
              <th>添加时间</th>
              <th>操作</th>
          </tr>
          </thead>
          <tbody>
          @foreach($productCat as $value)
          <tr class="{{ $value->id }}">
              <td>{{ $value->id }}</td>
              <td id="{{ $value->id }}">{{ $value->cat_name }}</td>
              <td>二级分类</td>
              <td>{{ $value->create_time }}</td>
              <td>
                  <button class="layui-btn layui-btn-small" onclick="edit({{ $value->id }},'{{$value->cat_name}}')">编辑分类</button>
                  <button class="layui-btn layui-btn-small layui-btn-danger" onclick="del({{ $value->id }},'{{ $value->cat_name }}')">删除分类</button>
              </td>
          </tr>
              @endforeach
          </tbody>
      </table>
  </div>

@endsection

@section('js')
  <script>
      function sCat(id) {
          layer_open('二级分类','/back/product/SecCat/'+id,'880','660');
      }

      /**
       * 修改分类
       * @param id
       * @param cat_name
       */

      function edit(id,cat_name) {
          layer.prompt({title: '修改分类', formType: 3,value:cat_name}, function(value, index){
              $.post('/back/product/CatAdd',{type:0,cat_name:value,id:id},function (obj) {
                  if(obj.code == 200){
                      $('#'+id).text(value);
                      setTimeout(function () {
                          location.reload();
                      },1000);
                  }else{
                      layer.msg("数据未改动");
                  }
              });
              layer.close(index);
          });
      }

      /**
       * 删除分类
       * @param id
       * @param cat_name
       */

      function del(id,cat_name) {
          var type = 2;
          layer.confirm('确定删除分类【'+cat_name+'】？', {
              btn: ['确定','取消'] //按钮
          }, function(){
              $.post('/back/product/sec_cat_del',{id:id},function (obj) {
                  if(obj.code == 200){
                      type = 1;
                      //删除对应的 tr
                      $('.'+id).remove();
                  }
                  layer.msg(obj.msg,{icon:type})
              });
          });
      }
  </script>
@endsection