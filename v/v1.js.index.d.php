<?php
$_sessio_str = 1;
include_once 'v1.js.index.head.php';
?>
<div class="set_details_c">
    <div class="dis_details_bm">
        <img src="/public/images/icon_21.png"/>
        <input type="text" id="gnum_id" placeholder="请输入商品编码"/>
    </div>
    <a href="javascript:void(0)" class="set_search_cx">查询</a>
</div>

<script>
    $('.set_search_cx').click(function () {
        var _d = $.trim($('#gnum_id').val());
        if (_d == '') {
            alert($('#gnum_id').attr('placeholder'));
            return false;
        }
        UtilCommon.href('/js/index-d-op/' + _d);
    });
</script>