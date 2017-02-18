<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/15
 * Time: 20:04
 */
final class GoodsSupplyRecordController extends Base
{
    public function applyCabinetPwd()
    {
        $_cnum = Run::req('cnum');
        $obj = new CabinetController();
        $rs = $obj->getCabinetDetail($_cnum, 'c_pwd');
        if (empty($rs)) {
            $this->_jsonEn('0', '柜子不存在');
        } else {
            $this->_addApplyCabinetPwdRecord($_cnum);
            //通知箱子，更新箱子
            //$rs = $this->noticeCabinet();
            $this->_jsonEn('1', $rs['c_pwd']);
        }
    }

    /**
     * 查询配送人员的取货记录，最近3次
     * @param $_cnum
     * @return array
     */
    public function getApplyCabinetPwdRecord($_cnum)
    {
        $user = $this->getUserDetail();
        $obj = new CabinetPickGoodsStsModel();
        $rs = $obj->getCabinetPickGoodsSts('cr_c_num="' . $_cnum . '" and cr_user_id="' . $user['id'] . '"', '', '3');
        if (empty($rs)) {
            return [];
        } else {
            return $rs;
        }
    }

    /**
     * 查询配送人员开关补货 记录
     * @param $_cnum
     * @return array
     */
    public function getApplyGoodsSupplyRecord($_cnum)
    {
        $user = $this->getUserDetail();
        $obj = new GoodsSupplyRecordModel();
        $obj->setField('r_g_num,(select g_name from goods where g_num=r_g_num) as r_g_name,r_sts,created_at');
        $rs = $obj->getGoodsSupplyRecord('r_c_num="' . $_cnum . '"', '', '3');
        if (empty($rs)) {
            return [];
        } else {
            return $rs;
        }
    }

    /**
     * 添加柜子回收密码申请记录操作
     *
     * @param $_cnum
     */
    private function _addApplyCabinetPwdRecord($_cnum)
    {
        $user = $this->getUserDetail();
        $obj = new UsersInfoSysController();
        $userinfo = $obj->getUsersInfoSysUid($user['id']);
        $data['cr_c_num'] = $_cnum;
        $data['cr_user_id'] = $userinfo['uid'];
        $data['cr_user_name'] = $userinfo['u_name'];
        $data['cr_goods_num'] = $this->_getCabinetOrderRecoveryGoods($_cnum);
        $data['created_at'] = date('Y-m-d H:i:s');
        $obj = new CabinetPickGoodsStsModel();
        $obj->addCabinetPickGoodsSts($data);
    }

    /**
     * 更新柜子回收密码
     * @param $_cnum
     */
    private function _updateCabinetPwd($_cnum)
    {
        $obj = new CabinetController();
        $rs = $obj->getCabinetDetail($_cnum);
        $rs['c_pwd'] = $this->getRandomString(6);
        $rs['updated_at'] = date('Y-m-d H:i:s');
        $obj = new CabinetModel();
        $obj->setCabinet($rs['id'], $rs);
    }

    /**
     * 根据柜子产生的订单，并且订单归还的状态来确定取的货物数量
     * @param $_cnum 柜子号
     * @return int
     */
    private function _getCabinetOrderRecoveryGoods($_cnum)
    {
        return 1;
    }

    public function applyCabinetGridGoodsPwd()
    {
        $gnum = Run::req('gnum');
        $cnum = Run::req('cnum');

        $obj = new GoodsGridRelationModel();
        $rs = $obj->getGoodsGridRelation('c_num="' . $cnum . '" and g_num="' . $gnum . '" and sts="3"', '', '', '1');
        if (empty($rs)) {
            $this->_jsonEn('0', '商品未分拣，请联系分拣人');
        } else {
            //添加申请记录
            $this->_addGoodsSupplyRecord($rs);
            //通知柜子
            //线上更新格子密码
            $this->_updateGoodsGridRelationPwd($rs, $obj);
            $this->_jsonEn('1', $rs['pwd']);
        }
    }

    private function _updateGoodsGridRelationPwd($rs, GoodsGridRelationModel $obj)
    {
        $rs['pwd'] = $this->getRandomString();
        $obj->setGoodsGridRelation($rs['id'], $rs);
    }

    private function _addGoodsSupplyRecord($rs)
    {
        $user = $this->getUserDetail();
        $obj = new UsersInfoSysController();
        $userinfo = $obj->getUsersInfoSysUid($user['id']);
        $obj = new GoodsSupplyRecordModel();
        $data['r_c_num'] = $rs['c_num'];
        $data['r_st_num'] = $rs['st_num'];
        $data['r_g_num'] = $rs['g_num'];
        $data['r_sts'] = '0';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['r_user_id'] = $userinfo['uid'];
        $data['r_user_name'] = $userinfo['u_name'];
        $obj->addGoodsSupplyRecord($data);
    }

}