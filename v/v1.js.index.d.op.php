<?php
$_sessio_str = 0;
include_once 'v1.js.index.head.php';
$obj = new UserOrderController();
$rs = $obj->getUserOrderDetailByGNum(RouteClass::getParams('3'));
if ($rs['rs']['is_abnormal'] == '1') {
    Run::show_msg('异常订单，需要去异常订单模块处理', '1', '/js/abnormal');
}
?>
<div class="set_details_c">
    <div class="dis_details_bm">
        <img src="/public/images/icon_21.png"/>
        <input type="text" value="<?php echo RouteClass::getParams('3'); ?>"
               id="gnum_id" placeholder="请输入商品编号"/>
    </div>
    <span class="set_search_cx">查询</span>
</div>
<div class="use_myorder margin60">
    <div class="use_myorder_dd">
        <p>订单编号：<?php echo $rs['rs']['oid']; ?></p>
        <p>租赁有效期：<?php echo Run::getFormatDate($rs['rs']['rent_date_start'], 'm月d日 H:i'); ?>
            至 <?php echo Run::getFormatDate($rs['rs']['rent_date_end'], 'm月d日 H:i'); ?></p>
        <p>用户归还时间：
            <?php
            $oldTime = strtotime($rs['rs']['rent_date_end']);
            $ct = strtotime($rs['rs']['return_date']);
            if ($oldTime > $ct) {
                echo $rs['rs']['return_date'], '（有效租期内）';
            } else {
                echo $rs['rs']['return_date'], '<span class="use_mycs">超时归还</span>';
            }
            ?>
        </p>
    </div>
    <table class="use_my_table " cellpadding="0" cellspacing="0">
        <tr>
            <th>宝贝明细</th>
            <th>商品编码</th>
            <th>金额</th>
            <th>验收</th>
        </tr>
        <tr>
            <td><?php echo $rs['odRs']['gnum_name']; ?></td>
            <td class="color"><?php echo $rs['odRs']['gnum']; ?></td>
            <td><?php echo floatval($rs['odRs']['price']); ?>元</td>
            <?php
            $_tmpJsStr = '';
            if ($rs['odRs']['is_damage'] == '1') {
                $_tmpJsStr = 'bad';
                echo '<td><a href="/js/bad-d-next/' . $rs['odRs']['oid'] . '" class="">损坏</a></td>';
            } else {
                if ($rs['rs']['is_end'] == '1') {
                    $_tmpJsStr = 'good';
                    echo '<td>完好</td>';
                } else {
                    echo '<td><span class="set_details_xz">选择</span></td>';
                }
            }
            ?>
        </tr>
        <tr class="table_border">
            <td colspan="4">
                <span></span>
            </td>
        </tr>
        <tr>
            <td>订单押金</td>
            <td></td>
            <td><?php echo floatval($rs['rs']['deposit_money']); ?>元</td>
        </tr>
        <tr>
            <td>超时归还扣款</td>
            <td></td>
            <td><?php echo floatval($rs['rs']['overtime_money']); ?>元</td>
        </tr>
        <tr>
            <td>损坏扣款</td>
            <td></td>
            <td><?php echo floatval($rs['rs']['damage_money']); ?>元</td>
        </tr>
        <tr>
            <td class="color">实际退还押金</td>
            <td></td>
            <?php
            if ($rs['rs']['is_damage'] == '1') {
                ?>
                <td class="color"><?php echo floatval($rs['rs']['real_refund_money']); ?>元</td>
                <?php
            } else {
                $_real_refund_money = floatval($rs['rs']['deposit_money']) - floatval($rs['rs']['overtime_money']);
                ?>
                <td class="color"><?php echo $_real_refund_money; ?>元</td>
                <?php
            }
            ?>

        </tr>
    </table>
    <div class="set_thbz">
        <?php echo $rs['rs']['order_bak']; ?>
    </div>
</div>
<?php
if ($rs['rs']['is_end'] == '0') {
    echo '<div class="set_details_b">
            <a href="javascript:void(0)" id="endid">结算</a>
        </div>';
}
?>

<div class="set_choose">
    <ul>
        <li class="wanhaodeyibi" v="good">完好</li>
        <li><a href="/js/index-order-bad/<?php echo $rs['rs']['oid']; ?>/<?php echo $rs['rs']['uid']; ?>"
               v="damage">损坏</a></li>
        <li class="quxiao">取消</li>
    </ul>
</div>
<div class="box"></div>

<script type="text/javascript">
    var sign = '<?php echo $_tmpJsStr;?>';
    $(document).ready(function () {
        $(".set_details_xz").click(function () {
            $(".box").show();
            $(".set_choose").show();
        });
        $(".box").click(function () {
            $(".box").hide();
            $(".set_choose").hide();
        });
        $('.wanhaodeyibi').click(function () {
            sign = $(this).attr('v');
            $(".box").hide();
            $(".set_choose").hide();
            $('.set_details_xz').html($(this).html());
        });
        $('.quxiao').click(function () {
            sign = '';
            $(".box").hide();
            $(".set_choose").hide();
        });
        $('.set_search_cx').click(function () {
            var _d = $.trim($('#gnum_id').val());
            if (_d == '') {
                alert($('#gnum_id').attr('placeholder'));
                return false;
            }
            UtilCommon.href('/js/index-d-op/' + _d);
        });
        var _submit_val = 0;
        $('#endid').click(function () {
            if (_submit_val == 1) {
                return false;
            }
            AjaxCommon.data = {
                'action': 'UserOrder',
                'run': 'endOrder',
                'oid': '<?php echo $rs['rs']['oid']?>',
                'uid': '<?php echo $rs['rs']['uid']?>',
                'sign': sign,
            };
            AjaxCommon.callback_func = function (data, sts) {
                var _d = eval('(' + data + ')');
                if (_d.code == '1') {
                    alert(_d.msg);
                    UtilCommon.reload();
                } else {
                    alert(_d.msg);
                    _submit_val = 0;
                    return false;
                }
            };
            _submit_val = 1;
            AjaxCommon.post();
        });
    })
</script>