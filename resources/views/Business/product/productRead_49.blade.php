@extends('Business.header')
@section('content')
    <div class="productRead" >
        <form class="layui-form" id="product_add">
            <div class="layui-form-item">
                <label class="layui-form-label">产品编号 :</label>
                <div class="layui-input-block">
                    <input type="text" name="pNumber"  autocomplete="off" class="layui-input" value="<?php echo empty($product['pNumber']) ? $pNumber : $product['pNumber'];?>" placeholder="随机产品编号" readonly>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">收费标准 :</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="other_need_1" placeholder="￥服务费" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="other_need_2" placeholder="￥调查费" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="other_need_3" placeholder="￥其他费用" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="other_need_4" placeholder="￥保证金" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">可接受抵押方式 :</label>
                <div class="layui-input-block">
                    <select name="mortgage_type">
                        <?php $title = ['质押','抵押'];?>
                        @foreach($title as $v)
                            <option value="{{ $v }}" @if($v === $product['mortgage_type']) selected="" @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">利息 :</label>
                <div class="layui-input-block">
                    <input type="number" name="accrual"  autocomplete="off" class="layui-input" value="{{ $product['accrual'] }}" placeholder="请填写数字" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">可借款周期 :</label>
                <div class="layui-input-block">
                    <select name="product_cycle">
                        <?php $title = empty($list[0]) ? ['3','6','9','12','18']: $list[0]['product_cycle'];?>
                        @foreach($title as $v)
                            <option value="{{ $v }}" @if($v === $product['product_cycle']) selected="" @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">额度 :</label>
                <div class="layui-input-block">
                    <select name="money">
                        <?php $title = empty($list[0]) ? ['0-30','30-50','50-70','100以上'] : $list[0]['money'];?>
                        @foreach($title as $v)
                            <option value="{{ $v }}" @if($v === $product['money']) selected="" @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">还款方式 :</label>
                <div class="layui-input-block">
                    <select name="lending_type">
                        <?php $title = empty($list[0]) ? ['先息后本','先本后息','等额本息'] : $list[0]['lending_type'];?>
                        @foreach($title as $v)
                            <option value="{{ $v }}" @if($v === $product['lending_type']) selected="" @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">审核周期 :</label>
                <div class="layui-input-block">
                    <input type="text" name="audit_time"  autocomplete="off" class="layui-input" value="{{ $product['audit_time'] }}" placeholder="单位（天）" required>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">估值率 :</label>
                <div class="layui-input-block">
                    <input type="text" name="property_cut"  autocomplete="off" class="layui-input" value="{{ $product['property_cut'] }}">
                </div>
            </div>

            @if($cat_id != 41 && !empty($list[0]))
                <div class="layui-form-item">
                    <label class="layui-form-label">类型 :</label>
                    <div class="layui-input-block">
                        <select name="type" required>
                            <?php $title = empty($list[0]) ? [] : $list[0]['type'];?>
                            @foreach($title as $v)
                                <option value="{{ $v }}" @if($v === $product['type']) selected="" @endif>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="layui-form-item">
                <label class="layui-form-label">地区范围</label>
                <div class="layui-input-inline">
                    <select name="province" lay-filter="province">
                        <option value="">请选择</option>
                        <option value="不限" <?php if($diqu->province == "不限" ){echo "selected";}?>>不限</option>
                        @foreach($province as $v)
                            <option value="{{ $v->provinceID }}" <?php if($diqu->province == $v->province ){echo "selected";}?>>{{ $v->province }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline" lay-filter="citydata" >
                    <select name="city"  lay-filter="city" id="city">
                        <option value="">请选择</option>
                        <option value="不限" <?php if($diqu->city == "不限" ){echo "selected";}?>>不限</option>
                        @foreach($city as $v)
                            <option value="{{ $v->cityID }}" <?php if($diqu->city == $v->city ){echo "selected";}?>>{{ $v->city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="diqu" id="area">
                        <option value="">请选择</option>
                        <option value="不限" <?php if($diqu->district == "不限" ){echo "selected";}?>>不限</option>
                        @foreach($district as $v)
                            <option value="{{ $v->areaID }}" <?php if($diqu->district == $v->area ){echo "selected";}?>>{{ $v->area }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否上门 :</label>
                <div class="layui-input-block">
                    <select name="is_home">
                        <?php $title = ['是','否'];?>
                        @foreach($title as $v)
                            <option value="{{ $v }}" >{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">征信要求 :</label>
                <div class="layui-input-block">
                    <select name="credit" required>
                        <?php $title = ['一年内逾期超过3次或超过90天','一年内逾期少于3次且少于90天','信用良好无逾期'];?>
                        @foreach($title as $v)
                            <option value="{{ $v }}" @if($v === $product['credit']) selected="" @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">产权年限要求 :</label>
                <div class="layui-input-block">
                    <input type="radio" name="years" value="1" title="有" <?php if(!empty($product['years'])){ echo "checked";}?>>
                    <input type="radio" name="years" value="0" title="无" <?php if(empty($product['years'])){ echo "checked";}?>>
                </div>
                <div class="layui-input-inline" style="width: 300px;">

                    <input type="text" name="year_content" placeholder="如果有产权年限请填写" autocomplete="off" class="layui-input" value="{{ empty($product['life']) ? $product['years'] : $product['life']}}">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">其他要求 :</label>
                <div class="layui-input-block">
                    <input type="radio" name="other" value="1" title="有" <?php if(!empty($product['other'])){ echo "checked";}?>>
                    <input type="radio" name="other" value="0" title="无" <?php if(empty($product['other'])){ echo "checked";}?>>
                </div>
                <div class="layui-input-inline" style="width: 300px;">
                    <input type="text" name="other_content" placeholder="如果有其他要求请填写" autocomplete="off" class="layui-input" value="{{ $product['other']}}">
                </div>
            </div>
            <div class="xieyi">
                <input type="radio" name="xieyi" value="1" title="阅读并已同意" checked="">
            </div>
            @if($type === '1')
                <input type="hidden" name="cat_id" value="{{ $cat_id }}">
                <span class="layui-btn layui-btn-normal read-btn" onclick="save()" style="margin: 0 0 5px 400px!important">添加</span>
            @elseif($type === '2')
                <input type="hidden" name="id" value="{{ $id }}">
                <span class="layui-btn layui-btn-normal read-btn" onclick="save()" style="margin: 0 0 5px 400px!important">修改</span>
            @endif
        </form>
    </div>
@endsection
@section('js')
    <script>
        function save() {
            $.post('/business/productSave',$('#product_add').serialize(),function (obj) {
                if(obj.code == 200){
                    layer.msg(obj.msg);
                    setTimeout(function () {
                        location.reload();
                        layer.closeAll();
                    },1000)
                }
            })
        }

        layui.use('form', function(){
            var form = layui.form;
            //各种基于事件的操作，下面会有进一步介绍
            form.on('select(province)', function(data){
                var father = data.value;
                $.post('/business/city',{father:father},function (obj) {
                    $('#city').empty();
                    $('#city').append(obj.data);
                    form.render()
                });
            });

            form.on('select(city)', function(data){
                var father = data.value;
                $.post('/business/area',{father:father},function (obj) {
                    $('#area').empty();
                    $('#area').append(obj.data);
                    form.render()
                });
            });
        });
    </script>
@endsection
