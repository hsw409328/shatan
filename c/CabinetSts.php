<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/15
 * Time: 20:04
 */
final class CabinetStsController extends Base
{

    private function _getUserInfoSys()
    {
        $_user = $this->getUserDetail();
        $obj = new UsersInfoSysController();
        $u_info_rs = $obj->getUsersInfoSysUid($_user['id']);
        return $u_info_rs;
    }

    /**
     * 每日初始化柜子
     */
    public function initDayCabinet()
    {
        $u_info_rs = $this->_getUserInfoSys();
        if (empty($u_info_rs)) {
            return null;
        } else {
            $cabinet = explode(',', $u_info_rs['u_cabinet']);
            $cObj = new CabinetStsModel();
            foreach ($cabinet as $k => $v) {
                if (!empty($v)) {
                    $this->_checkIsCreateCabinetSts($v, $cObj);
                }
            }
        }
    }

    /**
     * 校验某个管理人员所管理的柜子是否已经创建
     */
    private function _checkIsCreateCabinetSts($cabinet_num, CabinetStsModel $cObj)
    {
        $w = 'created_at like "' . date('Y-m-d') . ' %" and c_num="' . $cabinet_num . '" ';
        $rs = $cObj->getCabinetSts($w, '', '', '1');
        if (empty($rs)) {
            $data['c_num'] = $cabinet_num;
            $data ['sorting_sts'] = 0;
            $data ['send_sts'] = 0;
            $data ['clear_sts'] = 0;
            $data ['account_sts'] = 0;
            $data ['created_at'] = date('Y-m-d H:i:s');
            $data ['sorting_user_num'] = '';
            $data ['send_user_num'] = '';
            $data ['clear_user_num'] = '';
            $data ['account_user_num'] = '';
            $cObj->addCabinetSts($data);
        }
    }

    /**
     * 获取登录用户所管理的柜子
     * @return array|null
     */
    public function getUserManagerCabinet()
    {
        $_u_info = $this->_getUserInfoSys();
        if (empty($_u_info)) {
            return null;
        } else {
            $cabinet_list = explode(',', $_u_info['u_cabinet']);
            $cabinet_list = implode('","', $cabinet_list);
            $obj = new CabinetStsModel();
            $rs = $obj->getCabinetSts('c_num in ("' . $cabinet_list . '") and created_at like "' . date('Y-m-d') . '%"');
            if (empty($rs)) {
                return [];
            } else {
                return $rs;
            }
        }
    }

    /**
     * 根据柜子与用户ID查询最后一次取货记录
     * @return array|null
     */
    public function getCabinetPickGoodsStsEnd($cnum, $uid)
    {
        $obj = new CabinetPickGoodsStsModel();
        $obj->setField('created_at');
        $w = 'cr_c_num="' . $cnum . '" and cr_user_id="' . $uid . '" ';
        $rs = $obj->getCabinetPickGoodsSts($w, '', '1', '1');
        if (empty($rs)) {
            $rs = '未取货';
        } else {
            $rs = $rs['created_at'];
        }
        return $rs;
    }

    /**
     * 根据柜子与用户ID查询最后一次补货记录
     * @return array|null
     */
    public function getGoodsSupplyRecordEnd($cnum, $uid)
    {
        $obj = new GoodsSupplyRecordModel();
        $obj->setField('created_at');
        $w = 'r_c_num="' . $cnum . '" and r_user_id="' . $uid . '" ';
        $rs = $obj->getGoodsSupplyRecord($w, '', '1', '1');
        if (empty($rs)) {
            $rs = '未补货';
        } else {
            $rs = $rs['created_at'];
        }
        return $rs;
    }

    public function getUserManagerCabinetOne()
    {
        $c_num = RouteClass::getParams('3');
        $obj = new CabinetStsModel();
        return $obj->getCabinetSts('c_num = "' . $c_num . '" and created_at like "' . date('Y-m-d') . '%"', '', '', '1');
    }

    public function getStaticTodayCabinetSts($_sts)
    {
        $_str = '';
        switch ($_sts) {
            case 0:
                $_str = '今日未分拣';
                break;
            case 1:
                $_str = '今日已分拣';
                break;
        }
        return $_str;
    }

    /**
     * 根据柜子查询类目
     * @return array
     */
    public function getCabinetShopTypeList()
    {
        $c_num = RouteClass::getParams('3');
        $obj = new CgRelationModel();
        $obj->setField('st_num,c_grid_area,c_num');
        $rs = $obj->getCgRelation('c_num="' . $c_num . '"');
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }

    /**
     * 查询有多少件商品已经绑定及可用的格子
     * @param $c_num 柜子编号
     * @param $st_num 类目编号
     * @param  $grid_area 区域
     * @return int
     */
    public function getSortingNum($c_num, $st_num, $grid_area, $_return_array = false)
    {
        $grid_area = explode(',', $grid_area);
        $grid_area = array_filter($grid_area);
        if (count($grid_area) == 0) {
            if ($_return_array) {
                return [];
            }
            return 0;
        }
        $obj = new GoodsGridRelationModel();
        //$obj->setField('count(id) as num');
        $obj->setField('grid_num,sts');
        $rs = $obj->getGoodsGridRelation('c_num=\'' . $c_num . '\' AND st_num=\'' . $st_num . '\'');
        if (count($rs) == '0') {
            //如果为0，需要根据类目的区域进行填充全部的商品
            if (!$_return_array) {
                return count($grid_area);
            } else {
                return array_values($grid_area);
            }
        } else {
            //存储不可分拣的格子
            $_tmpRs = [];
            foreach ($rs as $k => $v) {
                if (in_array($v['grid_num'], $grid_area) && $v['sts'] != '6') {
                    $_tmpRs[] = $v['grid_num'];
                }
            }
            //去除不可分拣的格子
            foreach ($_tmpRs as $v) {
                $_k = array_search($v, $grid_area);
                unset($grid_area[$_k]);
            }
            $grid_area = array_values($grid_area);
            if (!$_return_array) {
                return count($grid_area);
            } else {
                return $grid_area;
            }
        }
    }

    /**
     * 更新柜子状态
     */
    public function updateCabinetSts()
    {
        $cnum = Run::req('cnum');
        $user = $this->getUserDetail();
        $obj = new CabinetStsModel();
        $obj->setField('id');
        $rs = $obj->getCabinetSts('c_num="' . $cnum . '" and created_at like "' . date('Y-m-d') . ' %"', '', '', '1');
        $data['sorting_sts'] = 1;
        $data['sorting_user_num'] = $user['id'];
        $data['updated_at'] = date('Y-m-d H:i:s');
        $rs = $obj->setCabinetSts($rs['id'], $data);
        if ($rs) {
            $this->_jsonEn('1', '更新状态成功');
        } else {
            $this->_jsonEn('0', '更新状态失败');
        }
    }

    /**
     * 向格子添加一个商品
     */
    public function addCabinetGridGoods()
    {
        $_c_num = Run::req('cnum');
        $_grid_area = Run::req('gridarea');
        $_st_num = Run::req('stnum');
        $_num = Run::req('val');
        $rs = $this->getSortingNum($_c_num, $_st_num, $_grid_area, true);
        if (empty($rs)) {
            $this->_jsonEn('0', '该库存已满');
        }
        $obj = new GoodsModel();
        $obj->setField('g_num');
        $gRs = $obj->getGoods('g_st_num="' . $_st_num . '" and g_num="' . $_num . '"', '', '', '1');
        if (empty($gRs)) {
            $this->_jsonEn('0', '商品与类目不对应');
        }
        $obj = new GoodsGridRelationModel();
        $obj->setField('id');
        $ggrRs = $obj->getGoodsGridRelation('g_num="' . $_num . '"', '', '', '1');
        if (!empty($ggrRs)) {
            //$this->_jsonEn('0', '该商品已经存在，请勿重新添加');
        }
        //var_dump($rs);exit;
        $w = 'c_num="' . $_c_num . '"  and st_num="' . $_st_num . '"  and grid_num="' . $rs[0] . '" ';
        $ggrRs = $obj->getGoodsGridRelation($w, '', '', '1');
        if (empty($ggrRs)) {
            $this->_addGoodsGridRelation($rs[0], $obj);
        } else {
            $this->_updateGridRelation($ggrRs['id'], $rs[0], $obj);
        }
    }

    private function _setGoodsGridRelationData($grid)
    {
        $dataArray ['c_num'] = Run::req('cnum');
        $dataArray ['st_num'] = Run::req('stnum');
        $dataArray ['g_num'] = Run::req('val');
        $dataArray ['pwd'] = $this->_getPwd($grid);
        $dataArray ['sts'] = '3';
        return $dataArray;
    }

    private function _addGoodsGridRelation($grid, GoodsGridRelationModel $sObj)
    {
        $dataArray = $this->_setGoodsGridRelationData($grid);
        $dataArray ['grid_num'] = $grid;
        $dataArray ['created_at'] = date('Y-m-d H:i:s');
        $res = $sObj->addGoodsGridRelation($dataArray);

        if ($res) {
            $this->_jsonEn('1', '添加商品成功');
        } else {
            $this->_jsonEn('0', '添加商品失败');
        }
    }

    private function _updateGridRelation($id, $grid, GoodsGridRelationModel $sObj)
    {
        $dataArray = $this->_setGoodsGridRelationData($grid);
        $dataArray ['grid_num'] = $grid;
        $dataArray ['updated_at'] = date('Y-m-d H:i:s');
        $res = $sObj->setGoodsGridRelation($id, $dataArray);

        if ($res) {
            $this->_jsonEn('1', '添加商品成功');
        } else {
            $this->_jsonEn('0', '添加商品失败');
        }
    }

    /**
     * 获取已经分拣的商品编号
     */
    public function getGoodsGridRelationByCnumAndStnum()
    {
        $cnum = Run::req('cnum');
        $stnum = Run::req('stnum');
        $obj = new GoodsGridRelationModel();
        $obj->setField('g_num');
        $rs = $obj->getGoodsGridRelation('c_num="' . $cnum . '" and st_num="' . $stnum . '" and sts<>6');
        if (empty($rs)) {
            $this->_jsonEn('1', '');
        } else {
            $str = '';
            foreach ($rs as $k => $v) {
                $str .= $v['g_num'] . ',';
            }
            $this->_jsonEn('1', $str);
        }
    }

}