@extends('Business.index')

@section('laybar')
    <div id="Cat">
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">产品分类</a>
                        <dl class="layui-nav-child">
                            @foreach($product_cat as $v)
                                <dd><a href="/business/share/{{ $v->id }}/{{ $v->sec_id }}" class="<?php if($cat_id == $v->id){ echo "layui-this";}?>">{{ $v->cat_name }}</a></dd>
                            @endforeach
                        </dl>
                    </li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">共享订单</a>

                        <dl  class="layui-nav-child">
                            <dd><a href="/business/sharePay">订单列表</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body">
            <!-- 内容主体区域 -->
            <div class="b-nav-bar">
                  <span class="layui-breadcrumb">
                  <a><cite>如易共享</cite></a>
                  <a><cite>产品分类</cite></a>
                  </span>
            </div>
            <hr>
            <div class="product-cat-nav">
                @foreach($secCat as $value)
                    <a href="/business/share/{{ $value->p_id }}/{{ $value->id }}">
                        <div class="layui-btn  <?php if($sec != $value->id){ echo "cat-btn";}?>" onclick="sec({{ $value->id }})" id="{{ $value->id }}" >
                            <span>
                                {{ $value->cat_name }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="form-data">

                <div class="share-btns" lay-filter="add">
                    <button class="layui-btn layui-btn-radius" @click="add">填写共享信息</button>
                </div>
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>公司编号</th>
                        <th>利息</th>
                        <th>还本付息方式</th>
                        <th>是否上门</th>
                        <th>匹配度</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody class="share-tbody">

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="layui-tab share-table" style="display: none;">
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
                                <input type="text" name="name"  autocomplete="off" class="layui-input share-input" value="{{ $basic->name }}">
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
                                <input type="text" name="money"  autocomplete="off" class="layui-input share-input" id="money" value="{{ !empty($data->need_data->money) }}">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">借款周期</label>
                            <div class="layui-input-inline">
                                <select name="product_cycle"  class="share-select">
                                    @foreach($property->product_cycle as $v)
                                        <option value="{{ $v }}" <?php if(!empty($data->need_data->product_cycle) && $data->need_data->product_cycle == $v)echo "selected";?>>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">接受利息范围</label>
                            <div class="layui-input-inline">
                                <select name="accrual"  class="share-select">
                                    @foreach($property->accrual as $v)
                                        <option value="{{ $v }}" <?php if(!empty($data->need_data->accrual) && $data->need_data->accrual == $v)echo "selected";?>>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">期望放款周期</label>
                            <div class="layui-input-inline">
                                <select name="lending_type"  class="share-select">
                                    @foreach($property->lending_cycle as $v)
                                        <option value="{{ $v }}" <?php if(!empty($data->need_data->lending_cycle) && $data->need_data->lending_cycle == $v)echo "selected";?>>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">还款方式</label>
                            <div class="layui-input-inline">
                                <select name="lending_type"  class="share-select">
                                    @foreach($property->lending_type as $v)
                                        <option value="{{ $v }}" <?php if(!empty($data->need_data->lending_type) && $data->need_data->lending_type == $v)echo "selected";?>>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">可接受其他费用</label>
                            <div class="layui-input-inline">
                                <select name="is_issue"  class="share-select">
                                    <option value="是" <?php if(!empty($data->need_data->is_issue) && $data->need_data->is_issue == '是')echo "selected";?>>是</option>
                                    <option value="否" <?php if(!empty($data->need_data->is_issue) && $data->need_data->is_issue == '否')echo "selected";?>>否</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">期望估值折扣范围</label>
                            <div class="layui-input-inline">
                                <input type="text" name="discount"  autocomplete="off" class="layui-input share-input" value="{{ !empty($data->need_data->discount) }}">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">抵押方式</label>
                            <div class="layui-input-inline">
                                <select name="mortgage_type"  class="share-select">
                                    <option value="质押" <?php if(!empty($data->need_data->mortgage_type) && $data->need_data->mortgage_type == '质押')echo "selected";?>>质押</option>
                                    <option value="抵押" <?php if(!empty($data->need_data->mortgage_type) && $data->need_data->mortgage_type == '抵押')echo "selected";?>>抵押</option>
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
                            <label class="layui-form-label">车辆类型</label>
                            <div class="layui-input-inline">
                                <select name="type"  class="share-select">
                                    @foreach($property->type as $v)
                                        <option value="{{ $v }}" <?php if(!empty($data->data->type) && $data->data->type == $v)echo "selected";?>>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">车牌所属地</label>
                        <select name="province" lay-filter="province" class="share-select-small province" onchange="Province()" id="province">
                            <option value="">请选择</option>
                            @foreach($province as $v)
                                <option value="{{ $v->provinceID }}" <?php if(!empty($data->data->area) && $data->data->area[0] == $v->province)echo "selected";?>>{{ $v->province }}</option>
                            @endforeach
                        </select>
                        <select name="city"  lay-filter="city" id="city" class="share-select-small city" onchange="City()">
                            <option value="">请选择</option>
                            @foreach($city as $v)
                                <option value="{{ $v->cityID }}" <?php if(!empty($data->data->area) && $data->data->area[1] == $v->city)echo "selected";?>>{{ $v->city }}</option>
                            @endforeach
                        </select>
                        <select name="diqu" id="area" class="share-select-small">
                            <option value="">请选择</option>
                            @foreach($district as $v)
                                <option value="{{ $v->areaID }}" <?php if(!empty($data->data->area) && $data->data->area[2] == $v->area)echo "selected";?>>{{ $v->area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">品牌</label>
                            <div class="layui-input-inline">
                                <input type="text" name="brand"  autocomplete="off" class="layui-input share-input" value="{{ !empty($data->data->brand) }}">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">个人征信</label>
                            <div class="layui-input-inline">
                                <select name="credit"  class="share-select">
                                    <option value="无逾期" <?php if(!empty($data->data->credit) && $data->data->credit == '无逾期')echo "selected";?>>无逾期</option>
                                    <option value="有逾期" <?php if(!empty($data->data->credit) && $data->data->credit == '有逾期')echo "selected";?>>有逾期</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">是否有抵押</label>
                            <div class="layui-input-inline">
                                <select name="mortgage"  class="share-select">
                                    <option value="无" <?php if(!empty($data->data->mortgage) && $data->data->mortgage == '无')echo "selected";?>>无</option>
                                    <option value="有" <?php if(!empty($data->data->mortgage) && $data->data->mortgage == '有')echo "selected";?>>有</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">是否被冻结</label>
                            <div class="layui-input-inline">
                                <select name="frozen"  class="share-select">
                                    <option value="是" <?php if(!empty($data->data->frozen) && $data->data->frozen == '是')echo "selected";?>>是</option>
                                    <option value="否" <?php if(!empty($data->data->frozen) && $data->data->frozen == '否')echo "selected";?>>否</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">保险期限</label>
                            <div class="layui-input-inline">
                                <input type="text" name="bx"  autocomplete="off" class="layui-input share-input" value="{{ !empty($data->data->bx) }}">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">共有人</label>
                            <div class="layui-input-inline">
                                <input type="text" name="owner"  autocomplete="off" class="layui-input share-input" value="{{ !empty($data->data->owner) }}">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">是否可装gps</label>
                            <div class="layui-input-inline">
                                <select name="gps"  class="share-select">
                                    <option value="是" <?php if(!empty($data->data->gps) && $data->data->gps == '是')echo "selected";?>>是</option>
                                    <option value="否" <?php if(!empty($data->data->gps) && $data->data->gps == '否')echo "selected";?>>否</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-upload" id="layui-upload-black">
                        <label class="layui-form-label">汽车登记证</label>

                        <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                            <button type="button" class="layui-btn" id="add-cardj">添加图片</button>
                            <div class="layui-upload-list" id="share-img-cardj">
                                @if(!empty($data->data->cardj))
                                    <img src="{{ $data->data->cardj }}" alt="" class="share-img" >
                                @endif
                            </div>
                            <input type="hidden" name="cardj" id="cardj" value="<?php if(isset($data->data->cardj))echo $data->data->cardj;?>">
                            {{--<img src="" alt="">--}}
                        </blockquote>

                    </div>
                    <div class="layui-upload" id="layui-upload-black">
                        <label class="layui-form-label">行驶证</label>

                        <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                            <button type="button" class="layui-btn" id="add-carxs">添加图片</button>
                            <div class="layui-upload-list" id="share-img-carxs">
                                @if(!empty($data->data->carxs))
                                    <img src="{{ $data->data->carxs }}" alt="" class="share-img" >
                                @endif
                            </div>
                            <input type="hidden" name="carxs" id="carxs" value="<?php if(isset($data->data->carxs))echo $data->data->carxs;?>">
                        </blockquote>

                    </div>
                    <div class="layui-upload" id="layui-upload-black">
                        <label class="layui-form-label">驾驶证</label>

                        <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                            <button type="button" class="layui-btn" id="add-carjs">添加图片</button>
                            <div class="layui-upload-list" id="share-img-carjs">
                                @if(!empty($data->data->carjs))
                                    <img src="{{ $data->data->carjs }}" alt="" class="share-img" name="carjs">
                                @endif
                            </div>
                            <input type="hidden" name="carjs" id="carjs" value="<?php if(isset($data->data->carjs))echo $data->data->carjs;?>">
                        </blockquote>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">备注</label>
                            <div class="layui-input-inline">
                                <textarea placeholder="填写是否有重大维修" class="layui-textarea share-textarea"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <button class="layui-btn share-btn">立即匹配</button>
    </div>
    <input type="hidden" id="cat_id" value="{{$sec}}">
@endsection
@section('js')
    <script type="text/html" id="barDemo1">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>
    <script src="{{ URL::asset('js/layer-table.js') }}"></script>
    <script src="{{ URL::asset('js/share.js') }}?_v=18"></script>
@endsection
