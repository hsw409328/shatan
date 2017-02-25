<?php
Run::c('/other/login');
$cnum = RouteClass::getParams('3');
$obj = new CabinetController();
$rs = $obj->getCabinetDetail($cnum);
$u = $obj->getCabinetPsUser($cnum);
$obj = new UserOrderController();
$oRs = $obj->getUserOrderOne(RouteClass::getParams('4'), RouteClass::getParams('5'));
if (empty($oRs)) {
    Run::show_msg('未找到订单');
}
$odRs = $obj->getUserOrderDetail(RouteClass::getParams('4'), RouteClass::getParams('5'));
if (empty($odRs)) {
    Run::show_msg('未找到订单详情');
}
$obj = new UsersController();
$uRs = $obj->getUserByUid($oRs['uid']);
?>
<div class="sorting_detalis_t">
    <dl class="overflow">
        <dt><?php echo $cnum; ?></dt>
        <dd class="sorting_det_bt"><strong><span><?php echo $rs['c_hotel']; ?></span>
                <span><?php echo $rs['c_address']; ?></span></strong></dd>
        <dd><span><?php echo $rs['c_city']; ?></span>·<span><?php echo $rs['c_area']; ?></span></dd>
        <dd class=" set_details">
            <h3>配送</h3>
            <p><?php echo $u['u_name']; ?></p>
        </dd>
    </dl>
</div>
<div class="use_myorder margin60">
    <div class="use_myorder_dd">
        <p>订单编号：<?php echo $oRs['oid']; ?></p>
        <p>租赁有效期：<?php echo Run::getFormatDate($oRs['rent_date_start'], 'm月d日 H:i'); ?>
            至 <?php echo Run::getFormatDate($oRs['rent_date_end'], 'm月d日 H:i'); ?></p>
        <p>用户归还时间：未归还</p>
        <p class="color1">用户昵称：<?php echo $uRs['u_nickname']; ?></p>
        <p class="color1">联系电话：<?php echo $uRs['mobile_num']; ?></p>
    </div>
    <table class="use_my_table " cellpadding="0" cellspacing="0">
        <tr>
            <th>宝贝明细</th>
            <th>商品编码</th>
            <th>金额</th>
            <th style="padding-right:0">验收</th>
        </tr>
        <tr>
            <td><?php echo $odRs['gnum_name']; ?></td>
            <td class="color"><?php echo $odRs['gnum']; ?></td>
            <td><?php echo floatval($odRs['price']); ?>元</td>
            <td><a href="/js/bad-d-next/<?php echo $oRs['oid']; ?>" class="set_details_xz">损坏</a></td>
        </tr>
        <tr class="table_border">
            <td colspan="4">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>订单押金</td>
            <td></td>
            <td><?php echo floatval($oRs['deposit_money']); ?>元</td>
        </tr>
        <tr>
            <td>超时归还扣款</td>
            <td></td>
            <td><?php echo floatval($oRs['overtime_money']); ?>元</td>
        </tr>
        <tr>
            <td>损坏扣款</td>
            <td></td>
            <td><?php echo floatval($oRs['damage_money']); ?>元</td>
        </tr>
        <tr>
            <td class="color">实际退还押金</td>
            <td></td>
            <td class="color"><?php echo floatval($oRs['real_refund_money']); ?>元</td>
        </tr>
    </table>
</div>
<div class="set_details_b ">
    <a href="javascript:void(0)" class="set_yjs">已结算</a>
</div>