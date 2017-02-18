<?php

final class UserOrderModel extends MysqliClass
{

    public $_f = '*';

    public function setField($f = '*')
    {
        $this->_f = $f;
    }

    public function getUserOrder($where = '', $orderby = '', $limit = '', $solo = '')
    {
        $sql = 'select ' . $this->_f . ' from `user_orders` ';
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

    public function addUserOrder($data)
    {

        $res = $this->insert('user_orders', $data);
        return $res;
    }

    public function setUserOrder($id, $dataArray)
    {
        $res = $this->update('user_orders', $dataArray, ' id="' . $id . '" ');
        return $res;
    }

    public function delUserOrder($id)
    {
        $res = $this->delete('user_orders', ' id="' . $id . '" ');
        return $res;
    }
}
