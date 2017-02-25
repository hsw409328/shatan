<?php

final class UserOrderDetailEuiController extends Base
{

    public function getUserOrderDetailList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new UserOrderDetailModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getUserOrderDetail($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getUserOrderDetail($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_oid = Run::req('_oid');
        if (!$_oid) {
            $w = '';
        } else {
            $w = '1';
        }
        if ($_oid) {
            $w .= ' and oid="' . $_oid . '" ';
        }
        return $w;
    }

}