<?php
Run::c('/other/login');
$obj = new UserOrderController();
$rs = $obj->getUserOrderByDamage();
?>
    <div class="dis_list_t">
        <p>* 损坏订单需要跟客户进行电话沟通，告知扣款情况。</p>
    </div>
<?php
$obj = new ShopTypeController();
foreach ($rs as $k => $v) {
    $tmp = $obj->getShopTypeDetail($v['stnum']);
    ?>
    <div class="set_dam_c">
        <ul class="overflow">
            <li>
                <a href="/js/bad-d/<?php echo $v['cnum']; ?>/<?php echo $v['oid']; ?>/<?php echo $v['uid']; ?>">
                    <h2>订单编号：<?php echo $v['oid']; ?></h2>
                    <p><?php echo $tmp['st_name']; ?>（损坏）</p>
                    <span><img src="/public/images/icon_12.png "/></span>
                </a>
            </li>
        </ul>

    </div>
    <?php
}

$cls = '2';
include_once 'v1.js.footer.nav.php';
?>