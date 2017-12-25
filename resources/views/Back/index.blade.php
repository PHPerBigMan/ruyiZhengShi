@extends('Back.header')
@section('content')

  <body>
  <div class="layui-layout layui-layout-admin">
    <div class="layui-header">
      <div class="layui-logo">如易金服</div>
      <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item">
          <a href="javascript:;">
              <?php use Illuminate\Support\Facades\DB;echo session('admin_user');?>
          </a>
        </li>
        <li class="layui-nav-item"><a href="/back/loginout">退出</a></li>
      </ul>
    </div>
    <div class="layui-side layui-bg-black">
      <div class="layui-side-scroll">
        <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
        <ul class="layui-nav layui-nav-tree"  lay-filter="test">
          <li class="layui-nav-item {{ Request::is('back/dashboard') ? 'layui-this' : '' }}">
            <a href="/back/dashboard">首页</a>
          </li>
            <?php
            $title = [
                'ProductCatList'
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">产品分类</a>
            <dl class="layui-nav-child ">
              <dd class="<?php if($Pagetitle == 'ProductCatList'){echo 'layui-this';}?>"><a href="/back" @click="secondCat()" id="">分类列表</a></dd>
            </dl>
          </li>
            <?php
            $title = [
                'userC',
                'userB',
                'userIntegral'
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">用户</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'userC'){echo 'layui-this';}?>"><a href="/back/user/1" @click="secondCat()" id="">C端用户列表</a></dd>
              <dd class="<?php if($Pagetitle == 'userB'){echo 'layui-this';}?>"><a href="/back/user/2" @click="secondCat()" id="">B端用户列表</a></dd>
              <dd class="<?php if($Pagetitle == 'userIntegral'){echo 'layui-this';}?>"><a href="/back/integral" @click="secondCat()" id="">用户积分</a></dd>
            </dl>
          </li>
            <?php
            $title = [
                'orderBasic',
                'orderShare',
                'orderPassed',
                'orderNoPassed',
                'orderDone',
                'ordercancel',
                'orderbcancel',
                'ordertui',
                'orderAll'
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>" >
            <a class="" href="javascript:;">订单列表</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'orderBasic'){echo 'layui-this';}?>"><a href="/back/order/1"  id="">B端支付待审核</a></dd>
              <dd class="<?php if($Pagetitle == 'orderShare'){echo 'layui-this';}?>"><a href="/back/order/0"  id="">C端支付待审核</a></dd>
              <!--<dd class="<?php if($Pagetitle == 'orderPassed'){echo 'layui-this';}?>"><a href="/back/order/2" id="">审核已通过订单</a></dd>-->
              <dd class="<?php if($Pagetitle == 'orderNoPassed'){echo 'layui-this';}?>"><a href="/back/order/3" id="">审核未通过订单</a></dd>
              <dd class="<?php if($Pagetitle == 'ordercancel'){echo 'layui-this';}?>"><a href="/back/order/5">C端用户取消订单</a></dd>
              <dd class="<?php if($Pagetitle == 'orderbcancel'){echo 'layui-this';}?>"><a href="/back/order/6">B端用户取消订单</a></dd>
              <dd class="<?php if($Pagetitle == 'ordertui'){echo 'layui-this';}?>"><a href="/back/order/7">退款订单</a></dd>
              <dd class="<?php if($Pagetitle == 'orderDone'){echo 'layui-this';}?>"><a href="/back/order/8" id="">成交订单</a></dd>
              <dd class="<?php if($Pagetitle == 'orderAll'){echo 'layui-this';}?>"><a href="/back/order/4" id="">所有订单</a></dd>
            </dl>
          </li>

            <?php
            $title = [
                'new',
                'show',
                'list',
                'unpass',
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">黑名单</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'new'){echo 'layui-this';}?>"><a href="/back/black/1" @click="secondCat()" id="">新增审核</a></dd>
              <dd class="<?php if($Pagetitle == 'show'){echo 'layui-this';}?>"><a href="/back/black/2" @click="secondCat()" id="">上架审核</a></dd>
              <dd class="<?php if($Pagetitle == 'list'){echo 'layui-this';}?>"><a href="/back/black/3" @click="secondCat()" id="">黑名单列表</a></dd>
              <dd class="<?php if($Pagetitle == 'unpass'){echo 'layui-this';}?>"><a href="/back/black/4" @click="secondCat()" id="">审核未过</a></dd>
            </dl>
          </li>
            <?php
            $title = [
                'setting',
                'settingServe'
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">数据系数配置</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'setting'){echo 'layui-this';}?>"><a href="/back/setting" @click="secondCat()" id="">匹配分数及共享条件</a></dd>
              <dd class="<?php if($Pagetitle == 'settingServe'){echo 'layui-this';}?>"><a href="/back/setting/serve" @click="secondCat()" id="">服务费比例</a></dd>
            </dl>
          </li>
            <?php
            $title = [
                'data'
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">成交额统计</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'data'){echo 'layui-this';}?>"><a href="/back/typeChoose?type=1">数据统计</a></dd>
            </dl>
          </li>
            <?php
            $title = [
                'article'
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">消息列表</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'article'){echo 'layui-this';}?>"><a href="/back/article">编辑文本</a></dd>
            </dl>
          </li>
            <?php
            $title = [
                'admin',
            ];
            ?>
          <li class="layui-nav-item <?php if(in_array($Pagetitle,$title)){echo 'layui-nav-itemed';}?>">
            <a class="" href="javascript:;">后台账号管理</a>
            <dl class="layui-nav-child">
              <dd class="<?php if($Pagetitle == 'admin'){echo 'layui-this';}?>"><a href="/back/admin">账号管理</a></dd>
            </dl>
          </li>


        </ul>
      </div>
    </div>

  </div>

  <div class="main">
    <div class="main-content">
      @yield('main')
    </div>
  </div>
  </body>
  </html>
@endsection