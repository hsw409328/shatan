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

    public function getCabinetBagNum()
    {
        $bnum = ParamsController::getSessionParams('js_cabinet_bag');
        $obj = new CBagModel();
        $obj->setField('c_num');
        $rs = $obj->getCBag('c_bag_num="' . $bnum . '"', '', '1', '1');
        if (empty($rs)) {
            ParamsController::unsetSession('js_cabinet_bag');
            Run::show_msg('配送袋子编号未找到');
        }
        return $rs['c_num'];
    }

    public function getCabinetPsUser($cnum)
    {
        $obj = new UsersInfoSysController();
        $rs = $obj->getUserInfoByCabinet($cnum);
        if (empty($rs)) {
            Run::show_msg('未找到相关配送人员');
        }
        return $rs;
    }

}