<?php
include_once '../public/config.inc.php';
include_once '../public/config.route.php';

final class PayCallbackController
{
    public function __construct()
    {
        $r = file_get_contents("php://input");
        $payObj = new WechatPayController();
        $payObj->payCallBack($r);
    }
}

new PayCallbackController();