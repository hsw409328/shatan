<?php

final class CBagEuiController extends Base
{

    public function getCBagList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new CBagModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getCBag($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getCBag($w, '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
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
            $w .= ' and c_bag_num="' . $_id . '" ';
        }
        return $w;
    }

    public function saveCBag()
    {
        $_id = Run::req('id');

        $dataArray ['c_num'] = Run::req('c_num');
        $dataArray ['c_bag_num'] = Run::req('c_bag_num');

        $sObj = new CBagModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setCBag($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addCBag($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}