<?php
include_once 'public/config.inc.php';
include_once 'public/config.route.php';

Run::set_login_user('123456789');

$_route = array (
    'other/login' => 'v1.other.login',
    'fj/index' => 'v1.fj.index',
);

$_urlParams = $_SERVER ['REQUEST_URI'];
$_urlParams = explode ( '/', $_urlParams );

Run::loadStart ( 'v1.header' );

if(isset($_urlParams ['1'])&&isset($_urlParams ['2'])){
	$view = isset ( $_route [$_urlParams ['1'] . '/' . $_urlParams ['2']] ) ? $_route [$_urlParams ['1'] . '/' . $_urlParams ['2']] : '';
}else{
	$view = '';
}

//var_dump($_urlParams ['1'] . '/' . $_urlParams ['2']);

if ($view != '') {
	Run::loadStart ( $view );
} else {
	Run::loadStart ( 'v1.index' );
}
Run::loadStart ( 'v1.footer' );