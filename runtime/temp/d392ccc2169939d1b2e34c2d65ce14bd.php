<?php /*a:2:{s:73:"C:\Users\Administrator\Desktop\tp5\application\index\view\index\home.html";i:1555574182;s:74:"C:\Users\Administrator\Desktop\tp5\application\index\view\common\base.html";i:1554870932;}*/ ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>主页</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/src/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/src/layuiadmin/style/admin.css" media="all">
    
<link rel="stylesheet" href="/static/css/index/home.css" media="all">

</head>
<body>

<div class="layui-fluid">
    
<div class="layui-row layui-col-space30">
    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                下级代理
                <span class="layui-badge layui-bg-blue layuiadmin-badge">全部</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="proxynum">0</p>
            </div>
        </div>
    </div>
    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                注册玩家
                <span class="layui-badge layui-bg-blue layuiadmin-badge">全部</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="playernum">0</p>
            </div>
        </div>
    </div>
    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                总充值
                <span class="layui-badge layui-bg-blue layuiadmin-badge">全部</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="totalfee">0</p>
            </div>
        </div>
    </div>
    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                总业绩
                <span class="layui-badge layui-bg-blue layuiadmin-badge">全部</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="totaltax">0</p>
            </div>
        </div>
    </div>


    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                总利润
                <span class="layui-badge layui-bg-blue layuiadmin-badge">全部</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="totalin">0</p>
            </div>
        </div>
    </div>

    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                昨日充值
                <span class="layui-badge layui-bg-green layuiadmin-badge">昨日</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="yesterdayfee">0</p>
            </div>
        </div>
    </div>
    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                昨日业绩
                <span class="layui-badge layui-bg-green layuiadmin-badge">昨日</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="yesterdaytax">0</p>
            </div>
        </div>
    </div>
    <div class="layui-col-md3 layui-col-lg3 layui-col-xs12 layui-col-sm6 ">
        <div class="layui-card">
            <div class="layui-card-header">
                昨日利润
                <span class="layui-badge layui-bg-green layuiadmin-badge">昨日</span>
            </div>
            <div class="layui-card-body layuiadmin-card-list">
                <p class="layuiadmin-big-font" id="yesterdayin">0</p>
            </div>
        </div>
    </div>

    <div class="layui-col-md12 layui-col-lg12 layui-col-xs12 layui-col-sm12">
        <div class="layui-row layui-col-space10" style="text-align: left;">
            <div class="layui-col-md2 layui-col-lg1 layui-col-xs3 layui-col-3 ">
                <i class="layui-icon">&#xe612;</i>&nbsp;&nbsp;&nbsp;&nbsp;当前在线
            </div>
            <div class="layui-col-md10 layui-col-lg11 layui-col-xs9 layui-col-9" style="font-weight: bold">
                <span id="online">0</span>位
            </div>
        </div>
    </div>

    <div class="layui-col-md12 layui-col-lg12 layui-col-xs12 layui-col-sm12">
        <div class="layui-card">
            <div class="layui-card-header layuiadmin-card-header-auto" style="text-align: left">数据概览</div>
            <div class="layui-card-body">
                <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview">
                    <div carousel-item id="LAY-index-dataview">
                        <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="layui-row layui-col-space30" style="text-align: center;">



</div>

</div>

<script src="/src/layuiadmin/layui/layui.js?t=1"></script>

<script>
    layui.config({
        base: '/src/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
        ,home: '../mymod/home'
    }).use(['index', 'home']);
</script>

</body>
</html>

