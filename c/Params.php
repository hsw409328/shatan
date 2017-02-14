<?php

final class ParamsController
{

    public static function setParams()
    {

        foreach ($_REQUEST as $k => $v) {
            $_SESSION [APP_NAME] [$k] = strip_tags($v);
        }

    }

    public static function localSetParams($k, $v)
    {
        $_SESSION [APP_NAME] [$k] = $v;
    }

    public static function getSessionParams($str)
    {
        if (isset ($_SESSION [APP_NAME] [$str])) {
            return $_SESSION [APP_NAME] [$str];
        } else {
            return false;
        }
    }

    public static function getSessionAll()
    {
        return $_SESSION [APP_NAME];
    }

    public static function unsetSession($str)
    {
        unset ($_SESSION [APP_NAME] [$str]);
    }

    public static function unsetAll()
    {
        unset ($_SESSION [APP_NAME]);
    }

}