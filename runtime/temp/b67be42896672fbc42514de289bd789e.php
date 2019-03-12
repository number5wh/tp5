<?php /*a:2:{s:79:"C:\Users\Administrator\Desktop\tp5\application\index\view\account\addProxy.html";i:1552355137;s:74:"C:\Users\Administrator\Desktop\tp5\application\index\view\common\base.html";i:1552028062;}*/ ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增代理</title>
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
    <h2>新增代理</h2>
  </div>

  <div class="layui-card-body">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
      <legend>基本信息</legend>
    </fieldset>
    <form class="layui-form" action="<?php echo url('account.doAddProxy'); ?>" method="post">
      <?php echo token(); ?>

      <div class="layui-form-item">
          <label class="layui-form-label">账号：</label>
          <div class="layui-input-block">
            <input type="text" name="account" placeholder="请输入账号" autocomplete="off" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
          <label class="layui-form-label">密码：</label>
          <div class="layui-input-block">
            <input type="password" name="password" placeholder="请输入密码" autocomplete="off" class="layui-input">
          </div>
      </div>
      <div class="layui-form-item">
          <label class="layui-form-label">确认密码：</label>
          <div class="layui-input-block">
            <input type="password" name="password_confirm" placeholder="请确认密码" autocomplete="off" class="layui-input">
          </div>
      </div>
      <div class="layui-form-item">
          <label class="layui-form-label">分成比例：</label>
          <div class="layui-input-block">
            <input type="text" name="rate" placeholder="请输入分成比例" autocomplete="off" class="layui-input">
          </div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label">备注：</label>
        <div class="layui-input-block">
          <input type="text" name="comment" placeholder="请输入12字内备注" autocomplete="off" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
          <button type="submit" class="layui-btn layui-btn-radius" lay-submit="" lay-filter="account-doAddProxy">保存</button>
          <button type="reset" class="layui-btn layui-btn-primary layui-btn-radius" lay-submit="" lay-filter="">重置</button>
        </div>
      </div>

    </form>
  </div>
</div>


</div>

<script src="/src/layuiadmin/layui/layui.js?t=1"></script>

<script>
  layui.use(['layer','table','form'],function () {
    var layer = layui.layer
            ,form = layui.form
            ,table = layui.table;
    //用户表格初始化
  });
</script>

</body>
</html>

