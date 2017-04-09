<?php
Run::c('/user/login');
$obj = new TianQiController();
$tqRs = $obj->getTianQi();
?>
<div class="use_index_t">
    <div class="use_index_wz">
        <img src="/public/images/icon_13.png"/>三亚
    </div>
    <ul class="use_index_zc">
        <li>涨潮 <?php echo $tqRs['zhangchao']; ?></li>
        <li>退潮 <?php echo $tqRs['tuichao']; ?></li>
        <li>日出 <?php echo $tqRs['richu']; ?></li>
        <li>日落 <?php echo $tqRs['riluo']; ?></li>
    </ul>
</div>
<div class="use_index_b">
    <ul class="overflow">
        <?php
        $obj = new BuyGoodsListController();
        $cnum = $obj->getUserDefaultCabinet();
        $stObj = new ShopTypeController();
        $listRs = $obj->getCabinetShopType($cnum);
        $obj = new UserOrderController();
        $rs = $obj->countUserIsReturn();
        ?>
        <li class="use_bg_1"><a href="/buy/goods"><h3>在线下单</h3>
                <p><?php echo count($listRs); ?>款宝贝开心玩</p></a></li>
        <li class="none"><a href="#"><h3>暂时没有</h3>
                <p>暂时没有</p></a></li>
        <li class="use_bg_3"><a href="http://mp.weixin.qq.com/s/oTHQYK2FpX_Y6xKxicPcgQ"><h3>使用攻略</h3>
                <p>参考攻略玩的更尽兴</p></a></li>
        <li class="use_bg_4"><a href="/user/order"><h3>我的订单</h3>
                <?php
                if (empty($rs)) {
                    echo '<p>暂无订单</p>';
                } else {
                    echo '<p>有订单需要归还<br/>' . Run::getFormatDate($rs['rent_date_end'], 'm月d日 H:i') . '</p>';
                }
                ?>
            </a></li>
    </ul>
</div>
<img src="/public/images/index_bg.jpg" class="use_index_bg">