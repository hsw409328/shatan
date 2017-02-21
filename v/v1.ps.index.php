<?php
$u = Run::c('/other/login');
$obj = new CabinetStsController();
$obj->initDayCabinet();

?>
<div class="dis_list_t">
    <p>* 工作流程及操作规范，点击<a href="#">查看详情>></a></p>
</div>
<ul class="dis_list_zong">
    <?php
    $cabinet_list = $obj->getUserManagerCabinet();
    $cabinetObj = new CabinetController();
    foreach ($cabinet_list as $k => $v) {
        $_tmp_detail = $cabinetObj->getCabinetDetail($v['c_num'], 'c_hotel,c_address');
        $pickDate = $obj->getCabinetPickGoodsStsEnd($v['c_num'], $u['id']);
        $gsrDate = $obj->getGoodsSupplyRecordEnd($v['c_num'], $u['id']);
        ?>
        <li>
            <a href="/ps/d/<?php echo $v['c_num']; ?>">
                <dl>
                    <dt>
                    <h2><?php echo $v['c_num']; ?></h2>
                    <span><?php echo $_tmp_detail['c_hotel']; ?></span>
                    <span><?php echo $_tmp_detail['c_address']; ?></span>
                    </dt>
                    <dd>
                        <strong>最近操作记录:</strong>
                        <span>补货：<?php echo $gsrDate; ?></span>
                        <span>取货：<?php echo $pickDate; ?></span>
                    </dd>
                </dl>
            </a>
        </li>
        <?php
    }
    ?>


</ul>