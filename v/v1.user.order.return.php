<?php
Run::c('/user/login');
$obj = new BuyGoodsListController();
$rs = $obj->getUserOrder();
?>
<div class="use_pay_t">
    租取、归还必须在同一个柜子上操作完成。
</div>
<div class="use_pay_b">
    <div class="use_pay_x use_return_r">
        <h3><label>柜子编号</label><input type="text" placeholder="请输入您眼前柜子的编号" id="cnum"/><span class="use_sk">查看示例</span>
        </h3>
        <p><strong id="oid_num"
                   v="<?php echo strtolower($rs['rs']['cnum']); ?>">订单编号：<?php echo $rs['rs']['oid']; ?></strong></p>
        <p>租赁有效期：<?php echo Run::getFormatDate($rs['rs']['rent_date_start'], 'm月d日 H:i'); ?>
            至 <?php echo Run::getFormatDate($rs['rs']['rent_date_end'], 'm月d日 H:i'); ?></p>
    </div>
    <table class="use_table" cellpadding="0" cellspacing="0">
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
            <td><?php echo floatval($rs['odRs']['deposit']); ?>元</td>
        </tr>
        <tr>
            <td>总额</td>
            <td><?php echo floatval($rs['rs']['total_money']); ?>元</td>
        </tr>
    </table>
    <div class="background1"></div>
    <div class="use_pay_remin use_return_bor">
        <h2 class="use_return_time">当前时间:<?php echo date('Y年m月d日 H:i'); ?></h2>
        <?php
        $oldTime = strtotime($rs['rs']['rent_date_end']);
        $ct = time();
        if ($oldTime > $ct) {
            echo '<p>您的订单处于有效期内归还，无需额外支付费用</p>';
        } else {
            echo '<p>您的订单已处于超时归还状态，<span class="use_return_kk">查看详情</span></p>';
        }
        ?>
    </div>

    <div class="use_pay_remin">
        <h2 class="use_return_bz">归还步骤</h2>
        <p>第一步：执行自检流程，检查是否有遗漏和损坏<br/>第二步：索取一次性封口袋，将袋子封锁放进回收箱
            <br/>第三步：24小时内工作人员验收，退还押金</p>
    </div>

</div>

<div class="use_pay_return">
    <h2>超时归还说明</h2>
    <p>① 有效期内归还，没有额外费用<br/>② 超时12小时内，扣款50元；<br/>③ 超时大于24小时，每天扣款100</p>
</div>

<div class="use_return_re">
    <a href="javascript:void(0);" class="use_return_g">我要归还</a>
    <a href="/user/order" class="use_return_n">暂不归还</a>
</div>
<!--弹出层-->
<img src="/public/images/share-bg-btn.png" width="100%;" class="close" style="display: none">
<div class="box_close">
</div>
<div class="box"></div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".use_sk").click(function () {
            $(".box_close").show();
            $('.close').show();
        })
        $(".box_close").click(function () {
            $(".box_close").hide();
            $('.close').hide();
        })
        $(".use_return_kk").click(function () {
            $(".box").show();
            $(".use_pay_return").show();
        });
        $(".box").click(function () {
            $(".box").hide();
            $(".use_pay_return").hide();
        });
        $('.use_return_g').click(function () {
            var _inputV = $.trim($('#cnum').val());
            var _cv = $('#oid_num').attr('v');
            if (_inputV.toUpperCase() != _cv.toUpperCase()) {
                alert('您输入的柜子编号与下单时的柜子不一致，租取、归还请务必在同一个柜子上操作完成。');
                return false;
            } else {
                UtilCommon.href('/user/order-return-1/<?php echo $rs['rs']['oid']; ?>');
            }
        });
    });
</script>