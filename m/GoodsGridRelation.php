<?php

final class GoodsGridRelationModel extends MysqliClass
{

    public $_f = '*';

    public function setField($f = '*')
    {
        $this->_f = $f;
    }

    public function getGoodsGridRelation($where = '', $orderby = '', $limit = '', $solo = '')
    {
        $sql = 'select ' . $this->_f . ' from `goods_grid_relation` ';
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

    public function getBuyGoodsListSql($c_num)
    {
        $sql = 'SELECT COUNT(id) AS buy_num,st_num,c_num FROM goods_grid_relation WHERE c_num=\''.$c_num.'\' AND sts=4 GROUP BY st_num';
        $res = $this->get_all($sql);
        return $res;
    }

    public function addGoodsGridRelation($data)
    {

        $res = $this->insert('goods_grid_relation', $data);
        return $res;
    }

    public function setGoodsGridRelation($id, $dataArray)
    {
        $res = $this->update('goods_grid_relation', $dataArray, ' id="' . $id . '" ');
        return $res;
    }

    public function delGoodsGridRelation($id)
    {
        $res = $this->delete('goods_grid_relation', ' id="' . $id . '" ');
        return $res;
    }
}
