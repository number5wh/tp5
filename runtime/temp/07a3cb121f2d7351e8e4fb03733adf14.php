<?php /*a:2:{s:81:"C:\Users\Administrator\Desktop\tp5\application\index\view\account\playerList.html";i:1552297218;s:74:"C:\Users\Administrator\Desktop\tp5\application\index\view\common\base.html";i:1552028062;}*/ ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>玩家列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/src/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/src/layuiadmin/style/admin.css" media="all">
    
</head>
<body>

<div class="layui-fluid">
    
<div class="layui-card">
  <div class="layui-card-header layuiadmin-card-header-auto">
    <h2>玩家列表</h2>
    <form class="layui-form" action="<?php echo url('account.searchPlayer'); ?>" method="post">
      <div class="layui-inline">
        <div class="layui-input-inline" style="width: 200px;">
          <input type="text" name="userid" placeholder="玩家账号" autocomplete="off" class="layui-input">
        </div>
      </div>

      <div class="layui-inline">
        <div class="layui-input-inline" style="width: 150px;">
          <button type="submit" class="layui-btn" lay-submit="" lay-filter="search-player">搜索</button>
        </div>
      </div>
    </form>
  </div>

  <div class="layui-card-body">
    <table id="playerlist" lay-filter="playerlist"></table>
  </div>
</div>


</div>

<script src="/src/layuiadmin/layui/layui.js?t=1"></script>

<script type="text/html" id="playerlist-bar">
  <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
</script>
<script>
  layui.use(['layer','table','form','jquery'],function () {
    var layer = layui.layer
        ,form = layui.form
        ,table = layui.table
            ,$ = layui.$;
    //用户表格初始化
    var dataTable = table.render({
      elem: '#playerlist'
      , height: 500
      , url: "<?php echo url('account.playerListData'); ?>" //数据接口
      , where: {}
      , page: true //开启分页
      , cols: [[ //表头
         {field: 'id', title: 'ID', sort: true, width: 80}
        , {field: 'userid', title: '账号'}
        , {field: 'room', title: '房间'}
        , {field: 'totalfee', title: '总充值'}
        , {field: 'total_tax', title: '总业绩'}
        , {field: 'leftmoney', title: '余额'}
        // , {field: 'comment', title: '备注'}
        , {fixed: 'right', title:'操作', align:'center', toolbar: '#playerlist-bar'}
      ]]
    });

    table.on('tool(playerlist)', function(obj) {
      var data = obj.data //获得当前行数据
          ,layEvent = obj.event //获得 lay-event 对应的值
          ,tr = obj.tr; //获得当前行 tr 的DOM对象
      if (layEvent === 'edit') {
        layer.msg('你点击了编辑, 当前用户id='+data.id);
      }

    });

    //搜索玩家账号
    form.on('submit(search-player)', function(data) {
      var userid = $.trim(data.field.userid);

      table.render({
        elem: '#playerlist'
        , height: 500
        , url: "<?php echo url('account.searchPlayer'); ?>" //数据接口
        ,method:"post"
        , where: {'userid':userid}
        , page: true //开启分页
        , cols: [[ //表头
          {field: 'id', title: 'ID', sort: true, width: 80}
          , {field: 'userid', title: '账号'}
          , {field: 'room', title: '房间'}
          , {field: 'totalfee', title: '总充值'}
          , {field: 'total_tax', title: '总业绩'}
          , {field: 'leftmoney', title: '余额'}
          // , {field: 'comment', title: '备注'}
          , {fixed: 'right', title:'操作', align:'center', toolbar: '#playerlist-bar'}
        ]]
      });
      return false;
    });
  });
</script>

</body>
</html>

