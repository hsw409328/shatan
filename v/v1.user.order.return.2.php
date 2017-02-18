<?php
Run::c('/user/login');
$bg1 = ParamsController::getSessionParams('uor2');
if ($bg1 != 'uor2') {
    Run::show_msg('','1','/user/order');
}
ParamsController::unsetSession('uor2');
$_id = Run::req('oid');
$_content = Run::req('content');
?>
<div class="use_pay_t center">
    第二步：归还大疆无人机
</div>
<div class="use_return_next">
    <!--大疆无人机的图片-->
    <img src="/public/images/process_wrj.jpg" width="100%"/>
    <!--普通商品图片-->
    <!--<img src="/public/images/process_pt.jpg" width="100%"/>-->
</div>
<div class="use_return_re">
    <a href="javascript:void(0);" class="use_return_g">已投入回收箱</a>
    <a href="/user/order" class="use_return_n">取消</a>
</div>
<script>
    $('.use_return_g').click(function () {
        AjaxCommon.data = {
            'action': 'UserOrder',
            'run': 'returnUserOrder',
            'oid': "<?php echo $_id;?>",
            'content': "<?php echo $_content;?>",
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = eval('(' + data + ')');
            if (_d.code == '1') {
                UtilCommon.href('/user/order-return-3');
            } else {
                alert(_d.msg);
                return false;
            }
        };
        AjaxCommon.post();
    });
</script>