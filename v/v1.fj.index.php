<?php
Run::c('/other/login');
//优化成定时操作
$obj = new CabinetStsController();
$obj->initDayCabinet();

?>
<div class="dis_list_t">
    <p>* 工作流程及操作规范，点击<a href="javascript:void(0)" class="check_details">查看详情>></a></p>
</div>
<ul class="sorting">
    <?php
    $cabinet_list = $obj->getUserManagerCabinet();
    $cabinetObj = new CabinetController();
    foreach ($cabinet_list as $k => $v) {
        $_tmp_detail = $cabinetObj->getCabinetDetail($v['c_num'], 'c_hotel,c_address');
        ?>
        <li>
            <a href="/fj/d/<?php echo $v['c_num']; ?>">
                <dl>
                    <dt><?php echo $v['c_num']; ?></dt>
                    <dd><?php echo $_tmp_detail['c_hotel']; ?></dd>
                    <dd><?php echo $_tmp_detail['c_address']; ?></dd>
                </dl>
                <p>
                    <?php echo $obj->getStaticTodayCabinetSts($v['sorting_sts']); ?>
                </p>
            </a>
        </li>
        <?php
    }
    ?>
</ul>