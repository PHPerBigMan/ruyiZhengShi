@extends('Back.index')
@section('main')
  <div class="box-body">
    <div class="btn-group-vertical">
      <button type="button" class="btn btn-info" onclick="add(0)">新增分类</button>

    </div>
    <table id="example2" class="table table-bordered table-hover">
      <thead>
      <tr>
        <th>贷款类别</th>
        <th>分类图标</th>
        <th>添加时间</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody>
      @foreach($data as $v1)
      <tr>
        <td>{{ $v1->cat_name }}</td>
        <td><img src="{{$v1->cat_pic}}" class="mini_pic"></td>
        <td>{{ $v1->create_time }}</td>
        <td>
          <div class="tools">
            <i class="fa fa-edit" onclick="edit({{ $v1->id }})"></i>
            |
            <i class="fa fa-trash-o" onclick="del( '{{$v1->id}}','{{$v1->cat_name}}',this )"></i>
          </div>

        </td>
      </tr>
        @endforeach
      </tbody>

    </table>
  </div>

@endsection

@section('js')
  <script>
    $(function () {
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false
      });
    });

    /*删除分类*/
    function del(id,cat_name,td){
      layer.confirm('是否删除分类:'+cat_name,{title:'删除提示',btn:['是','否']},function(){
        $.post('/back/product/cat_del',{id:id,_token:'{{ csrf_token()}}'},function(obj){
          if(obj == 200){
            layer.msg('删除分类：'+"【"+cat_name+"】"+' 成功!',{time:2000});
            setTimeout(function(){
              location.reload();
            },2000);
          }
        })
      })
    }

    /*分类编辑*/
    function edit(id){
      layer_open('分类编辑','/back/product/edit?id='+id,660,440)
    }

    function add(id) {
        layer_open('分类添加','/back/product/edit?id='+id,660,440)
    }

  </script>
  @endsection