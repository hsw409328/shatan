<?php
include_once '../../public/config.inc.php';

final class HrefClass
{

    private $_req = null;
    public $_tpl = '/';
    private $_jumpUrl = '/e/auth/auth.callback.php?tpl=';

    public function HrefClass()
    {
        $this->_req = $_REQUEST;
        if (isset ($_REQUEST ['tpl'])) {
            $this->_tpl = $_REQUEST ['tpl'];
        }
        $this->_jumpUrl .= $this->_tpl;
    }

    public function run()
    {
        $URL = AUTH_URL . '?appid=' . APPID . '&redirect_uri=' . urlencode(APP_WEBSITE . $this->_jumpUrl) . '&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
        header("location:" . $URL);
    }
}

$hrefClass = new HrefClass ();
$hrefClass->_tpl = Run::req('tpl');
$hrefClass->run();