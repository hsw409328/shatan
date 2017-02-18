<div class="dis_list_t">
    <p>* 工作流程及操作规范，点击<a href="#">查看详情>></a></p>
</div>

<div class="set_search_c">
    <div class="dis_details_bm">
        <img src="/public/images/icon_24.png"/><input type="text" id="psdz" placeholder="请输入运输袋编码"/>
    </div>
    <a href="javascript:void(0)" class="set_search_cx">查询</a>
</div>
<?php
$cls = '1';
include_once 'v1.js.footer.nav.php';
?>
<script>
    $('.set_search_cx').click(function () {
        var _d = $.trim($('#psdz').val());
        if (_d == '') {
            alert($('#psdz').attr('placeholder'));
            return false;
        }
        UtilCommon.href('/js/index-d/' + _d);
    });
</script>
