<?php

class WechatToken
{

    /**
     * 如果Token超时，重新设定token
     */
    public static function setToken()
    {
        $ac_token = Run::getHttpRes(BASE_ACCESS_TOKEN_URL);
        $ac_token = json_decode($ac_token, true);
        $token = $ac_token ['access_token'];
        self::saveTokenFile($token);
        return $token;
    }

    /**
     * 保存token
     */
    public static function saveTokenFile($token)
    {
        file_put_contents(APP_PATH . 'public/token/token.inc', $token . ' ' . time());
    }

    /**
     * 读取token
     */
    public static function readTokenFile()
    {
        $a = file_get_contents(APP_PATH . 'public/token/token.inc');
        $a = explode(' ', $a);
        if (isset($a['1'])) {
            return self::setToken();
        } else {
            $t = time();
            $tc = $t - intval($a[1]);
            if ($tc > 7000) {
                return self::setToken();
            } else {
                return $a[0];
            }
        }
    }

    /**
     * JS-SDK token
     */
    public static function setJsToken($token)
    {
        //		$token = self::readTokenFile ();
        $_jsapi_ticket_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $token . '&type=jsapi';
        $_jsapi_ticket = Run::getHttpRes($_jsapi_ticket_url);
        $_jsapi_ticket = json_decode($_jsapi_ticket, true);
        self::saveJsTokenFile(time() . ' ' . $_jsapi_ticket ['ticket']);
        return $_jsapi_ticket ['ticket'];
    }

    /**
     * 保存js-token
     */
    public static function saveJsTokenFile($token)
    {
        file_put_contents(APP_PATH . 'public/token/token.js.inc', $token . ' ' . time());
    }

    /**
     * 读取js-token
     */
    public static function readJsTokenFile()
    {
        $a = file_get_contents(APP_PATH . 'public/token/token.js.inc');
        $a = explode(' ', $a);
        $t = time();
        if (isset($a['1'])) {
            $token = self::readTokenFile();
            return self::setJsToken($token);
        } else {
            $t = time();
            $tc = $t - intval($a[1]);
            if ($tc > 7000) {
                $token = self::readTokenFile();
                return self::setJsToken($token);
            } else {
                return $a[0];
            }
        }
    }
}