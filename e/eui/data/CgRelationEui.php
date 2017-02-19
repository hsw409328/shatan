<?php

final class CgRelationEuiController extends Base
{

    public function getCgRelationList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new CgRelationModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getCgRelation($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getCgRelation($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    public function getCgRelationCabinet()
    {
        $obj = new CabinetModel();
        $obj->setField('c_num as id,c_num as text');
        $rs = $obj->getCabinet();
        echo json_encode($rs);
        exit();
    }

    public function getCgRelationStNum()
    {
        $id = Run::req('id');
        $obj = new CgRelationModel();
        $obj->setField('st_num as id,(select st_name from shop_type where shop_type.st_num=c_g_relation.st_num) as text');
        $rs = $obj->getCgRelation('c_num="' . $id . '"');
        echo json_encode($rs);
        exit();
    }

    public function getCgRelationGridArea()
    {
        $c_num = Run::req('c_num');
        $st_num = Run::req('st_num');
        $obj = new CgRelationModel();
        $obj->setField('c_grid_area');
        $rs = $obj->getCgRelation('c_num="' . $c_num . '" and st_num="' . $st_num . '"', '', '', '1');
        if (empty($rs)) {
            echo json_encode([]);
            exit();
        }
        $_tmp = [];
        $rs = explode(',', $rs['c_grid_area']);
        foreach ($rs as $k => $v) {
            if (!empty($v)) {
                $_tmp[$k - 1]['id'] = $v;
                $_tmp[$k - 1]['text'] = $v;
            }
        }
        echo json_encode($_tmp);
        exit();
    }

    public function getCgRelationConfigCabinet()
    {
        $rs = ConfigCabinet::getAllKey();
        $_tmp = [];
        foreach ($rs as $k => $v) {
            $_tmp[$k]['id'] = $v;
            $_tmp[$k]['text'] = $v;

        }
        echo json_encode($_tmp);
        exit();
    }

    public function getCgRelationConfigCabinetList()
    {
        $_kid = Run::req('_kid');
        $rs = ConfigCabinet::getAllGridByGroup($_kid);
        $_tmp = [];
        foreach ($rs as $k => $v) {
            $_tmp[$k]['id'] = $v;
            $_tmp[$k]['text'] = $v;

        }
        echo json_encode($_tmp);
        exit();
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_keyword = Run::req('_keyword');
        $_id = Run::req('_id');
        if (!$_keyword && !$_id) {
            $w = '';
        } else {
            $w = '1';
        }
        if ($_id) {
            $w .= ' and st_num="' . $_id . '" ';
        }
        return $w;
    }


    public function updateStatus()
    {
        $_s = Run::req('_s');
        $_id = Run::req('_id');
        $obj = new CgRelationModel ();
        $data ['cgr_status'] = $_s;
        $data ['updated_at'] = date('Y-m-d H:i:s');
        $res = $obj->setCgRelation($_id, $data);
        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

    /**
     * 判断格子是否已经被绑定过商品类目
     * @param $c_num 柜子编号
     * @param $c_grid 格子区域
     * @param $c_config_key 柜子具体类型
     * @param $st_num 商品类目
     */
    private function _checkCgRelationHave($c_num, $c_grid, $c_config_key, $st_num)
    {
        $_id = Run::req('id');

        $obj = new CgRelationModel();
        if (empty($_id)) {
            $stRs = $obj->getCgRelation("st_num='{$st_num}' and c_num='{$c_num}'", '', '', '1');
            if (!empty($stRs)) {
                echo "{$st_num}类目已经存在，请进行编辑";
                exit();
            }
        }
        $c_grid_list = explode(',', $c_grid);
        if (empty($c_grid)) {
            /*echo '格子区域不能为空';
            exit();*/
        } else {
            foreach ($c_grid_list as $v) {
                if (!empty($v)) {
                    $w = "c_num='{$c_num}' and cabinet_config_key='{$c_config_key}' and c_grid_area like '%{$v}%'";
                    $tmpRs = $obj->getCgRelation($w, '', '', 1);
                    if (!empty($tmpRs)) {
                        echo "{$v}格子已经和{$tmpRs['st_num']}类目绑定，请重新选择";
                        exit();
                    }
                }
            }
        }
    }

    public function saveCgRelation()
    {
        $_id = Run::req('id');

        $dataArray ['cabinet_config_key'] = Run::req('cabinet_config_key');
        $dataArray ['c_num'] = Run::req('c_num');
        $dataArray ['c_grid_area'] = Run::req('c_grid_area');
        $dataArray ['st_num'] = Run::req('st_num');
        $dataArray ['cgr_status'] = Run::req('cgr_status');

        $this->_checkCgRelationHave($dataArray ['c_num'], $dataArray ['c_grid_area'], $dataArray ['cabinet_config_key'], $dataArray ['st_num']);

        $sObj = new CgRelationModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setCgRelation($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addCgRelation($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}