<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/17
 * Time: 13:26
 */
final class TianQiController extends Base
{
    public function getTianQi()
    {
        $obj = new CityWeatherModel();
        return $obj->getCityWeather('', '', '1', '1');
    }

}