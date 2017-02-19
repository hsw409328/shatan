<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>身份绑定</title>
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <?php
    $user = ParamsController::getSessionParams('userDetail');
    if (empty($user)) {
        echo '<link rel="stylesheet" type="text/css" href="/public/css/style_d.css"/>
              <link rel="stylesheet" type="text/css" href="/public/css/style_u.css"/>';
    } else {
        if ($user['user_type'] == '9') {
            echo '<link rel="stylesheet" type="text/css" href="/public/css/style_u.css"/>';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="/public/css/style_d.css"/>';
        }
    }
    ?>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="/public/js/common.js"></script>
</head>

<body>