<?php
include_once '../../../public/config.inc.php';
include_once '../../../public/config.route.php';
final class RunClass {
	
	public static function run($className, $methodName) {
		include_once $className . '.php';
		$className .= 'Controller';		
		$obj = new $className ();
		$obj->$methodName ();
	}

}
RunClass::run ( Run::req ( 'c' ), Run::req ( 'f' ) );