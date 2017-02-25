<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/19
 * Time: 13:17
 */
final class WangYiCloudController
{
    private static $_appkey = '0ce2da85ef52bcc4160414446104e49c';
    private static $_appsecret = 'b09cf0f8197f';

    private static function getHeaders()
    {
        $rndstr = self::getRandomString(32, '3');
        $t = time();
        $header_arr = [
            'AppKey: ' . self::$_appkey,
            'Nonce: ' . $rndstr,
            'CurTime: ' . $t,
            'CheckSum: ' . sha1(self::$_appsecret . $rndstr . $t),
            'Content-Type: application/x-www-form-urlencoded',
        ];
        return $header_arr;
    }

    public static function sendMobileValidate($mobile)
    {
        $url = 'https://api.netease.im/sms/sendcode.action';
        $data ['mobile'] = $mobile;
        $data = 'mobile=' . $mobile . '&templateid=3050114&codeLen=6';
        $header_arr = self::getHeaders();
        $rs = Run::getHttpPostRes($data, $url, $header_arr);
        $rs = json_decode($rs, true);
        if (empty($rs)) {
            return false;
        } else {
            if ($rs['code'] == '200') {
                return $rs['obj'];
            } else {
                return false;
            }
        }
    }

    public static function checkMobileValidate($mobile, $num)
    {
        $url = 'https://api.netease.im/sms/verifycode.action';
        $data ['mobile'] = $mobile;
        $data ['code'] = $num;
        $header_arr = self::getHeaders();
        $rs = Run::getHttpPostRes($data, $url, $header_arr);
        $rs = json_decode($rs, true);
        if (empty($rs)) {
            return false;
        } else {
            if ($rs['code'] == '200') {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function sendNoticeMsg($mobile, $params)
    {
        $url = 'https://api.netease.im/sms/sendtemplate.action';
        $data = 'templateid=3050115&mobiles=["' . $mobile . '"]&params=["' . implode('","', $params) . '"]';
        $header_arr = self::getHeaders();
        $rs = Run::getHttpPostRes($data, $url, $header_arr);
        $rs = json_decode($rs, true);
        if (empty($rs)) {
            return false;
        } else {
            if ($rs['code'] == '200') {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     *
     * 获取随机字符串
     * @param int $len
     * @param string $type 1数字 2字符 3数字+字符     默认1
     */
    public static function getRandomString($len = 6, $type = '1')
    {
        if ($type == '1') {
            $str = '0123456789';
        } elseif ($type == '2') {
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxzy';
        } elseif ($type == '3') {
            $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxzy';
        }

        $n = $len;
        $len = strlen($str) - 1;
        $s = '';
        for ($i = 0; $i < $n; $i++) {
            $s .= $str [rand(0, $len)];
        }

        return $s;
    }

}