<?php

final class UserOrderRefundModel extends MysqliClass
{

    public $_f = '*';

    public function setField($f = '*')
    {
        $this->_f = $f;
    }

    public function getUserOrderRefund($where = '', $orderby = '', $limit = '', $solo = '')
    {
        $sql = 'select ' . $this->_f . ' from `user_orders_refund` ';
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

    public function addUserOrderRefund($data)
    {

        $res = $this->insert('user_orders_refund', $data);
        return $res;
    }

    public function setUserOrderRefund($id, $dataArray)
    {
        $res = $this->update('user_orders_refund', $dataArray, ' id="' . $id . '" ');
        return $res;
    }

    public function delUserOrderRefund($id)
    {
        $res = $this->delete('user_orders_refund', ' id="' . $id . '" ');
        return $res;
    }
}
