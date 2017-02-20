<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/20
 * Time: 12:15
 */
final class ApiController extends Base
{
    private $_sign = '';
    private $_appsecret = 'biuzx1cABv43pi2yqUZwePFVFr3pafI8';

    /**
     * 校验用户签名
     * @param $_t 时间戳
     * @param $c 柜子号
     * @param $p 密码
     * @param $sign 用户的签名
     */
    private function _checkSign($_t, $c, $p, $sign)
    {
        //柜子编号注意大小写
        //密钥+时间戳+柜子+密码 注意顺序  最后md5操作
        $this->_sign = md5($this->_appsecret . $_t . $c . $p);
        if ($this->_sign != $sign) {
            $this->_jsonEn(501, '签名错误');
        } else {
            return true;
        }
    }

    /**
     * 开门操作
     */
    public function getGridXY()
    {
        //柜子+密码
        $cnum = Run::req('c_num');
        $pwd = Run::req('pwd');
        $t = Run::req('t');
        $sign = Run::req('sign');
        $this->_checkSign($t, $cnum, $pwd, $sign);
        //确定商品及格子
        $obj = new GoodsGridRelationModel();
        $gridnum = $this->_parsePwd($pwd);
        $obj->setField('id,grid_num,sts');
        $w = 'c_num="' . $cnum . '" and pwd="' . $pwd . '" and grid_num="' . $gridnum . '" and sts in (3,5)';
        $rs = $obj->getGoodsGridRelation($w, '', '', '1');
        if (empty($rs)) {
            $this->_jsonEn(404, '密码输入错误');
        } else {
            if ($rs['sts'] == '5') {
                $this->_updateUserOrderIsPickUp($cnum, $pwd, $gridnum);
            }
            $this->_updateGridPwdSts($rs['id'], $gridnum, $rs['sts'], $obj);
            $this->_jsonEn(200, ['c_grid_num' => $rs['grid_num']]);
        }
    }

    /**
     *
     * 更新用户订单取货状态
     *
     * @param $cnum
     * @param $pwd
     * @param $grid
     */
    private function _updateUserOrderIsPickUp($cnum, $pwd, $grid)
    {
        $obj = new UserOrderDetailModel();
        $obj->setField('oid');
        $w = 'cnum="' . $cnum . '" and pwd="' . $pwd . '" and gridnum="' . $grid . '"';
        $rs = $obj->getUserOrderDetail($w, '', '', '1');
        if (empty($rs)) {
            $this->wlog(APP_PATH . 'public/log/cabinet-op', $cnum . '-' . $pwd . '-' . $grid . '-not-find-order');
        }
        $obj = new UserOrderModel();
        $obj->setField('id,oid');
        $w = 'oid="' . $rs['oid'] . '"';
        $rs = $obj->getUserOrder($w, '', '', '1');
        $endRs = $obj->setUserOrder($rs['id'], ['is_pickup' => 1]);
        if (empty($endRs)) {
            $this->wlog(APP_PATH . 'public/log/cabinet-op', $rs['oid'] . '-update-order-error');
        }
    }

    /**
     * 更新格子密码及状态
     */
    private function _updateGridPwdSts($id, $grid, $sts, GoodsGridRelationModel $obj)
    {
        if ($sts == '5') {
            $data['sts'] = '6';
        }
        $data['pwd'] = $this->_getPwd($grid);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $endRs = $obj->setGoodsGridRelation($id, $data);
        if (empty($endRs)) {
            $this->wlog(APP_PATH . 'public/log/cabinet-op', $id . '-update-grid-error');
        }
    }

    /**
     * 关门操作
     */
    public function setGridXY()
    {
        //柜子+格子
        $cnum = Run::req('c_num');
        $grid = Run::req('grid');
        $grid = strtoupper($grid);

        //确定商品状态
        $obj = new GoodsGridRelationModel();
        $obj->setField('id,grid_num,sts,g_num,st_num');
        $w = 'c_num="' . $cnum . '" and grid_num="' . $grid . '" ';
        $rs = $obj->getGoodsGridRelation($w, '', '', '1');
        if ($rs['sts'] == '3') {
            $obj->setGoodsGridRelation($rs['id'], [
                'updated_at' => date('Y-m-d H:i:s'),
                'pwd' => $this->_getPwd($rs['grid_num']),
                'sts' => 4]);
            $this->_updateGoodsSupplyRecordSts($cnum, $rs['st_num'], $rs['g_num']);
            $this->_jsonEn(200, '配送员关门完成');
        } else {
            if ($rs['sts'] == '4') {
                $this->_jsonEn(405, '配送状态已经完成');
            } else {
                $this->_jsonEn(200, '订单完成');
            }
        }
    }

    private function _updateGoodsSupplyRecordSts($cnum, $stnum, $gnum)
    {
        $obj = new GoodsSupplyRecordModel();
        $obj->setField('id');
        $w = 'r_c_num="' . $cnum . '" and r_st_num="' . $stnum . '" and r_g_num="' . $gnum . '" ';
        $rs = $obj->getGoodsSupplyRecord($w, '', '1', '1');
        if (empty($rs)) {
            $this->wlog(APP_PATH . 'public/log/cabinet-op', $cnum . '-' . $gnum . '-update-send-error');
        }
        $obj->setGoodsSupplyRecord($rs['id'], ['r_sts' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function getWeather()
    {
        $city = Run::req('city');
        if (!empty($city)) {
            $obj = new CityWeatherModel();
            $rs = $obj->getCityWeather('city="' . $city, '"', '', '1');
            if (empty($rs)) {
                $this->_jsonEn(403, '查询结果为空');
            } else {
                $_endData['tide'] = $rs['zhangchao'];
                $_endData['ebb'] = $rs['tuichao'];
                $_endData['sunup'] = $rs['richu'];
                $_endData['sundown'] = $rs['riluo'];
                $_endData['background'] = $rs['img_path'];
                $this->_jsonEn(200, $_endData);
            }
        } else {
            $this->_jsonEn(402, '参数错误，未检测到city或者为空');
        }
    }
}