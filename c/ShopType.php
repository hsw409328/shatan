<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/15
 * Time: 20:04
 */
final class ShopTypeController extends Base
{
    public function getShopTypeDetail($st_num)
    {
        $obj = new ShopTypeModel();
        return $obj->getShopType('st_num="' . $st_num . '"', '', '', '1');
    }

}