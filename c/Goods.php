<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/20
 * Time: 16:31
 *
 */
final class GoodsController extends Base
{
    public function getGoodsDetail($_gnum)
    {
        $obj = new GoodsModel();
        return $obj->getGoods('g_num="' . $_gnum . '"', '', '', '1');
    }
}