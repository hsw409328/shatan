<?php
Run::c('/other/login');
$obj = new CabinetStsController();
$rs = $obj->getUserManagerCabinetOne();
$cObj = new CabinetController();
$_tmpDetail = $cObj->getCabinetDetail($rs['c_num'], 'c_hotel,c_address,c_area,(select sName from website_type where id=c_city) as c_city_name');
?>
<div class="sorting_detalis_t">
    <dl class="overflow">
        <dt><?php echo $rs['c_num']; ?></dt>
        <dd class="sorting_det_bt"><strong><span><?php echo $_tmpDetail['c_hotel']; ?></span>
                <span><?php echo $_tmpDetail['c_address']; ?></span></strong></dd>
        <dd><span><?php echo $_tmpDetail['c_city_name']; ?></span>·<span><?php echo $_tmpDetail['c_area']; ?></span>
        </dd>
        <?php
        if ($rs['sorting_sts'] == '1') {
            $_hrefStr = 'javascript:void(0)';
        } else {
            $_hrefStr = 'javascript:sortingSuccess(\'' . $rs['c_num'] . '\')';
        }
        ?>
        <dd class="sorting_det_ok"><a
                    href="<?php echo $_hrefStr; ?>"><?php echo $obj->getStaticTodayCabinetSts($rs['sorting_sts']); ?></a>
        </dd>
    </dl>
</div>
<div class="sorting_detalis_b">
    <ul>
        <?php
        $stRs = $obj->getCabinetShopTypeList();
        $stObj = new ShopTypeController();
        foreach ($stRs as $k => $v) {
            $tmpRs = $stObj->getShopTypeDetail($v['st_num']);
            $_num = $obj->getSortingNum($v['c_num'], $v['st_num'], $v['c_grid_area']);
            if ($_num > 0) {
                $_css = 'sorting_det_color';
                $_select = 1;
            } else {
                $_css = '';
                $_select = '';
            }
            ?>
            <li>
                <dl select="<?php echo $_select; ?>" stnum="<?php echo $v['st_num']; ?>"
                    cnum="<?php echo $v['c_num']; ?>" gridarea="<?php echo $v['c_grid_area']; ?>">
                    <dt>
                        <img src="<?php echo $tmpRs['st_img']; ?>"/>
                    </dt>
                    <dd class="<?php echo $_css; ?>"><?php echo $_num; ?></dd>
                    <div class="sorting_sjx"></div>
                </dl>
            </li>
            <?php
        }
        ?>
        <!--
                <li>
                    <dl>
                        <dt><img src="images/1.jpg"/></dt>
                        <dd class="sorting_det_color">-6</dd>
                        <div class="sorting_sjx"></div>
                    </dl>
                </li>-->
    </ul>
    <div class="use_login_border sorting_det_bh">
        <div class="use_login_l u_wdx">
            <input placeholder="请输入对应的商品编号" id="num" type="text" class="icon_4"/>
        </div>
        <button class="right" id="submit_btn">提交</button>
    </div>
</div>
<div class="box"></div>

<div class="sorting_det_add">
    <img src="images/1.jpg" id="fj_gnum_img_path"/>
    <div class="sorting_det_adb">
        <strong>已分拣的商品编号:</strong>
        <span id="fj_gnum_list_id"></span>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".sorting_detalis_b ul li dl dd ").click(function () {
            $(".box").show();
            $(".sorting_det_add").show();
            $('#fj_gnum_img_path').attr('src', $(this).parent().find('img').attr('src'));
            AjaxCommon.data = {
                'action': 'CabinetSts',
                'run': 'getGoodsGridRelationByCnumAndStnum',
                'stnum': $(this).parent().attr('stnum'),
                'cnum': $(this).parent().attr('cnum'),
            };
            AjaxCommon.callback_func = function (data, sts) {
                var _d = UtilCommon.parseJson(data);
                $('#fj_gnum_list_id').html(_d.msg);
            };
            AjaxCommon.post();
        })
        $(".box").click(function () {
            $(".box").hide();
            $(".sorting_det_add").hide();

        });
        var _gridarea = '';
        var _stnum = '';
        var _cnum = '';

        $('dl[select=1]').click(function () {
            $('dl[select=1]').attr('class', '');
            $(this).attr('class', 'sorting_det_checked');
            $('dl[select=1]').find('div').hide();
            $(this).find('div').show();
            _gridarea = $(this).attr('gridarea');
            _cnum = $(this).attr('cnum');
            _stnum = $(this).attr('stnum');
        });

        var _submit_val = 0;
        $('#submit_btn').click(function () {
            if (_submit_val != 0) {
                return false;
            }
            if (_gridarea == '') {
                alert('请选择对应的商品类目');
                return false;
            }
            var _val = $.trim($('#num').val());
            if (_val == '') {
                alert('请输入对应的商品编号');
                return false;
            }

            AjaxCommon.data = {
                'action': 'CabinetSts',
                'run': 'addCabinetGridGoods',
                'gridarea': _gridarea,
                'stnum': _stnum,
                'cnum': _cnum,
                'val': _val
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
    });
    var sortingSuccess = function (_cnum) {
        AjaxCommon.data = {
            'action': 'CabinetSts',
            'run': 'updateCabinetSts',
            'cnum': _cnum,
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = eval('(' + data + ')');
            if (_d.code == '1') {
                alert(_d.msg);
                UtilCommon.reload();
            } else {
                alert(_d.msg);
                return false;
            }
        };
        AjaxCommon.post();
    };
</script>