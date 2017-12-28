@extends('Back.index')
@section('main')
    <div class="demoTable productCat">
        <form action="">
            <select name="is_pass" id="select-type" class="order-select">
                <option value="3" <?php if($is_pass == 3)echo "selected";?>>全部</option>
                <option value="0" <?php if($is_pass == 0)echo "selected";?>>未通过</option>
                <option value="1" <?php if($is_pass == 1)echo "selected";?>>已通过</option>
                <option value="2" <?php if($is_pass == 2)echo "selected";?>>审核中</option>
            </select>
            <div class="layui-inline">
                <input class="layui-input" name="keyword" id="demoReload" autocomplete="off" placeholder="用户名或手机号">
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">时间：</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" id="test10" placeholder=" - " style="width: 300px" name="exTime" value="{{ $time }}">
                </div>
            </div>
            <button class="layui-btn" data-type="reload">搜索</button>
        </form>
        <button  onclick="excel()" class="layui-btn">导出用户</button>
    </div>
    <div class="layui-form table-data">
        <table class="layui-table">
            <colgroup>
                <col width="50">
                <col width="150">
                <col width="150">
                <col width="80">
                <col width="50">
                <col width=80">
                <col width=80">
                <col width="200">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>企业编号</th>
                <th>企业名称</th>
                <th>企业代码</th>
                <th>企业法人</th>
                <th>法人联系电话</th>
                <th>是否通过审核</th>
                <th>身份证归属地</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{ $value->number }}</td>
                    <td>
                        {{ $value->companyName }}
                    </td>
                    <td>{{ $value->companyCode }}</td>
                    <td>{{ $value->companyLegal }}</td>
                    <td>{{ $value->phone }}</td>
                    <td id="{{ $value->id }}">
                        @if($value->is_pass == 0)
                             未通过
                            @elseif($value->is_pass == 1)
                            已通过
                            @elseif($value->is_pass == 2)
                            审核中
                            @endif
                    </td>
                    <td>{{ $value->belonging }}</td>
                    <td>{{ $value->create_time }}</td>
                    <td>
                        <a href="{{ route('company.detail', ['id' => $value->id]) }}" class="layui-btn layui-btn-small">查看用户信息</a>
                        <button class="layui-btn layui-btn-small" onclick="is_pass({{ $value->id }},1)">通过审核</button>
                        <button class="layui-btn layui-btn-small layui-btn-danger" onclick="is_pass({{ $value->id }},0)">审核不通过</button>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $data->appends(['keyword' => $keyword,'is_pass'=>$is_pass,'exTime'=>$time])->links() }}
    </div>

@endsection

@section('js')
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            //日期时间范围
            laydate.render({
                elem: '#test10'
                ,type: 'datetime'
                ,range: true
            });
        });
        function excel() {
            var select_type = $('#select-type option:selected').val();
            // 关键词
            var keyword = $('#demoReload').val();

            var time = $('#test10').val();
            location.href = "/back/excel?exl=userB&selectType="+select_type+"&keyword="+keyword+"&time="+time;
//            location.href = "/back/excel?exl=userB";
        }

        /**
         * 用户是否通过审核
         * @param id
         * @param type
         */
        var message = "";
        var pass = "";

        function is_pass(id,type) {
            var icon = 2;
            if(type == 0){
                message = "确定将该用户的状态修改为"+'<span style="color:red">不通过</span>'+"？";
                pass = "未通过";
            }else{
                message = "确定将该用户的状态修改为"+'<span style="color:red">已通过</span>'+"？";
                pass = "已通过";
            }

            layer.confirm(message, {
                btn: ['确定','取消'] //按钮
                ,title:"状态修改"
            }, function(){
                $.post('/back/user/changeStatus',{id:id,is_pass:type},function (obj) {
                    if(obj.code == 200){
                        icon = 1;
                        $('#'+id).text(pass);
                    }
                    layer.msg(obj.msg,{icon:icon});
                });
            });
        }
    </script>
@endsection