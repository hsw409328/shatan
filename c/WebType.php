<?php

final class WebTypeController extends Base
{

    /**
     * 取得父级下的子列表
     * @param $pid 父级ID
     */
    public function getParentChilList($pid)
    {
        $obj = new WebTypeModel();
        $obj->setField('id,sName');
        $rs = $obj->getWebType('parentId="' . $pid . '" and wStatus=1');
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }

}