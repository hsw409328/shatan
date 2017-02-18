<?php

final class CabinetPickGoodsStsModel extends MysqliClass
{

    public $_f = '*';

    public function setField($f = '*')
    {
        $this->_f = $f;
    }

    public function getCabinetPickGoodsSts($where = '', $orderby = '', $limit = '', $solo = '')
    {
        $sql = 'select ' . $this->_f . ' from `cabinet_pick_goods_sts` ';
        if (!empty ($where)) {
            $sql .= ' where ' . $where;
        }
        if (!empty ($orderby)) {
            $sql .= ' order by ' . $orderby;
        } else {
            $sql .= ' order by id desc ';
        }
        if (!empty ($limit)) {
            $sql .= ' limit ' . $limit;
        }
        if (empty ($solo)) {
            $res = $this->get_all($sql);
        } else {
            $res = $this->get_one($sql);
        }
        return $res;
    }

    public function addCabinetPickGoodsSts($data)
    {

        $res = $this->insert('cabinet_pick_goods_sts', $data);
        return $res;
    }

    public function setCabinetPickGoodsSts($id, $dataArray)
    {
        $res = $this->update('cabinet_pick_goods_sts', $dataArray, ' id="' . $id . '" ');
        return $res;
    }

    public function delCabinetPickGoodsSts($id)
    {
        $res = $this->delete('cabinet_pick_goods_sts', ' id="' . $id . '" ');
        return $res;
    }
}
