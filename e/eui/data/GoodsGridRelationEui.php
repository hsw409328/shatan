<?php

final class GoodsGridRelationEuiController extends Base
{

    public function getGoodsGridRelationList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $ueObj = new GoodsGridRelationModel ();
        $ueObj->setField(' *,(select g_name from goods gs where gs.g_num=`goods_grid_relation`.g_num) as g_num_name ');
        $w = $this->getWhere();
        $res = $ueObj->getGoodsGridRelation($w, '', $limit);
        $ueObj->setField('count(id) as total');
        $totalRes = $ueObj->getGoodsGridRelation($w, '', '', '1');
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

    public function saveGoodsGridRelation()
    {
        $_id = Run::req('id');

        $dataArray ['c_num'] = Run::req('c_num');
        $dataArray ['st_num'] = Run::req('st_num');
        $dataArray ['g_num'] = Run::req('g_num');
        $dataArray ['grid_num'] = Run::req('grid_num');
        $dataArray ['pwd'] = Run::req('pwd');
        $dataArray ['sts'] = Run::req('sts');

        $sObj = new GoodsGridRelationModel ();
        if ($_id) {
            $dataArray ['updated_at'] = date('Y-m-d H:i:s');
            $res = $sObj->setGoodsGridRelation($_id, $dataArray);
        } else {
            $dataArray ['created_at'] = date('Y-m-d H:i:s');
            $res = $sObj->addGoodsGridRelation($dataArray);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

}