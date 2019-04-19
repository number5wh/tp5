<?php /*a:2:{s:77:"C:\Users\Administrator\Desktop\tp5\application\index\view\template\index.html";i:1554870932;s:74:"C:\Users\Administrator\Desktop\tp5\application\index\view\common\base.html";i:1554870932;}*/ ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>游戏分享</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/src/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/src/layuiadmin/style/admin.css" media="all">
    
</head>
<body>

<div class="layui-fluid">
    
<div class="layui-container" style="float: left">
    <input type="text" id="tempid" value="1" hidden>
    <div class="layui-row layui-col-space30">
        <img id="pic"  src="../<?php echo htmlentities($pic); ?>" class="layui-col-md4 layui-col-lg4 layui-col-xs12 layui-col-sm12">
    </div>
    <div class="layui-row layui-col-space30" style="text-align: center">
        <div class="layui-col-sm6 layui-col-md2 layui-col-lg2 layui-col-xs6" >
            <a id="refresh_pic" href="javascript:;"><i class="layui-icon layui-icon-refresh"></i><cite>刷新</cite></a>
        </div>
        <div class="layui-col-sm6 layui-col-md2 layui-col-lg2 layui-col-xs6">
            <a id="download_pic" href="<?php echo htmlentities($down_url); ?>"><i class="layui-icon layui-icon-download-circle"></i><cite>下载</cite></a>
        </div>
    </div>
    <div class="layui-row layui-col-space40">

        <div class="layui-inline layui-col-sm8 layui-col-md3 layui-col-lg3 layui-col-xs8" >
            <input type="text" id="qrcode" value="<?php echo htmlentities($short_url); ?>" placeholder="二维码链接地址"  class="layui-input">
        </div>
        <div class="layui-col-sm4 layui-col-md1 layui-col-lg1 layui-col-xs4">
            <button class="layui-btn" id="getqrcode">复制链接</button>
        </div>
    </div>
</div>



</div>

<script src="/src/layuiadmin/layui/layui.js?t=1"></script>

<script>
    layui.extend({
        sendmsg:'../src/layuiadmin/mymod/sendmsg'
    }).use(['layer','table','form', 'jquery'],function () {
        var layer = layui.layer
            ,form = layui.form
            ,table = layui.table
            ,$    = layui.$;

        $('#refresh_pic').on('click', function() {
            var tempid = parseInt($('#tempid').val());
            $.ajax({
                type:'get',
                url:"<?php echo url('template.generate'); ?>",
                data:{
                    'tempid': tempid
                },
                dataType:'json',
                success: function(res) {
                    if (res.code === 0) {
                        $('#pic').attr('src', "../"+res.pic);
                        $('#tempid').val(res.tempid);
                        $('#qrcode').val(res.short_url);
                        var u = navigator.userAgent;
                        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
                        if (isiOS) {
                            $('#download_pic').attr('href', 'javascript:;');
                        } else {
                            $('#download_pic').attr('href', "<?php echo url('template/save'); ?>/tempid/"+res.tempid);
                        }

                    }
                }
            });
        });

        $('#download_pic').on('click', function() {
            var u = navigator.userAgent;
            var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
            if (isiOS) {
                layer.msg('请长按图片保存');
            }
        });

        $('#getqrcode').on('click', function() {
            // var Url=document.getElementById("qrcode");
            // Url.select(); // 选择对象
            // document.execCommand("Copy"); // 执行浏览器复制命令
            // layer.msg('复制成功');

            const range = document.createRange();
            range.selectNode(document.getElementById('qrcode'));

            const selection = window.getSelection();
            if(selection.rangeCount > 0) selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            layer.msg('复制成功');

        });
    });
</script>

</body>
</html>

