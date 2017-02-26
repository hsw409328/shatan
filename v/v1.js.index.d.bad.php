<?php
Run::c('/other/login');
$obj = new UserOrderController();
$rs = $obj->getUserOrderDetail(RouteClass::getParams('3'), RouteClass::getParams('4'));
if ($rs['is_damage'] == '1') {
    Run::show_msg();
}
?>
<div class="sp_damage">商品损坏处理</div>
<div class="set_next">
    <table class="use_my_table1 " cellpadding="0" cellspacing="0">
        <tr>
            <th>宝贝明细</th>
            <th>商品编码</th>
            <th>金额</th>
            <th>验收</th>
        </tr>
        <tr>
            <td><?php echo $rs['gnum_name']; ?></td>
            <td class="color"><?php echo $rs['gnum']; ?></td>
            <td><?php echo floatval($rs['price']); ?>元</td>
            <td>损坏</td>
        </tr>
    </table>
    <div class="set_kk">
        <p>扣款（只能填写5的倍数）</p>
        <span>元</span>
        <input type="hidden" id="deposit_id" value="<?php echo floatval($rs['deposit']); ?>"/>
        <input type="number" id="d_money" placeholder="请输入金额"/>
    </div>
    <div class="border_1">
        <textarea name="content" rows="6" id="content" placeholder="请输入损坏情况及处理结果说明，便于用户查看。"></textarea>
    </div>
</div>
<div class="set_filez">
    <div class="detail">
        <a href="javascript:void(0);" class="file">
            <img src="/public/images/sctp.png">
            <input type="file" id="upload1" name="upload1" accept="image/gif, image/jpeg, image/png"
                   onchange="ajaxFileUpload()">
            <input type="hidden" id="imgPath">
        </a>
        <a href="javascript:void(0)" class="set_sc"><img src="/public/images/icon_25.png"/></a>
    </div>
</div>
<div class="background1"></div>
<div class="use_pay_return">
    <h2>损坏定价规范</h2>
    <p>
        <?php
        $stObj = new ShopTypeController();
        $rs1 = $stObj->getShopTypeDetail($rs['stnum']);
        echo $rs1['st_bad_info'];
        ?>
    </p>
</div>
<div class="set_detailsx_b">
    <a href="javascript:void(0)" id="sv_btn">保存</a><a href="javascript:UtilCommon.back();" class="cancel">取消</a>
</div>
<script src="/public/js/ajaxfileupload.js"></script>
<script>
    var _submit_val = 0;
    $('#sv_btn').click(function () {
        if (_submit_val == 1) {
            return false;
        }
        var _tmpv = '';
        $('input[files="files"]').each(function () {
            _tmpv += $(this).val() + ',';
        });
        var _d_m = parseFloat($('#d_money').val());
        var _p_m = parseFloat($('#deposit_id').val());
        if (_d_m > _p_m) {
            alert('扣款不能大于押金');
            return false;
        }
        var _c = $.trim($('#content').val());
        if (_c == '') {
            alert($('#content').attr('placeholder'));
            return false;
        }

        AjaxCommon.data = {
            'action': 'UserOrder',
            'run': 'addBadOrder',
            'oid': '<?php echo $rs['oid']?>',
            'uid': '<?php echo $rs['uid']?>',
            'img': _tmpv,
            'content': _c,
            'money': _d_m,
        };
        AjaxCommon.callback_func = function (data, sts) {
            var _d = eval('(' + data + ')');
            if (_d.code == '1') {
                alert(_d.msg);
                UtilCommon.back();
            } else {
                alert(_d.msg);
                _submit_val = 0;
                return false;
            }
        };
        _submit_val = 1;
        AjaxCommon.post();

    });
    var a = function () {
        $('.show').click(function () {
            $(this).parent().remove();
        });
    };
    a();
    var ajaxFileUpload = function () {
        $.ajaxFileUpload
        (
            {
                url: '/c/?action=Upload&run=uploadImgFile', //用于文件上传的服务器端请求地址
                secureuri: false, //是否需要安全协议，一般设置为false
                fileElementId: 'upload1', //文件上传域的ID
                dataType: 'json', //返回值类型 一般设置为json
                success: function (data, status)  //服务器成功响应处理函数
                {
                    if (data.code != '1') {
                        console.log(data.msg);
                    }

                    var _html = '<div class="detail">' +
                        '<a href="javascript:void(0);" class="file">' +
                        '<img src="' + data.msg + '">' +
                        '</a><input type="hidden" value="' + data.msg + '" files="files">' +
                        '<a href="javascript:void(0)" class="set_sc show">' +
                        '<img src="/public/images/icon_25.png"/>' +
                        '</a>' +
                        '</div>';

                    $('.set_filez').prepend(_html);

                    a();

                },
                error: function (data, status, e)//服务器响应失败处理函数
                {
                    console.log(e);
                }
            }
        )
        return false;
    }
</script>