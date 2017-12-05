@extends('Business.header')
@section('content')
    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this">基础资料</li>
            <li>需求资料</li>
            <li>担保品资料</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show share-basic">
                <form id="share-table1">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">共享人</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name"  autocomplete="off" class="layui-input share-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">共享人ID</label>
                        <div class="layui-input-inline">
                            <input type="text" name="number"  id="number" autocomplete="off" class="layui-input share-input" disabled value="{{ $company->number }}">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">共享条件</label>
                        <div class="layui-input-inline">
                            <input type="text" name="share"  id="share" autocomplete="off" class="layui-input share-input" disabled value="{{ $share }}">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">联系方式</label>
                        <div class="layui-input-inline">
                            <input type="text" name="phone" id="phone" autocomplete="off" class="layui-input share-input" disabled value="{{ $company->companyHousePhone }}">
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form id="share-table2">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">借款金额</label>
                        <div class="layui-input-inline">
                            <input type="text" name="money"  autocomplete="off" class="layui-input share-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">借款周期</label>
                        <div class="layui-input-inline">
                            <select name="product_cycle"  class="share-select">
                                @foreach($property->product_cycle as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">接收利息范围</label>
                        <div class="layui-input-inline">
                            <select name="accrual"  class="share-select">
                                @foreach($property->accrual as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">还本付息方式</label>
                        <div class="layui-input-inline">
                            <select name="lending_type"  class="share-select">
                                @foreach($property->lending_type as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">期望估值折扣范围</label>
                        <div class="layui-input-inline">
                            <select name="discount"  class="share-select">
                                {{--@foreach($property->discount as $v)--}}
                                    {{--<option value="">{{ $v }}</option>--}}
                                {{--@endforeach--}}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">可接受其他费用</label>
                        <div class="layui-input-inline">
                            <select name="is_issue"  class="share-select">
                                <option value="是">是</option>
                                <option value="否">否</option>
                            </select>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="layui-tab-item">
            <form id="share-table3">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">担保品位置</label>
                        <div class="layui-input-inline">
                            <input type="text" name="area"  autocomplete="off" class="layui-input share-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">面积</label>
                        <div class="layui-input-inline">
                            <input type="text" name="measure"  autocomplete="off" class="layui-input share-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">装修程度</label>
                        <div class="layui-input-inline">
                            <select name="decoration"  class="share-select">
                                @foreach($property->decorate as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">房屋类型</label>
                        <div class="layui-input-inline">
                            <select name="type"  class="share-select">
                                @foreach($property->property as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">中文版</label>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test1" placeholder="yyyy-MM-dd">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">产权年限</label>
                        <div class="layui-input-inline">
                            <select name="years"  class="share-select">
                                @foreach($property->life as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">产权证号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="title_card"  autocomplete="off" class="layui-input share-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">个人征信</label>
                        <div class="layui-input-inline">
                            <select name="credit"  class="share-select">
                                <option value="有">有</option>
                                <option value="无">无</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">是否有抵押</label>
                        <div class="layui-input-inline">
                            <select name="mortgage"  class="share-select">
                                <option value="有">有</option>
                                <option value="无">无</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">是否被冻结</label>
                        <div class="layui-input-inline">
                            <select name="frozen"  class="share-select">
                                <option value="是">是</option>
                                <option value="否">否</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">房产证类型</label>
                        <div class="layui-input-inline">
                            <select name="certificate_type"  class="share-select">
                                @foreach($property->house_type as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <button class="layui-btn share-btn">立即匹配</button>
    </div>
@endsection
@section('js')
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //常规用法
            laydate.render({
                elem: '#test1'
            });
        });

        $('.share-btn').click(function () {
            var number = $('#number').val();
            var phone = $('#phone').val();
            var share = $('#share').val();
            var share_table_data = $('#share-table1').serialize()+"&"+$('#share-table2').serialize()+"&"+$('#share-table3').serialize()+"&number="+number+"&phone="+phone+"&share="+share;
            /*layer.closeAll();
            location.href = "/business/share/"+share_table_data;*/
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
            location.href = "http://ruyi.hwy.sunday.so/business/share/"+share_table_data;
            console.log("http://ruyi.hwy.sunday.so/business/share/business/share/"+share_table_data);
        })
    </script>
    @endsection
