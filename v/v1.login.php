<?php
$user = ParamsController::getSessionParams('userDetail');
if (!empty($user)) {
    Run::show_msg(null, 1, '/');
}
?>
<!--头部-->
<div class="use_login_top">
    <img src="/public/images/login_bg.jpg" class="use_login_bg"/>
    <div class="use_login_hp">
        <img src="/public/images/1.jpg"/>
    </div>
</div>
<!--身体-->
<div class="use_login_body">
    <div class="use_login_border">
        <div class="use_login_l u_wbh">
            <input placeholder="请输入租赁柜编号" type="text" id="cnum" class="icon_2"/>
        </div>
        <span class="use_see">查看<br/>示例</span>
    </div>
    <div class="use_login_g">
        <img src="/public/images/icon_1.png"/>
        <input type="text" placeholder="请输入手机号码" id="mobile"/>
    </div>
    <div class="use_login_border">
        <div class="use_login_l u_wdx">
            <input placeholder="请输入短信验证码" id="validate_num" type="text" class="icon_3"/>
        </div>
        <!--点击之后添加use_login_dis-->
        <button class="use_login_dis" id="send_mobile_validate">发送验证码</button>
    </div>
</div>
<!--提交-->
<div class="submit_order1">
    <a href="javascript:void(0)" id="click_sub_btn">提交</a>
    <p>* 您的租取、归还都将在此柜子上自助操作完成</p>
</div>
<!--弹出层-->
<div class="box">
    <img src="/public/images/share-bg-btn.png" width="100%;" class="close">
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".use_see").click(function () {
            $(".box").show();
        })
        $(".close").click(function () {
            $(".box").hide();
        })
    });
    $('#click_sub_btn').click(function () {
        var _utype = 9;
        var _cnum = $.trim($('#cnum').val());
        if (_cnum == '') {
            alert($('#cnum').attr('placeholder'));
            return false;
        }
        var _mobile = $('#mobile').val();
        if ($.trim(_mobile) == '') {
            alert($('#mobile').attr('placeholder'));
            return false;
        }
        var _validate_num = $('#validate_num').val();
        if ($.trim(_validate_num) == '') {
            alert($('#validate_num').attr('placeholder'));
            return false;
        }
        AjaxCommon.data = {
            "action": "Users",
            "run": "reg",
            "mobile": _mobile,
            "validate": _validate_num,
            'utype': _utype,
            'cnum': _cnum,
        };
        AjaxCommon.callback_func = function (data, status) {
            var _d = eval('(' + data + ')');
            if (_d.code == '1') {
                UtilCommon.href('/buy/goods');
            } else {
                alert(_d.msg);
                return false;
            }
        };
        AjaxCommon.post();
    });
</script>