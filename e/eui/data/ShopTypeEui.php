<?php

final class ShopTypeEuiController extends Base
{

    public function getShopTypeList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new ShopTypeModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getShopType($w, 'st_sort asc,id desc', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getShopType($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    public function getShopTypeCombobox()
    {
        $obj = new ShopTypeModel();
        $obj->setField('st_num as id,st_name as text');
        $rs = $obj->getShopType();
        echo json_encode($rs);
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
        if ($_keyword) {
            $w .= ' and st_name like "%' . $_keyword . '%" ';
        }
        if ($_id) {
            $w .= ' and st_num="' . $_id . '" ';
        }
        return $w;
    }

    public function saveShopType()
    {
        $_id = Run::req('id');

        $dataArray ['st_num'] = Run::req('st_num');
        $dataArray ['st_name'] = Run::req('st_name');
        $dataArray ['st_span'] = Run::req('st_span');
        $dataArray ['st_info'] = Run::req('st_info');
        $dataArray ['st_bad_info'] = Run::req('st_bad_info');
        $dataArray ['st_money'] = Run::req('st_money');
        $dataArray ['st_day'] = Run::req('st_day');
        $dataArray ['st_leveal'] = Run::req('st_leveal');
        $dataArray ['st_sort'] = Run::req('st_sort');
        $listImg = $this->uploadImg($_FILES ['st_img_path']);
        if ($listImg) {
            $dataArray ['st_img'] = $listImg;
        } else {
            $dataArray ['st_img'] = Run::req('st_img');
        }
        $listImg = $this->uploadImg($_FILES ['st_img_path1']);
        if ($listImg) {
            $dataArray ['st_img1'] = $listImg;
        } else {
            $dataArray ['st_img1'] = Run::req('st_img1');
        }
        $listImg = $this->uploadImg($_FILES ['st_img_path2']);
        if ($listImg) {
            $dataArray ['st_img2'] = $listImg;
        } else {
            $dataArray ['st_img2'] = Run::req('st_img2');
        }
        $listImg = $this->uploadImg($_FILES ['st_img_path3']);
        if ($listImg) {
            $dataArray ['st_img3'] = $listImg;
        } else {
            $dataArray ['st_img3'] = Run::req('st_img3');
        }

        $sObj = new ShopTypeModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setShopType($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addShopType($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}