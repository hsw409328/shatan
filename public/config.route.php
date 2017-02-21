<?php
ini_set("display_errors", 1);
include_once APP_PATH . 'public/config.cabinet.php';
include_once APP_PATH . 'public/config.token.php';

//include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'Params.php';
include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'Base.php';
//include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'WebType.php';
//include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'ValidateCode.php';

$_cGlob = scandir(APP_PATH . 'c' . DIRECTORY_SEPARATOR);
foreach ($_cGlob as $k => $v) {
    if ($v == 'index.php') {
        continue;
    }
    if (strstr($v, '.php')) {
        include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . $v;
    }
}

include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . 'DB.php';
include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . 'Admin.php';
include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . 'WebType.php';

$_mGlob = scandir(APP_PATH . 'm' . DIRECTORY_SEPARATOR);
foreach ($_mGlob as $k => $v) {
    if (strstr($v, '.php')) {
        include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . $v;
    }
}

class RouteClass
{
    public static $params = [];

    public static function setParams($key, $value)
    {
        self::$params[$key] = $value;
    }

    public static function getParams($key)
    {
        return isset(self::$params[$key]) ? self::$params[$key] : '';
    }
}

class TitleClass
{
    public static $params = [
        'other/login' => '身份绑定',

        'fj/index' => '管理的柜子',
        'fj/d' => '柜子详情',

        'ps/index' => '管理的柜子',
        'ps/d' => '柜子详情',

        'js/index' => '正常结算',
        'js/index-d' => '正常结算',
        'js/index-d-op' => '结算操作',
        'js/index-order-bad' => '损坏处理',
        'js/bad' => '损坏订单',
        'js/bad-d' => '损坏订单详情',
        'js/bad-d-next' => '损坏处理详情',
        'js/abnormal' => '异常订单',
        'js/ab-d' => '异常订单详情',

        'user/login' => '注册',
        'user/order' => '我的主页',
        'user/order-return' => '立即归还',
        'user/order-return-1' => '宝贝自检',
        'user/order-return-2' => '锁袋归还',
        'user/order-return-3' => '归还完成',
        'user/order-bad' => '损坏详情',
        'user/feedback' => '意见反馈',

        'buy/goods' => '选择宝贝',
        'buy/goods-next' => '订单支付',
        'buy/success' => '支付完成',
    ];

    public static $title = '';

    public static function setTitle($route)
    {
        if (isset(self::$params[$route])) {
            self::$title = self::$params[$route];
        } else {
            self::$title = '趣海边';
        }
    }
}
