<?php
include_once '../public/config.inc.php';
include_once '../public/config.route.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
    echo json_encode(['code' => 400, 'msg' => '请求方式不正确']);
    exit();
}

$_route = [

    //开关门操作
    'v1/open-door' => 'getGridXY',
    'v1/close-door' => 'setGridXY',

    //潮汐时间
    'v1/get-weather' => 'getWeather',

];

$_urlParams = $_SERVER ['REQUEST_URI'];
$_urlParams = explode('/', $_urlParams);

if (isset($_urlParams ['2']) && isset($_urlParams ['3'])) {
    $run = isset ($_route [$_urlParams ['2'] . '/' . $_urlParams ['3']]) ? $_route [$_urlParams ['2'] . '/' . $_urlParams ['3']] : '';
    $obj = new ApiController();
    $obj->$run();
} else {
    echo json_encode(['code' => 401, 'msg' => '请求地址不存在']);
    exit();
}
