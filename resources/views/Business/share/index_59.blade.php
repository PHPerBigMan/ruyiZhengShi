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
                                <input type="text" name="money" id="money" autocomplete="off" class="layui-input share-input" value="{{ !empty($data->need_data->money) ? $data->need_data->money : "" }}">
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
                            <label class="layui-form-label">接收利息范围</label>
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
                            <label class="layui-form-label">还本付息方式</label>
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
                            <label class="layui-form-label">期望估值折扣范围</label>
                            <div class="layui-input-inline">
                                <input type="text" name="discount" id="discount" autocomplete="off" class="layui-input share-input" value="{{ !empty($data->need_data->discount) ? $data->need_data->discount : "" }}">
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
                            <label class="layui-form-label">法院起诉</label>
                            <div class="layui-input-inline">
                                <select name="Fayuan"  class="share-select">
                                    <?php $title = ['无','已结案','未结案']?>
                                    @foreach($title as $v)
                                        <option value="{{ $v }}" <?php if(!empty($data->data->Fayuan) && $data->data->Fayuan == $v)echo "selected";?>>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">其他平台授信</label>
                            <div class="layui-input-inline">
                                <select name="otherPin"  class="share-select">
                                    <option value="有" <?php if(!empty($data->need_data->otherPin) && $data->need_data->otherPin == '有')echo "selected";?>>有</option>
                                    <option value="无" <?php if(!empty($data->need_data->otherPin) && $data->need_data->otherPin == '无')echo "selected";?>>无</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">其他平台还款</label>
                            <div class="layui-input-inline">
                                <select name="otherHuan"  class="share-select">
                                    <option value="无逾期" <?php if(!empty($data->need_data->otherHuan) && $data->need_data->otherHuan == '无逾期')echo "selected";?>>无逾期</option>
                                    <option value="有逾期" <?php if(!empty($data->need_data->otherHuan) && $data->need_data->otherHuan == '有逾期')echo "selected";?>>有逾期</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form id="share-table3">
                    <div class="layui-form-item">
                        <label class="layui-form-label">位置</label>
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
                    <div class="layui-form">
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
                                <label class="layui-form-label">芝麻信用积分</label>
                                <div class="layui-input-inline">
                                    <select name="Jifen"  class="share-select">
                                        <?php $title = ['100-200','200-300','300-400','400以上']?>
                                        @foreach($title as $v)
                                            <option value="{{ $v }}" <?php if(!empty($data->data->Jifen) && $data->data->Jifen == $v)echo "selected";?>>{{ $v }}</option>
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
    <input type="hidden" id="cat_id" value="59">
@endsection
@section('js')
    <script type="text/html" id="barDemo1">
        <a class="layui-btn layui-btn-mini" lay-event="read">&nbsp; 查看 &nbsp;</a>
    </script>
    <script src="{{ URL::asset('js/layer-table.js') }}"></script>
    <script src="{{ URL::asset('js/share.js') }}?_v=22"></script>
@endsection
