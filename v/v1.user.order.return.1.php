<?php
Run::c('/user/login');
ParamsController::localSetParams('uor2','uor2');
?>
<div class="use_pay_t center">
    第一步：商品自检，请认真核对
</div>
<div class="use_return_next">
    <dl class="use_return_cp">
        <dt><img src="/public/images/1.jpg"/></dt>
        <dd class="use_return_cpt">大疆无人机</dd>
        <dd>* 如果自检过程中发现有配件遗漏漏在客房，可以先取消归还，取回后再次归还。</dd>
    </dl>
    <div class="use_return_note">
        <h2>务必要看</h2>
        <p>无人机配件较多，请认真检查喔！<br/>请按照背包内布局装入。</p>
        <ul class="use_return_i">
            <li><img src="/public/images/1.jpg"/></li>
            <li><img src="/public/images/1.jpg"/></li>
            <li><img src="/public/images/1.jpg"/></li>
        </ul>
        <div class="border_1">
            <form method="post" action="/user/order-return-2/">
                <input type="hidden" value="<?php echo RouteClass::getParams(3); ?>" name="oid">
                <textarea name="content" rows="6" id="content" placeholder="部分配件找不到了，或整个商品丢失，请在此备注"></textarea>
            </form>
        </div>
        <p>*点击取消按钮，会暂时中止归还。</p>
    </div>

</div>
<div class="use_return_re">
    <a href="javascript:$('form').submit();" class="use_return_g">自检完成</a>
    <a href="/user/order" class="use_return_n">取消</a>
</div>