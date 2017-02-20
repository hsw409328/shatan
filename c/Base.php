<?php

class Base
{

    public function __call($ar, $f)
    {
        $this->_jsonEn('999', '方法不存在');
    }

    /**
     * 根据格子获取对应的密码
     * @param $grid
     * @return string
     */
    public function _getPwd($grid)
    {
        $one = $grid[0];
        if (isset(ConfigCabinet::$_configABCD[$one])) {
            $pwd[0] = ConfigCabinet::$_configABCD[$one];
        } else {
            $this->_jsonEn('500', '系统内部错误');
        }
        $pwd[1] = $this->getRandomString(3);
        $two = intval(substr($grid, 1, 2));
        if ($two < 10) {
            $pwd[2] = '0' . strval($two);
        } else {
            $pwd[2] = strval($two);
        }
        return implode($pwd);
    }

    /**
     * @return bool
     */
    public function _parsePwd($pwd)
    {
        $one = $pwd[0];
        $key = array_search($one, ConfigCabinet::$_configABCD);
        $two = intval($pwd[4] . $pwd[5]);
        return strval($key) . strval($two);
    }

    public function checkValiNum()
    {
        $_vail = ParamsController::getSessionParams('web_authnum_num');
        $_rVail = Run::req('eVali');
        if (strtolower($_vail) != strtolower($_rVail)) {
            $this->_jsonEn('0', '验证码输入错误');
        }
        return true;
    }

    public function getUserDetail()
    {
        $userDetail = ParamsController::getSessionParams('userDetail');
        if (empty ($userDetail)) {
            return null;
        }
        return $userDetail;
    }

    public function _getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset ($_SERVER ['REMOTE_ADDR']) && $_SERVER ['REMOTE_ADDR'] && strcasecmp($_SERVER ['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER ['REMOTE_ADDR'];
        else
            $ip = "192.168.1.2";
        return ($ip);
    }

    /**
     *
     * 获取随机字符串
     * @param int $len
     * @param string $type 1数字 2字符 3数字+字符     默认1
     */
    public function getRandomString($len = 6, $type = '1')
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

    public function uploadImg($file)
    {
        $f = $file ['tmp_name'];
        if (!$f) {
            return false;
        }
        $imgtype = $file ['type'];
        $PSize = filesize($f);
        $picturedata = fread(fopen($f, "r"), $PSize);
        $param = $picturedata;
        $arr = explode('/', $imgtype);
        $h = array("Content-Type:" . $arr ['1']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, IMG_WEBSITE . "upload");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $s = curl_exec($ch);
        curl_close($ch);
        $r = json_decode($s);
        $md5 = $r->info->md5;
        if ($arr ['1'] == 'png') {
            $md5 = $md5 . '?f=png';
        }
        return IMG_WEBSITE . $md5;
    }

    public function uploadFile($file, $_files)
    {
        $_path = APP_PATH . '/public/file/' . $_files . '/';
        $f = $file ['tmp_name'];
        if (is_dir($_path)) {
            $res = true;
        } else {
            $res = mkdir($_path);
        }

        if ($res) {
            if (!$f) {
                return false;
            }
            $imgtype = $file ['type'];
            $arr = explode('/', $imgtype);
            $fileName = '/public/file/' . $_files . '/' . time() . '.' . $arr ['1'];
            $pathFileName = $_path . time() . '.' . $arr ['1'];
            if (move_uploaded_file($f, $pathFileName)) {
                return $fileName;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function wlog($fileName = '', $str)
    {
        if (empty ($fileName)) {
            $fileName = Run::getFormatDate('', 'Ymd') . '.log';
        } else {
            $fileName .= '.log';
        }
        $str = Run::getFormatDate('', 'Y-m-d H:i:s') . ' ' . $str;
        $_r = file_get_contents($fileName);
        file_put_contents($fileName, $str . "\r" . $_r);
    }

    public function SysId($_prev = 'ST')
    {
        $t = strval(time());
        $r = $this->getRandomString(3, 3);
        return $_prev . $t . $r;
    }

    public function SysOrderId($_prev = 'ST')
    {
        $t = date('YmdHis', time());
        $r = $this->getRandomString(6);
        return $_prev . $t . strtoupper($r);
    }

    public function _jsonEn($code = '999', $msg = '系统异常')
    {
        $res ['code'] = $code;
        $res ['msg'] = $msg;
        echo json_encode($res);
        exit ();
    }

}