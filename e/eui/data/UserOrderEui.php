<?php

final class UserOrderEuiController extends Base
{

    public function getUserOrderList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new UserOrderModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getUserOrder($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getUserOrder($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    private function getWhere()
    {
        $w = ' 1 ';
        $_id = Run::req('_id');
        $_uid = Run::req('_uid');
        $_paycode = Run::req('_paycode');
        $_start = Run::req('_start');
        $_end = Run::req('_end');
        $_day = Run::req('_day');

        if (!$_uid && !$_id && !$_paycode && !$_start && !$_end && !$_day) {
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
        if ($_paycode) {
            $w .= ' and wx_code="' . $_paycode . '" ';
        }
        if (empty($_day)) {
            if ($_start) {
                $w .= ' and rent_date_start >= "' . $_start . ' 00:00:00" ';
            }
            if ($_end) {
                $w .= ' and rent_date_start <= "' . $_end . ' 23:59:59" ';
            }
        } else {
            $_overdate = date('Y-m-d', strtotime('-' . $_day . ' day'));
            $w .= ' and is_pay=1 and is_return=0 and is_end=0 and rent_date_end like "' . $_overdate . '%"';
        }
        return $w;
    }

    public function updateAbnormal()
    {
        $_oid = Run::req('_oid');
        $obj = new UserOrderModel();
        $obj->setField('id');
        $rs = $obj->getUserOrder('oid="' . $_oid . '"', '', '', '1');
        $updRs = $obj->setUserOrder($rs['id'], ['is_abnormal' => 1, 'refund_money' => 0, 'real_refund_money' => 0]);
        if ($updRs) {
            echo '更新为异常订单成功，请在结算端强制结算。';
            exit();
        } else {
            echo '更新为异常订单失败';
            exit();
        }
    }

}