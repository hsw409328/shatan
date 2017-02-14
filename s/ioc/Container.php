<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/13
 * Time: 21:46
 */
class Container
{
    protected $binds;
    protected $instances;

    public function bind($abstruct, $concrete)
    {
        if ($concrete instanceof Closure) {
            $this->binds[$abstruct] = $concrete;
        } else {
            $this->instances[$abstruct] = $concrete;
        }
    }

    public function make($abstruct, $parameters = [])
    {
        if (isset($this->instances[$abstruct])) {
            return $this->instances[$abstruct];
        }

        array_unshift($parameters, $this);
        return call_user_func_array($this->binds[$abstruct], $parameters);
    }
}

class A
{

    public function __construct($a, $b)
    {
        $this->t();
    }

    public function t()
    {
        echo __METHOD__;
    }
}

class B
{
    public static function getTime()
    {
        $a = microtime();
        for($i=1;$i<1000;$i++){
            echo str_replace('.','',$a),'<br>';
        }
    }
}

$container = new Container;

/*$container->bind('a', function ($container, $moduleName) {
    return new A;
});

$container->make('a', [1, 2]);*/
$container->bind('b', B::getTime());
//$container->make('b', [1, 2]);