<?php
Run::c('/user/login');
ParamsController::localSetParams('uor2', 'uor2');
$obj = new UserOrderController();
$user = $obj->getUserDetail();
$rs = $obj->getUserOrderDetail(RouteClass::getParams('3'), $user['id']);
$obj = new GoodsController();
$rs = $obj->getGoodsDetail($rs['gnum']);
ParamsController::localSetParams('uor_title', $rs['g_name']);
?>
<div class="use_pay_t center">
    第一步：商品自检，请认真核对
</div>
<div class="use_return_next">
    <dl class="use_return_cp">
        <dt><img src="<?php echo $rs['g_list_img_path']; ?>"/></dt>
        <dd class="use_return_cpt"><?php echo $rs['g_name']; ?></dd>
        <dd>* <?php echo $rs['g_check_info']; ?></dd>
    </dl>
    <div class="use_return_note">
        <h2>务必要看</h2>
        <img src="<?php echo $rs['g_check_img']; ?>" class="wubiyk" />
        <div class="border_1">
            <form method="post" action="/user/order-return-2/">
                <input type="hidden" value="<?php echo RouteClass::getParams(3); ?>" name="oid">
                <textarea name="content" rows="6" id="content" placeholder="部分配件找不到了，或整个商品丢失，请在此备注"></textarea>
            </form>
        </div>
        <p style="padding-bottom:20px;">*点击取消按钮，会暂时中止归还。</p>
    </div>

</div>
<div class="use_return_re">
    <a href="javascript:$('form').submit();" class="use_return_g">自检完成</a>
    <a href="/user/order" class="use_return_n">取消</a>
</div>