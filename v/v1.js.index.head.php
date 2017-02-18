<?php
Run::c('/other/login');
if ($_sessio_str == 1) {
    ParamsController::localSetParams('js_cabinet_bag', RouteClass::getParams('3'));
}
$obj = new CabinetController();
$cnum = $obj->getCabinetBagNum();
$rs = $obj->getCabinetDetail($cnum);
$u = $obj->getCabinetPsUser($cnum);
?>
<div class="sorting_detalis_t">
    <dl class="overflow">
        <dt><?php echo $cnum; ?></dt>
        <dd class="sorting_det_bt"><strong><span><?php echo $rs['c_hotel']; ?></span>
                <span><?php echo $rs['c_address']; ?></span></strong></dd>
        <dd><span><?php echo $rs['c_city']; ?></span>·<span><?php echo $rs['c_area']; ?></span></dd>
        <dd class=" set_details">
            <h3>配送</h3>
            <p><?php echo $u['u_name'];?></p>
        </dd>
    </dl>
</div>