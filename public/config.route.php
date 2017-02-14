<?php
ini_set ( "display_errors", 1 );
include_once APP_PATH . 'public/config.cabinet.php';
include_once APP_PATH . 'public/config.token.php';

//include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'Params.php';
include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'Base.php';
//include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'WebType.php';
//include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR . 'ValidateCode.php';

$_cGlob = scandir(APP_PATH . 'c' . DIRECTORY_SEPARATOR);
foreach ($_cGlob as $k=>$v){
    if($v=='index.php'){
        continue;
    }
    if(strstr($v, '.php')){
        include_once APP_PATH . 'c' . DIRECTORY_SEPARATOR.$v;
    }
}

include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . 'DB.php';
include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . 'Admin.php';
include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR . 'WebType.php';

$_mGlob = scandir(APP_PATH . 'm' . DIRECTORY_SEPARATOR);
foreach ($_mGlob as $k=>$v){
	if(strstr($v, '.php')){
		include_once APP_PATH . 'm' . DIRECTORY_SEPARATOR.$v;
	}
}
