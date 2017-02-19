<?php

final class UsersInfoSysEuiController extends Base
{

    public function getUsersInfoSysList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new UsersInfoSysModel ();
        $ueObj->setField('*,(select sName from website_type where id=u_city) as u_city_name');
        $w = $this->getWhere();
        $res = $ueObj->getUsersInfoSys($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getUsersInfoSys($w, '', '', '1');
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

    public function saveUsersInfoSys()
    {
        $_id = Run::req('id');

        $dataArray ['uid'] = Run::req('uid');
        $dataArray ['u_name'] = Run::req('u_name');
        $dataArray ['u_city'] = Run::req('u_city');
        $dataArray ['u_area'] = Run::req('u_area');
        $dataArray ['u_cabinet'] = Run::req('u_cabinet');

        $sObj = new UsersInfoSysModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setUsersInfoSys($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addUsersInfoSys($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}