<?php
include_once '../../public/config.inc.php';
include_once '../../public/config.route.php';

final class WechatCallback
{

    private $_req = null;
    private $_error = null;
    private $_resouce = null;
    private $_user = null;

    public function WechatCallback()
    {
        $this->_req = $_REQUEST;
    }

    public function check()
    {
        if (!isset ($this->_req ['code'])) {
            $this->_error = '回调CODE出现错误';
            $this->error();
        }
    }

    public function run()
    {
        $this->check();
        $param = '?appid=' . APPID . '&secret=' . APPSECRET . '&code=' . $this->_req ['code'] . '&grant_type=authorization_code';
        $url = TOKEN_URL . $param;
        $res = Run::getHttpRes($url);
        $this->_resouce = json_decode($res, true);
        if (isset ($this->_resouce ['errcode']) && $this->_resouce ['errcode'] == '40029') {
            $res = Run::getHttpRes($url);
            $this->_resouce = json_decode($res, true);
            if (isset ($this->_resouce ['errcode']) && $this->_resouce ['errcode'] == '40029') {
                //可以提供独立的维护服务
                $this->_error = '微信服务器异常，请稍后重试！';
                $this->error();
            }
        }
        $this->login();
    }

    public function login()
    {
        $this->_user = Run::set_login_user($this->_resouce ['openid']);
        $this->href();
    }

    public function href()
    {
        if (is_array($this->_resouce)) {
            //执行跳转
            Run::show_msg('', '1', APP_WEBSITE . '/' .$this->_req ['tpl']);
            exit ();
        } else {
            $this->_error = '获取用户资料错误';
            $this->error();
        }
    }

    public function error()
    {
        Run::checkBrowser($this->_error, true);
    }

}

$obj = new WechatCallback ();
$obj->run();
?>