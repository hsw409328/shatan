<?php
Run::c('/user/login');
$obj = new BuyGoodsListController();
$rs = $obj->getUserOrder();
?>
<div class="use_pay_success">
    <div class="use_pay_succt">
        <img src="/public/images/icon_11.png"/>
        <p>支付成功</p>
    </div>
    <div class="background1"></div>
    <div class="use_pay_sxx">
        <dl>
            <dt>取货柜编号：<span><?php echo $rs['rs']['cnum']; ?></span></dt>
            <dd>租赁有效期：<?php echo Run::getFormatDate($rs['rs']['rent_date_start'], 'm月d日 H:i'); ?>
                至 <?php echo Run::getFormatDate($rs['rs']['rent_date_end'], 'm月d日 H:i'); ?></dd>
            <a href="/user/order"><img src="/public/images/icon_12.png"></a>
        </dl>

        <table class="use_pay_mx">
            <tr>
                <th>宝贝明细</th>
                <th>取货密码</th>
            </tr>
            <tr>
                <td><?php echo $rs['odRs']['gnum_name']; ?></td>
                <td><?php echo floatval($rs['odRs']['pwd']); ?></td>
            </tr>
        </table>

    </div>
    <div class="background1"></div>
    <p class="use_ts">请在柜子上输入取货密码，即可取出宝贝！<br/>记得按时来归还喔~</p>
    <div class="submit_order">
        <a href="/">完成</a>
    </div>
</div>
