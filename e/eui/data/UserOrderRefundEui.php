<?php

final class UserOrderRefundEuiController extends Base
{

    public function getUserOrderRefundList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new UserOrderRefundModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getUserOrderRefund($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getUserOrderRefund($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_id = Run::req('_id');
        $_uid = Run::req('_uid');
        $_rid = Run::req('_rid');
        $_wxrid = Run::req('_wxrid');

        if (!$_uid && !$_id && !$_rid && !$_wxrid) {
            $w = '';
        } else {
            $w = '1';
        }
        if ($_id) {
            $w .= ' and oid="' . $_id . '" ';
        }
        if ($_uid) {
            $w .= ' and uid="' . $_uid . '" ';
        }
        if ($_rid) {
            $w .= ' and refund_oid="' . $_rid . '" ';
        }
        if ($_wxrid) {
            $w .= ' and wx_refund_code="' . $_wxrid . '" ';
        }

        return $w;
    }

}