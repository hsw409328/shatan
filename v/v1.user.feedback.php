<?php
$user = Run::c('/user/login');
?>
<div class="use_my_t">
    <img src="<?php echo $user['u_head_photo']; ?>"/> <?php echo $user['u_nickname']; ?>
</div>
<div class="use_my_nav">
    <p class="overflow"><a href="/user/order"><span>我的订单</span></a><a href="javascript:void(0)" class="cur">意见反馈</a>
    </p>
</div>
<div class="background1"></div>
<form class="use_feedback" onsubmit="return false;">
    <div class="border_1 ">
        <textarea name="content" rows="6" id="content" placeholder="请输入您的宝贵意见，帮助我们更好的优化产品和服务。"></textarea>
    </div>
    <button id="btnbtn">确定</button>
</form>
<div class="use_fanhui">
    <a href="/"><img src="/public/images/icon_27.png"/></a>
</div>
<script>
    $('#btnbtn').click(function () {
        AjaxCommon.data = {
            'action': 'Users',
            'run': 'addFeedback',
            'msg': $('#content').val(),
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = UtilCommon.parseJson(data);
            if (_d.code == '1') {
                alert(_d.msg);
                UtilCommon.reload();
            } else {
                alert(_d.msg);
                return false;
            }
        };
        AjaxCommon.post();
    });
</script>