<?php

final class GoodsEuiController extends Base
{

    public function getGoodsList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new GoodsModel ();
        $ueObj->setField(' *,(select st_name from shop_type w where w.st_num=g_st_num) as g_st_name ');
        $w = $this->getWhere();
        $res = $ueObj->getGoods($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getGoods($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    public function getGoodsStNumList()
    {
        $id = Run::req('id');
        $obj = new GoodsModel();
        $obj->setField('g_name as text,g_num as id');
        $rs = $obj->getGoods('g_st_num="' . $id . '"');
        echo json_encode($rs);
        exit();
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_keyword = Run::req('_keyword');
        $_wtype = Run::req('_wtype');
        $_id = Run::req('_id');
        if (!$_keyword && !$_wtype && !$_id) {
            $w = '';
        } else {
            $w = '1';
        }
        if ($_wtype) {
            $w .= ' and g_st_num ="' . $_wtype . '" ';
        }
        if ($_id) {
            $w .= ' and g_num="' . $_id . '" ';
        }
        if ($_keyword) {
            $w .= ' and g_name like "%' . $_keyword . '%" ';
        }
        return $w;
    }

    public function updateStatus()
    {
        $_s = Run::req('_s');
        $_id = Run::req('_id');
        $obj = new GoodsModel ();
        $data ['updated_at'] = date('Y-m-d H:i:s');
        $res = $obj->setGoods($_id, $data);
        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

    private function _checkGoodsNum($g_num)
    {
        $obj = new GoodsModel();
        $rs = $obj->getGoods('g_num="' . $g_num . '"', '', '', '1');
        if ($rs) {
            echo '1-商品编号已经存在，请重新输入';
            exit();
        }
    }

    public function saveGoods()
    {
        $_id = Run::req('id');

        $dataArray ['g_num'] = Run::req('g_num');
        if (empty($_id)) {
            $this->_checkGoodsNum($dataArray['g_num']);
        }
        $dataArray ['g_st_num'] = Run::req('g_st_num');
        $dataArray ['g_name'] = Run::req('g_name');
        $dataArray ['g_introduce'] = Run::req('g_introduce');
        $dataArray ['g_content'] = Run::req('g_content');

        $listImg = $this->uploadImg($_FILES ['upload']);
        if ($listImg) {
            $dataArray ['g_list_img_path'] = $listImg;
        } else {
            $dataArray ['g_list_img_path'] = Run::req('g_list_img_path');
        }

        $listImg1 = $this->uploadImg($_FILES ['upload1']);
        if ($listImg1) {
            $dataArray ['g_check_img'] = $listImg1;
        } else {
            $dataArray ['g_check_img'] = Run::req('g_check_img');
        }

        $dataArray ['g_img_content'] = '';
        $dataArray ['g_deposit'] = Run::req('g_deposit');
        $dataArray ['g_rent'] = Run::req('g_rent');
        $dataArray ['g_buy_num'] = Run::req('g_buy_num');
        $dataArray ['g_check_info'] = Run::req('g_check_info');

        $sObj = new GoodsModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setGoods($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addGoods($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}