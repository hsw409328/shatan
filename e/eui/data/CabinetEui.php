<?php

final class CabinetEuiController extends Base
{

    public function getCabinetList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new CabinetModel ();
        $ueObj->setField(' *,(select sName from website_type w where w.id=c_type) as c_type_name ');
        $w = $this->getWhere();
        $res = $ueObj->getCabinet($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getCabinet($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_wtype = Run::req('_wtype');
        $_id = Run::req('_id');
        if (!$_wtype && !$_id) {
            $w = '';
        } else {
            $w = '1';
        }
        if ($_wtype) {
            $w .= ' and c_type="' . $_wtype . '" ';
        }
        if ($_id) {
            $w .= ' and c_num="' . $_id . '" ';
        }
        return $w;
    }

    public function updateStatus()
    {
        $_s = Run::req('_s');
        $_id = Run::req('_id');
        $obj = new CabinetModel ();
        $data ['c_status'] = $_s;
        $data ['updated_at'] = date('Y-m-d H:i:s');
        $res = $obj->setCabinet($_id, $data);
        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

    public function saveCabinet()
    {
        $_id = Run::req('id');

        $dataArray ['c_num'] = Run::req('c_num');
        $dataArray ['c_type'] = Run::req('c_type');
        $dataArray ['c_lat'] = Run::req('c_lat');
        $dataArray ['c_lng'] = Run::req('c_lng');
        $dataArray ['c_province'] = Run::req('c_province');
        $dataArray ['c_city'] = Run::req('c_city');
        $dataArray ['c_area'] = Run::req('c_area');
        $dataArray ['c_hotel'] = Run::req('c_hotel');
        $dataArray ['c_address'] = Run::req('c_address');
        $dataArray ['c_status'] = Run::req('c_status');
        $dataArray ['c_pwd'] = $this->_getPwd('A21');

        $sObj = new CabinetModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setCabinet($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addCabinet($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}