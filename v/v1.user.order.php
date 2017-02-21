<?php
$user = Run::c('/user/login');
$obj = new UserOrderController();
$rs = $obj->getUserOrder();
?>
<div class="use_my_t">
    <img src="<?php echo $user['u_head_photo']; ?>"/> <?php echo $user['u_nickname']; ?>
</div>
<div class="use_my_nav">
    <p class="overflow"><a href="javascript:void(0)" class="cur"><span>我的订单</span></a><a href="/user/feedback">意见反馈</a>
    </p>
</div>
<div class="use_myorders">
    <?php
    if (empty($rs)) {
        echo '<div class="background1"></div>';
    } else {
        foreach ($rs as $k => $v) {
            ?>
            <div class="use_myorder">
                <div class="use_myorder_dd">
                    <p>订单编号:<?php echo $v['oid']; ?><span
                                class="use_myzt">
                        <?php
                        if ($v['is_return'] == '0') {
                            echo $v['is_pickup'] ? '已取货' : '未取货';
                        } else {
                            echo $v['is_end'] ? '验收完成' : '未验收';
                        }
                        ?>
                    </span>
                    </p>
                    <p>租赁柜:<?php echo $v['cnum']; ?></p>
                    <p>租赁有效期:<?php echo Run::getFormatDate($v['rent_date_start'], 'm/d H:i'); ?>
                        至 <?php echo Run::getFormatDate($v['rent_date_end'], 'm/d H:i'); ?></p>
                    <?php
                    if ($v['is_return'] == '0') {
                        if ($v['is_pickup'] == '1') {
                            echo '<a href="/user/order-return/' . $v['oid'] . '" class="use_ljgh">立即归还</a>';
                        }
                    } else {
                        echo '<p>归还时间:' . Run::getFormatDate($v['return_date'], 'm月d日 H:i');
                        if (!empty($v['overtime_desc'])) {
                            echo '<span class="use_mycs">超时归还</span>';
                        }
                        echo '</p>';
                    }
                    ?>
                </div>
                <table class="use_my_table">
                    <tr>
                        <th>宝贝明细</th>
                        <th>开箱密码</th>
                        <th>金额</th>
                        <?php
                        if ($v['is_return'] == '0') {
                            echo '<th>备注</th>';
                        } else {
                            echo '<th>验收</th>';
                        }
                        ?>
                    </tr>
                    <?php
                    $odRs = $obj->getUserOrderDetail($v['oid'], $v['uid']);
                    ?>
                    <tr>
                        <td><?php echo $odRs['gnum_name']; ?></td>
                        <td class="color"><?php echo $odRs['pwd']; ?></td>
                        <td><?php echo floatval($odRs['price']); ?>元</td>
                        <?php
                        if ($v['is_return'] == '0') {
                            echo '<td>需要归还</td>';
                        } else {
                            if ($v['is_damage'] == '0') {
                                echo '<td></td>';
                            } else {
                                echo '<td><a href="/user/order-bad/' . $v['oid'] . '" class="use_ma_dama">损坏</a></td>';
                            }
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>订单押金</td>
                        <td></td>
                        <td><?php echo floatval($v['deposit_money']); ?>元</td>
                    </tr>
                    <tr>
                        <td>订单总额</td>
                        <td></td>
                        <td><?php echo floatval($v['total_money']); ?>元</td>
                    </tr>
                    <?php
                    if ($v['is_return'] == '1' && $v['is_end'] == '1') {
                        ?>
                        <tr>
                            <td>超时归还扣款</td>
                            <td></td>
                            <td><?php echo floatval($v['overtime_money']); ?>元</td>
                        </tr>
                        <tr>
                            <td>损坏扣款</td>
                            <td></td>
                            <td><?php echo floatval($v['damage_money']); ?>元</td>
                        </tr>
                        <tr>
                            <td class="color">退还押金</td>
                            <td></td>
                            <td class="color"><?php echo floatval($v['refund_money']); ?>元</td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
                if ($v['is_return'] == '0') {
                    echo '<div class="use_my_zs">
                最晚归还时间：' . Run::getFormatDate($v['rent_date_end'], 'm月d日H:i') .
                        '。归还请点击“立即归还“按钮，按流程操作，将宝贝投入' . $v['cnum'] . '柜子的归还箱。
                    </div>';
                }
                ?>
            </div>
            <?php
        }
    }
    ?>

</div>

<div class="use_fanhui">
    <a href="/"><img src="/public/images/icon_27.png"/></a>
</div>

<!--归还提醒-->
<div class="use_pay_return use_my_return">
    <h2>超时归还说明</h2>
    <p>① 有效期内归还，没有额外费用<br/>② 超时12小时内，扣款50元<br/>③ 超时大于24小时，每天扣款100</p>
</div>

<!--透明背景-->
<div class="box"></div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".use_mycs").click(function () {
            $(".box").show();
            $(".use_pay_return").show();
        })
        $(".box").click(function () {
            $(".box").hide();
            $(".use_pay_return").hide();
        })

    })
</script>