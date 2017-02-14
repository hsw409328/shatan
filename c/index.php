<?php
include_once '../public/config.inc.php';
include_once '../public/config.route.php';

final class RunClass
{

    public static function run($className, $methodName)
    {
        $className .= 'Controller';
        if(!class_exists($className)){
            self::_error();
        }
        $obj = new $className ();
        $obj->$methodName ();
    }

    public static function _error()
    {
        echo json_encode(['code' => '900', 'msg' => '加载的类不存在']);
        exit();
    }

}

RunClass::run(Run::req('action'), Run::req('run'));