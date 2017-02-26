<?php
$u = Run::c('/user/login');
$obj = new UserOrderController();
$rs = $obj->getUserOrderDamageByOid(RouteClass::getParams('3'));
if (empty($rs)) {
    Run::show_msg('未找到损坏订单');
}
$odRs = $obj->getUserOrderDetail($rs['oid'], $rs['uid']);
?>
<div class="use_set_t">
    商品损坏处理
</div>
<table class="use_my_table use_set_table" cellpadding="0" cellspacing="0">
    <tr>
        <th>宝贝明细</th>
        <th>商品编号</th>
        <th>金额</th>
        <th>验收</th>
    </tr>
    <tr>
        <td class="color"> <?php echo $odRs['gnum_name']; ?></td>
        <td class="color"><?php echo $odRs['gnum']; ?></td>
        <td class="color"><?php echo floatval($odRs['price']); ?>元</td>
        <td class="color"> 损坏</td>
    </tr>
</table>
<div class="use_set_kk">
    <span>扣款</span><strong><?php echo floatval($rs['damage_money']); ?>元</strong>
</div>
<div class="use_set_kk">
    <p>
        <?php
        echo $rs['damage_explain'];
        ?>
    </p>
</div>
<ul class="use_set_img">
    <?php
    $_img = explode(',', $rs['damage_img']);
    foreach ($_img as $k => $v) {
        if (!empty($v)) {
            echo '<li><img src="' . $v . '"/></li>';
        }
    }
    ?>
</ul>
<p class="use_set_time">2016-01-01 19:00</p>
<div class="use_set_dj">
    <h2>损坏定价规范</h2>
    <p>
        <?php
        $stObj = new ShopTypeController();
        $rs1 = $stObj->getShopTypeDetail($odRs['stnum']);
        echo $rs1['st_bad_info'];
        ?>
    </p>
</div>
<div class="use_set_re">
    <a href="/user/order">返回</a>
</div>