<?php

final class UserOrderDamageEuiController extends Base
{

    public function getUserOrderDamageList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new UserOrderDamageModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getUserOrderDamage($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getUserOrderDamage($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_id = Run::req('_id');
        $_uid = Run::req('_uid');

        if (!$_uid && !$_id) {
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

        return $w;
    }

}