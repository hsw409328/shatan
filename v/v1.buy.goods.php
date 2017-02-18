<?php
Run::c('/user/login');
ParamsController::localSetParams('bg1','bg1');
$obj = new BuyGoodsListController();
$cnum = $obj->getUserDefaultCabinet();
?>
<link rel="stylesheet" href="/public/css/swiper3.1.0.min.css" type="text/css"/>
<style>
    .swiper-container {
        width: 100%;
    }

    .swiper-wrapper img {
        width: 100%;
        -webkit-border-radius: 5px 5px 0 0;
        -moz-border-radius: 5px 5px 0 0;
        border-radius: 5px 5px 0 0;
    }

    .swiper-pagination-bullet-active {
        background: #FFC04D;
    }
</style>
<div class="use_order_t">
    <p>每笔订单只可选1个宝贝，如需租多个请反复下单，统一为2天，您当前选择的租赁柜<?php echo $cnum; ?></p>
    <a href="javascript:void(0)" class="update">点击修改>></a>
</div>
<div class="use_order_b">
    <ul>
        <?php
        $stObj = new ShopTypeController();
        $listRs = $obj->getCabinetShopType($cnum);
        $_tmpStRs = [];
        foreach ($listRs as $k => $v) {
            $stRs = $stObj->getShopTypeDetail($v['st_num']);
            $_tmpStRs[$v['st_num']] = $stRs;
            $goods_num = $obj->checkStGoods($cnum, $v['st_num'], $v['c_grid_area']);
            ?>
            <li select="<?php echo $goods_num ? 1 : 0; ?>" stnum="<?php echo $stRs['st_num']; ?>"
                cnum="<?php echo $cnum; ?>" m="<?php echo $stRs['st_money']; ?>"
                day="<?php echo $stRs['st_day']; ?>">
                <img src="<?php echo $stRs['st_img']; ?>">
                <dl>
                    <dt class="use_order_bt">
                    <h1><?php echo $stRs['st_name']; ?></h1>
                    <span><?php echo $stRs['st_span']; ?></span>
                    <strong><?php echo $goods_num ? '剩余' . $goods_num : '无货'; ?></strong>
                    </dt>
                    <dd class="use_order_xq"><?php echo $stRs['st_info']; ?></dd>
                    <dd class="use_order_jg"><strong><?php echo intval($stRs['st_money']); ?>
                            元</strong>&frasl;<span><?php echo $stRs['st_day']; ?>天</span>
                        <?php echo $goods_num ? '<div class="use_order_check check"></div>' : ''; ?>

                    </dd>
                </dl>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
<div class="use_order_next">
    <p>金额：<span id="m_id">0</span> <strong>元</strong></p>
    <a href="javascript:void(0);" id="next_id">下一步</a>
</div>
<!--修改租赁柜编号-->
<div class="use_order_number overflow">
    <div class="use_login_l floatn">
        <input placeholder="请输入租赁柜编号" id="cnum_id" type="text" class="icon_2"/>
    </div>
    <p>* 编号在租赁柜的左上⻆</p>
    <div class="use_order_sub overflow">
        <button class="determine">确定</button>
        <button class="cancel">取消</button>
    </div>
</div>

<!--产品详情-->
<div class="product_list">
    <?php
    foreach ($listRs as $k => $v) {
        $tmp = $_tmpStRs[$v['st_num']];
        $_tmpHtml = '';
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($tmp['st_img' . $i])) {
                $_tmpHtml .= '<div class="swiper-slide blue-slide"><img src="' . $tmp['st_img' . $i] . '"></div>';
            }
        }
        ?>
        <div class="product">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php echo $_tmpHtml; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="product_about">
                <h2 class="overflow"><strong><?php echo $tmp['st_name']; ?></strong><span
                            class="product_level level_<?php echo $tmp['st_leveal']; ?>"></span>
                </h2>
                <p><?php echo $tmp['st_info']; ?></p>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<!--透明背景-->
<div class="box"></div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".update").click(function () {
            $(".box").show();
            $(".use_order_number").show();
        });
        $(".cancel").click(function () {
            $(".box").hide();
            $(".use_order_number").hide();
        });
        $(".use_order_b ul li img").click(function () {
            $(".box").show();
            oclick = $(this).parents();
            number = oclick.index();
            $(".product_list .product").eq(number).css({"opacity": "1", "left": "50%", "top": "50%"});
        });
        $(".box").click(function () {
            $(".box").hide();
            $(".use_order_number").hide();
            $(".product_list .product").css({"opacity": "0", "left": "-50%", "top": "-50%"});
        });
        var _sub = 0;
        $('.determine').click(function () {
            if (_sub == 1) {
                return false;
            }
            var _cnum = $.trim($('#cnum_id').val());
            if (_cnum == '') {
                alert($('#cnum_id').attr('placeholder'));
                return false;
            }
            AjaxCommon.data = {
                'action': 'BuyGoodsList',
                'run': 'updateUserDefaultCabinet',
                'cnum': _cnum,
            };
            AjaxCommon.callback_func = function (data, sts) {
                var _d = UtilCommon.parseJson(data);
                if (_d.code == '1') {
                    UtilCommon.reload();
                } else {
                    alert(_d.msg);
                    _sub = 0;
                    return false;
                }
            };
            _sub = 1;
            AjaxCommon.post();
        });
        $('.use_order_b li[select=1]').click(function () {
            $('.use_order_b li').find('.use_order_jg div').attr('class', 'use_order_check check');
            $(this).find('.use_order_jg div').attr('class', 'use_order_check checked');
            var _cnum = $(this).attr('cnum');
            var _stnum = $(this).attr('stnum');
            var _m = $(this).attr('m');
            var _day = $(this).attr('day');
            $('#next_id').attr('href', '/buy/goods-next/' + _cnum + '/' + _stnum + '/' + _m + '/' + _day);
            $('#m_id').html(_m);
        });
    })

</script>
<script type="text/javascript" src="/public/js/swiper3.1.0.min.js"></script>
<script type="text/javascript">
    var mySwiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: 3000,
        pagination: '.swiper-pagination',
        paginationClickable: true,
    });
</script>