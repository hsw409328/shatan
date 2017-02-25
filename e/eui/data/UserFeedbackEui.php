<?php

final class UserFeedbackEuiController extends Base
{

    public function getUserFeedbackList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new UserFeedbackModel ();
        $ueObj->setField(' * ');
        $w = $this->getWhere();
        $res = $ueObj->getUserFeedback($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getUserFeedback($w, '', '', '1');
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
            $w .= ' and uid="' . $_id . '" ';
        }
        return $w;
    }

}