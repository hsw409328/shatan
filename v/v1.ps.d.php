<?php
Run::c('/other/login');
$obj = new CabinetStsController();
$rs = $obj->getUserManagerCabinetOne();
$cObj = new CabinetController();
$_tmpDetail = $cObj->getCabinetDetail($rs['c_num'], 'c_hotel,c_address,c_area,(select sName from website_type where id=c_city) as c_city_name');
$pickObj = new GoodsSupplyRecordController();
?>
<div class="dis_list_t">
    <p> * 请务必站在租赁柜屏幕前，进行以下操作。</p>
</div>
<div class="dis_details_top">
    <dl class="overflow">
        <dt><?php echo $rs['c_num']; ?></dt>
        <dd class="sorting_det_bt"><strong><span><?php echo $_tmpDetail['c_hotel']; ?></span>
                <span><?php echo $_tmpDetail['c_address']; ?></span></strong></dd>
        <dd><span><?php echo $_tmpDetail['c_city_name']; ?></span>·<span><?php echo $_tmpDetail['c_area']; ?></span>
        </dd>
    </dl>
</div>
<div class="dis_details_rep">
    <h2><img src="/public/images/icon_19.png"/><span>补货操作</span></h2>
    <div class="dis_details_input">
        <div class="dis_details_bm">
            <img src="/public/images/icon_21.png"/>
            <input type="text" placeholder="请输入商品编号" id="gnum"/>
        </div>
        <div class="dis_details_open" id="pwd_html" style="display:none"></div>
        <div class="dis_details_open" id="apply_click">申请开箱码</div>
        <div class="dis_details_ref" id="reload_click">刷新</div>
    </div>
    <table class="use_table">
        <?php
        $gsuRs = $pickObj->getApplyGoodsSupplyRecord($rs['c_num']);
        foreach ($gsuRs as $k => $v) {
            ?>
            <tr>
                <td><?php echo $v['r_g_num']; ?></td>
                <td><?php echo $v['r_g_name']; ?></td>
                <td><?php echo $v['r_sts'] == '0' ? '开门' : '入箱'; ?></td>
                <td><?php echo Run::getFormatDate($v['created_at'], 'm月d日 H:i'); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
<div class="background1"></div>
<div class="dis_details_rep">
    <h2><img src="/public/images/icon_22.png"/><span>取货操作</span></h2>
    <div class="dis_details_input">
        <div class="dis_details_bm">
            <img src="/public/images/icon_23.png"/>
            <input type="text" value="<?php echo $rs['c_num']; ?>" id="cnum_r" readonly="readonly"/>
        </div>
        <div class="dis_details_open" id="apply_recovery_html" style="display:none"></div>
        <div class="dis_details_open" id="apply_recovery_click">申请开箱码</div>
    </div>
    <table class="use_table">
        <?php
        $pickRs = $pickObj->getApplyCabinetPwdRecord($rs['c_num']);
        foreach ($pickRs as $k => $v) {
            ?>
            <tr>
                <td><?php echo $v['cr_c_num']; ?></td>
                <td width="20%"><?php echo $v['cr_user_name']; ?></td>
                <td width="20%">取出<?php echo $v['cr_goods_num']; ?>个</td>
                <td><?php echo Run::getFormatDate($v['created_at'], 'm月d日 H:i'); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
<script>
    var _sub = 0;
    $('#apply_recovery_click').click(function () {
        if (_sub == 1) {
            return false;
        }
        var me = $(this);
        var _cnum = $('#cnum_r').val();
        AjaxCommon.data = {
            'action': 'GoodsSupplyRecord',
            'run': 'applyCabinetPwd',
            'cnum': _cnum,
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = UtilCommon.parseJson(data);
            if (_d.code == '1') {
                me.remove();
                $('#apply_recovery_html').html(_d.msg);
                $('#apply_recovery_html').show();
            } else {
                alert(_d.msg);
                _sub = 0;
                return false;
            }
        };
        _sub = 1;
        AjaxCommon.post();
    });
    var _sub1 = 0;
    $('#apply_click').click(function () {
        if (_sub1 == 1) {
            return false;
        }
        var me = $(this);
        var _cnum = $('#cnum_r').val();
        var _gnum = $.trim($('#gnum').val());
        if (_gnum == '') {
            alert('请输入商品编号');
            return false;
        }
        AjaxCommon.data = {
            'action': 'GoodsSupplyRecord',
            'run': 'applyCabinetGridGoodsPwd',
            'cnum': _cnum,
            'gnum': _gnum,
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = UtilCommon.parseJson(data);
            if (_d.code == '1') {
                me.hide();
                $('#pwd_html').html(_d.msg);
                $('#pwd_html').show();
            } else {
                alert(_d.msg);
                _sub1 = 0;
                return false;
            }
        };
        _sub1 = 1;
        AjaxCommon.post();
    });
    $('#reload_click').click(function () {
        _sub1 = 0;
        $('#apply_click').show();
        $('#pwd_html').html('');
        $('#pwd_html').hide();
    });
</script>