<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/15
 * Time: 20:04
 */
final class CabinetController extends Base
{
    /**
     * 获取柜子详细信息
     * @param $c_num 柜子编号
     * @param $filed 字段名
     * @return array|null
     */
    public function getCabinetDetail($c_num, $filed = '*')
    {
        $obj = new CabinetModel();
        $obj->setField($filed);
        $rs = $obj->getCabinet('c_num="' . $c_num . '"', '', '', '1');
        return $rs;
    }

}