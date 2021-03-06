<?php
$user = ParamsController::getSessionParams('userDetail');
if (!empty($user)) {
    Run::checkUserType($user['user_type']);
}
?>
<!--头部-->
<div class="use_login_top">
    <img src="/public/images/login_bg.jpg" class="use_login_bg"/>
    <div class="use_login_hp">
        <img src="<?php $a = ParamsController::getSessionParams('open_wx_info');
        echo $a['headimgurl']; ?>"/>
    </div>
</div>
<!--身体-->
<div class="use_login_body">
    <div class="use_login_g">
        <img src="/public/images/icon_20.png"/>
        <?php
        $wtOjb = new WebTypeController();
        $rs = $wtOjb->getParentChilList('8');
        ?>
        <select class="dis_xz" placeholder="请选择身份">
            <option value="0" disabled selected>选择身份</option>
            <?php
            foreach ($rs as $k => $v) {
                echo "<option value='{$v['id']}'>{$v['sName']}</option>>";
            }
            ?>
        </select>
    </div>
    <div class="use_login_g">
        <img src="/public/images/icon_1.png"/>
        <input type="number" id="mobile" placeholder="请输入您的手机号码" onkeyup="validate_btn($(this));"/>
    </div>
    <div class="use_login_border">
        <div class="use_login_l u_wdx">
            <input placeholder="请输入4位短信验证码" id="validate_num" onkeyup="submit_change_cls($(this));" type="number"
                   class="icon_3"/>
        </div>
        <!--点击之后添加use_login_dis-->
        <button class="use_login_dis" id="send_mobile_validate" v="abc">发送验证码</button>
    </div>
</div>
<!--提交-->
<div class="submit_order">
    <a href="javascript:void(0)" id="click_sub_btn" class="bg1" v="abc">提交</a>
</div>
<script>
    var validate_btn = function (obj) {
        var v = obj.val();
        if (v.length == 11) {
            $('#send_mobile_validate').attr('class', '');
            $('#send_mobile_validate').attr('v', '0');
        } else {
            $('#send_mobile_validate').attr('class', 'use_login_dis');
            $('#send_mobile_validate').attr('v', 'abc');
        }
    };
    var submit_change_cls = function (obj) {
        var v = obj.val();
        if (v.length == 6) {
            $('#click_sub_btn').attr('class', '');
            $('#click_sub_btn').attr('v', '123');
        } else {
            $('#click_sub_btn').attr('class', 'bg1');
            $('#click_sub_btn').attr('v', 'abc');
        }
    };
    $('#click_sub_btn').click(function () {
        var v = $(this).attr('v');
        if (v != '123') {
            return false;
        }
        var _utype = $('select').find('option:selected').val();
        if (_utype == '0') {
            alert($('select').attr('placeholder'));
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
            'utype': _utype
        };
        AjaxCommon.callback_func = function (data, status) {
            var _d = eval('(' + data + ')');
            if (_d.code == '1') {
                if (_utype == '10') {
                    UtilCommon.href('/fj/index');
                } else if (_utype == '11') {
                    UtilCommon.href('/ps/index');
                } else {
                    UtilCommon.href('/js/index');
                }
            } else {
                alert(_d.msg);
                return false;
            }
        };
        AjaxCommon.post();
    });

    var wait = 60; //设置秒数(单位秒)
    var i = 0;
    var iid = 0;
    var sTimer = function () {
        var lr = wait - i;
        if (lr == 0) {
            clearInterval(iid);
            $('#send_mobile_validate').attr('class', '');
            $('#send_mobile_validate').html("发送验证码");
            me.attr('v', '0');
            iid = 0;
            i = 0;
        }
        else {
            $('#send_mobile_validate').attr('class', 'use_login_dis');
            $('#send_mobile_validate').html(lr + "秒后再发");
            i++;
        }
    }

    $('#send_mobile_validate').click(function () {
        var me = $(this);
        var v = $(this).attr('v');
        if (v != '0') {
            return false;
        }
        var _mobile = $('#mobile').val();
        if ($.trim(_mobile) == '') {
            alert($('#mobile').attr('placeholder'));
            return false;
        }
        AjaxCommon.data = {
            "action": "Users",
            "run": "sendMobileNum",
            "mobile": _mobile,
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = UtilCommon.parseJson(data);
            if (_d.code == '1') {
                iid = setInterval("sTimer()", 1000);
                me.attr('v', 'abc');
            } else {
                alert(_d.msg);
                me.attr('v', '0');
                return false;
            }
        };
        AjaxCommon.post();
    });

</script>