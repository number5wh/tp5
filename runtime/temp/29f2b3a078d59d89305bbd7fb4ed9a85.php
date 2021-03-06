<?php /*a:2:{s:77:"C:\Users\Administrator\Desktop\tp5\application\index\view\withdraw\apply.html";i:1554870932;s:74:"C:\Users\Administrator\Desktop\tp5\application\index\view\common\base.html";i:1554870932;}*/ ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>分成提现</title>
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
    <h2>分成提现</h2>
  </div>

  <div class="layui-card-body">
    <div style="padding-bottom: 20px;">
      <h3>余额：<span id="leftmoney"><?php echo htmlentities($balance); ?></span></h3>
    </div>
    <form class="layui-form" action="<?php echo url('withdraw.doApply'); ?>" method="post">
      <div class="layui-form-item">
        <label for="" class="layui-form-label">账户类型</label>
        <div class="layui-input-block">
          <select name="checktype" lay-filter="withdraw_select">
            <option value="1" selected>支付宝</option>
            <option value="2">银行卡</option>
          </select>
        </div>
      </div>
      <div class="layui-form-item">
        <label for="" class="layui-form-label">账号</label>
        <div class="layui-input-block">
          <input type="text" name="account" id="drawaccount" value="<?php echo htmlentities($info['alipay']); ?>" lay-verify="required" placeholder="暂未绑定账号" class="layui-input" disabled>
        </div>
      </div>
      <div class="layui-form-item">
        <label for="" class="layui-form-label">提现金额</label>
        <div class="layui-input-block">
          <input type="text" name="amount" id="getmoney" onkeyup="clearNoNum(this)" value="" lay-verify="required" placeholder="请输入金额，可提现金额为<?php echo htmlentities($balance); ?>" class="layui-input" >
        </div>
      </div>
      <div class="layui-form-item">
        <label for="" class="layui-form-label">验证码</label>
        <div class="layui-input-inline">
          <input type="text" name="code" id="getcode" value="" lay-verify="required|number" placeholder="请输入验证码" class="layui-input" >
        </div>
        <div class="layui-input-inline">
          <button type="button" class="layui-btn layui-btn-normal" id="sms">获取验证码</button>
        </div>
      </div>
      <div class="layui-form-item">
        <div class="layui-input-block">
          <button type="submit" class="layui-btn" lay-submit="" lay-filter="withdraw-apply">确 认</button>
        </div>
      </div>
    </form>
  </div>
</div>


</div>

<script src="/src/layuiadmin/layui/layui.js?t=1"></script>

<script>
  function clearNoNum(obj){
    obj.value = obj.value.replace(/[^\d.]/g,"");  //清除“数字”和“.”以外的字符
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3');//只能输入两个小数
    if(obj.value.indexOf(".")< 0 && obj.value !=""){
      obj.value= parseFloat(obj.value);
    }
  }
</script>
<script>
  layui.extend({
    sendmsg:'../src/layuiadmin/mymod/sendmsg'
  }).use(['layer','table','form','jquery','sendmsg'],function () {
    var layer = layui.layer
        ,form = layui.form
        ,table = layui.table
            ,$ = layui.$
            ,sendmsg = layui.sendmsg;

    form.verify({
      amount: function(value, item){ //value：表单的值、item：表单的DOM对象
        var left = $('#leftmoney').html();
        if (isNaN(value)) {
          return '金额必须为数字';
        }
        if(parseFloat(left) < parseFloat(value)){
          return '您当前可提现余额为'+parseFloat(left)+'元';
        }
        if (parseFloat(value) < 100) {
          return '提现金额至少为100元';
        }
      }
    });

    form.on('select(withdraw_select)', function(data){
      if (data.value == 1) {
        //支付宝
        var account = "<?php echo htmlentities($info['alipay']); ?>";
      } else {
        //银行卡
        var account = "<?php echo htmlentities($info['cardaccount']); ?>";
      }
      if (!account) {
        $('#drawaccount').val("").attr('placeholder', '暂未绑定账号');
      } else {
        $('#drawaccount').val(account).attr('placeholder', '');
      }
    });

    //提现申请
    form.on('submit(withdraw-apply)', function(data) {
      var amount = parseFloat(data.field.amount).toFixed(2);
      if (isNaN(amount)) {
        layer.msg('金额必须填数字', {icon:5});
        return false;
      }
      if (amount<0) {
        layer.msg('金额必须大于0', {icon:5});
        return false;
      }
      var code = data.field.code;
      var checktype = data.field.checktype;
      if (checktype != 1 && checktype !=2) {
        layer.msg('账号类型有误', {icon:5});
        return false;
      }
      var account = data.field.account;
      if (!account) {
        layer.msg('请先绑定账号', {icon:5});
        return false;
      }

      $.ajax({
        type: 'post',
        url: data.form.action,
        data: {
          'checktype':checktype,
          'code':code,
          'amount':amount
        },
        dataType: 'json',
        success: function (res) {
          $('#getmoney').val('');
          $('#getcode').val('');
          if (res.code === 0) {
            $('#leftmoney').html(res.leftmoney);
            $('#getmoney').attr('placeholder', "请输入金额，可提现金额为"+res.leftmoney);
            layer.msg(res.msg, {icon: 6});
          } else {
            layer.msg(res.msg, {icon: 5});
          }
        }
      });
      return false;
    });

    $('#sms').on('click', function(){
      sendmsg.sendmsg('#sms',1,'');
    });
  });
</script>

</body>
</html>

