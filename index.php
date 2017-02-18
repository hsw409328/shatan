<?php
include_once 'public/config.inc.php';
include_once 'public/config.route.php';

Run::set_login_user('userid1');

$_route = array(
    'other/login' => 'v1.other.login',

    'fj/index' => 'v1.fj.index',
    'fj/d' => 'v1.fj.d',

    'ps/index' => 'v1.ps.index',
    'ps/d' => 'v1.ps.d',

    'js/index' => 'v1.js.index',
    'js/index-d' => 'v1.js.index.d',
    'js/index-d-op' => 'v1.js.index.d.op',
    'js/index-order-bad' => 'v1.js.index.d.bad',
    'js/bad' => 'v1.js.bad',
    'js/abnormal' => 'v1.js.abnormal',

    'user/login' => 'v1.login',
    'user/order' => 'v1.user.order',
    'user/order-return' => 'v1.user.order.return',
    'user/order-return-1' => 'v1.user.order.return.1',
    'user/order-return-2' => 'v1.user.order.return.2',
    'user/order-return-3' => 'v1.user.order.return.3',
    'user/order-bad' => 'v1.user.order.bad',
    'user/feedback' => 'v1.user.feedback',

    'buy/goods' => 'v1.buy.goods',
    'buy/goods-next' => 'v1.buy.goods.next',
    'buy/success' => 'v1.buy.success',
);

$_urlParams = $_SERVER ['REQUEST_URI'];
$_urlParams = explode('/', $_urlParams);

Run::loadStart('v1.header');

if (isset($_urlParams ['1']) && isset($_urlParams ['2'])) {
    $view = isset ($_route [$_urlParams ['1'] . '/' . $_urlParams ['2']]) ? $_route [$_urlParams ['1'] . '/' . $_urlParams ['2']] : '';
    foreach ($_urlParams as $k => $v) {
        if ($k < 3) {
            continue;
        }
        RouteClass::setParams($k, $v);
    }
} else {
    $view = '';
}

//var_dump($_urlParams ['1'] . '/' . $_urlParams ['2']);

if ($view != '') {
    Run::loadStart($view);
} else {
    Run::loadStart('v1.index');
}
Run::loadStart('v1.footer');