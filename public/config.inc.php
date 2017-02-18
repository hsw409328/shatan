<?php
header("Content-type:text/html;charset=utf-8");
define('APP_TITLE', '沙滩柜子');
define('APPID', '123');
define('APPSECRET', '123');
define('APP_PAY_MCHID', '123');
define('APP_PAY_STR', '123');
define('TOKEN_URL', 'https://api.weixin.qq.com/sns/oauth2/access_token');
define('AUTH_URL', 'https://open.weixin.qq.com/connect/oauth2/authorize');
define('REFRESH_TOKEN', 'https://api.weixin.qq.com/sns/oauth2/refresh_token');
define('WECHAT_REDIRECT', 'https://mp.weixin.qq.com/mp/redirect?url=');
define('BASE_ACCESS_TOKEN_URL', 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APPID . '&secret=' . APPSECRET . '');
define('SERVICE_MSG_URL', 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=');
define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('IMG_WEBSITE', 'http://120.25.86.251:4869/');
define('APP_WEBSITE', 'http://' . $_SERVER ['HTTP_HOST']);
define('APP_NAME', 'lvyouapp');
ini_set('date.timezone', 'Asia/Shanghai');

session_start();

class Run
{

    public static $SECRET_KEY = 'lvyouapp';

    public static $PAGE_ADMIN = 15;

    private static function checkTpl($view)
    {
        $file = 'v/' . $view . '.php';
        if (!file_exists($file)) {
            return false;
        }
        return $file;
    }

    private static function includeTpl($file)
    {
        global $_urlParams;
        require_once $file;
    }

    public static function checkBrowser()
    {
        if (!strpos($_SERVER ['HTTP_USER_AGENT'], "MicroMessenger")) {
            die ('<html>
				    <head>
				    	<title>抱歉，出错了</title>
				        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
				        <style>
							body{background:#e1e0de;line-height: 1.6;
							font-family: "Helvetica Neue",Helvetica,"Microsoft YaHei",Arial,Tahoma,sans-serif;text-align: center}
							img{margin-top:40px;}
							p{font-weight: 400;color: #000000;}							
						</style>
				    </head>
					<body>
						<img src="http://img.momopet.cn:4869/4a06f368048c265f1f1c18b99d59d237?f=png" />
						<p>请在微信客户端打开链接</p>
					</body>
				</html>');
            exit ();
        }
    }

    public static function loadStart($view)
    {
        $check_tpl_res = self::checkTpl($view);
        if ($check_tpl_res) {
            self::includeTpl($check_tpl_res);
            return true;
        } else {
            self::show_msg('', '', APP_WEBSITE);
        }
    }

    /**
     *
     * 获取参数
     * @param string $str
     * @return 得到的结果
     */
    public static function req($str)
    {
        $str = isset ($_REQUEST [$str]) ? addslashes($_REQUEST [$str]) : '';
        return trim($str);
    }

    /**
     *
     * 显示网页操作信息
     * @param string $url 跳转URL
     * @param string $msg 提示语
     * @param int $single 标识位，0后退 1跳转
     */
    public static function show_msg($msg = null, $single = 0, $url = './')
    {
        if ($msg != null) {
            $alert_str = "alert('{$msg}');";
        } else {
            $alert_str = "";
        }
        if ($single != 0) {
            echo "<script>{$alert_str}window.location.href='" . APP_WEBSITE . "{$url}';</script>";
            exit ();
        } else {
            echo "<script>{$alert_str}history.go(-1);</script>";
            exit ();
        }
    }

    /**
     *
     * CURL GET方式请求
     * @param string $url
     * @param Array $params
     */
    public static function getHttpRes($url, $params = NULL)
    {
        $final_url = $url;
        if ($params != null) {
            $param = '?';
            foreach ($params as $key => $value) {
                $param .= $key . '=' . $value . '&';
            }
            $final_url .= $param;
        }
        $ch = curl_init($final_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (strpos($final_url, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     *
     * CURL POST方式请求
     * @param string $url
     * @param Array $params
     */
    public static function getHttpPostRes($data, $url, $header = array())
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url); //定义表单提交地址
        curl_setopt($ch, CURLOPT_POST, 1); //定义提交类型 1：POST ；0：GET
        curl_setopt($ch, CURLOPT_HEADER, 0); //定义是否显示状态头 1：显示 ； 0：不显示
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //定义请求类型
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //定义是否直接输出返回流
        if (strpos($url, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //定义提交的数据
        $response = curl_exec($ch); //接收返回信息
        if (curl_errno($ch)) { //出错则显示错误信息
            //print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        return $response; //显示返回信息
    }

    public static function set_login_user($oid)
    {
        ParamsController::localSetParams('openid', $oid);
        $userObj = new UsersController ();
        $userObj->login();
    }

    public static function c($b = '')
    {
        $user = ParamsController::getSessionParams('userDetail');
        if (empty($user)) {
            self::show_msg(null, 1, $b);
        }
        return $user;
    }

    public static function getFormatDate($d = null, $f = 'Y-m-d')
    {
        $returnDate = '';
        if ($d == null) {
            $returnDate = date($f, time());
        } else {
            if (!is_int($d)) {
                $returnDate = date($f, strtotime($d));
            } else {
                $returnDate = date($f, $d);
            }
        }
        return $returnDate;
    }

}

?>
