<?php
Run::c('/user/login');
$bg1 = ParamsController::getSessionParams('bg1');
if ($bg1 != 'bg1') {
    Run::show_msg('', '1', '/buy/goods');
}
ParamsController::unsetSession('bg1');

$_params = explode('-', RouteClass::getParams('3'));
$i = 3;
foreach ($_params as $k => $v) {
    RouteClass::setParams($i, $v);
    $i++;
}

$obj = new BuyGoodsListController();
//提前创建订单
$rs = $obj->createOrder();
$user = $obj->getUserDetail();
?>
<div class="use_pay_t">
    柜内商品有限，请尽快支付喔~ 避免被其他用户选择。
</div>
<div class="use_pay_b">
    <div class="use_pay_x">
        <h3>租赁柜编号：<?php echo RouteClass::getParams('3'); ?></h3>
        <p>订单编号：<?php echo $rs['oRs']['oid']; ?></p>
        <p>租赁有效期：<?php echo Run::getFormatDate($rs['oRs']['rent_date_start'], 'm月d日 H:i'); ?>
            至 <?php echo Run::getFormatDate($rs['oRs']['rent_date_end'], 'm月d日 H:i'); ?></p>
    </div>
    <table class="use_table">
        <tr>
            <th>宝贝明细</th>
            <th>金额</th>
            <th>备注</th>
        </tr>
        <tr>
            <td><?php echo $rs['odRs']['gnum_name']; ?></td>
            <td><?php echo floatval($rs['odRs']['price']); ?>元</td>
            <td>需要归还</td>
        </tr>
        <tr>
            <td>押金</td>
            <td><?php echo $rs['oRs']['deposit_money']; ?>元</td>
        </tr>
        <tr>
            <td>总额</td>
            <td><?php echo $rs['oRs']['total_money']; ?>元</td>
        </tr>
    </table>
    <div class="background1"></div>
    <div class="use_pay_u">
        <p class="use_pay_ch"><label>称呼</label><?php echo $user['u_nickname']; ?></p>
        <p class="use_pay_sj"><label>手机号码</label><?php echo $user['mobile_num']; ?></p>
    </div>
    <div class="background1"></div>
    <div class="use_pay_remin">
        <h2>归还提醒</h2>
        <p>默认租期为<?php echo RouteClass::getParams('6'); ?>天，请于<strong><?php echo Run::getFormatDate($rs['oRs']['rent_date_end'], 'Y年m月d日 H:i'); ?></strong>前归还，租取、归还请在同一个柜子上操作完成。
        </p>
        <p>押金会在归还后的48小时内退还至您的微信钱包。</p>
    </div>
</div>
<div class="use_order_next">
    <p>金额：<span><?php echo $rs['oRs']['total_money']; ?></span> <strong>元</strong></p>
    <a href="javascript:void(0);" class="paycls">立即支付</a>
</div>
<script>
    var _sub = 0;
    var _e = '';
    $('.paycls').click(function () {
        //测试
        //window.location.href = "/buy/success/<?php echo $rs['oRs']['oid']; ?>";
        //return false;
        if (_sub == 1) {
            return false;
        }
        _sub = 1;
        AjaxCommon.data = {
            "action": "BuyGoodsList",
            "run": "pay",
            "oid": "<?php echo $rs['oRs']['oid']; ?>",
        };
        AjaxCommon.callback_func = function (data, status) {
            var _d = eval('(' + data + ')');
            if (_d.code == '1') {
                _e = _d.msg;
                pay();
            } else {
                alert(_d.msg);
                UtilCommon.back();
                _sub = 0;
                return false;
            }
        };
        AjaxCommon.post();
    });
    var pay = function () {
        WeixinJSBridge.invoke('getBrandWCPayRequest', {
            "appId": _e.appid, // 公众号名称，由商户传入
            "timeStamp": _e.timestamp + "", // 时间戳，自1970年以来的秒数
            "nonceStr": _e.newRndstr, // 随机串
            "package": "prepay_id=" + _e.prepay_id,
            "signType": "MD5", // 微信签名方式:
            "paySign": _e.newSign
            // 微信签名
        }, function (res) {
            // 使用以下方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回 ok，但并不保证它绝对可靠。
            if (res.err_msg == "get_brand_wcpay_request:ok") {
                alert('支付成功');
                window.location.href = "/buy/success/<?php echo $rs['oRs']['oid']; ?>";
            } else {
                _sub = 0;
                alert('支付失败，请重新进行操作');
                window.location.href = "/buy/goods/";
                // WeixinJSBridge.invoke('closeWindow', {}, function(res){});
            }
        });
    }
</script>