<?php
final class UserOrderDetailModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getUserOrderDetail($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `user_orders_detail` ';
		if (! empty ( $where )) {
			$sql .= ' where ' . $where;
		}
		if (! empty ( $orderby )) {
			$sql .= ' order by ' . $orderby;
		} else {
			$sql .= ' order by id desc ';
		}
		if (! empty ( $limit )) {
			$sql .= ' limit ' . $limit;
		}
		if (empty ( $solo )) {
			$res = $this->get_all ( $sql );
		} else {
			$res = $this->get_one ( $sql );
		}
		return $res;
	}
	
	public function addUserOrderDetail($data) {

		$res = $this->insert ( 'user_orders_detail', $data );
		return $res;
	}
	
	public function setUserOrderDetail($id, $dataArray) {
		$res = $this->update ( 'user_orders_detail', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delUserOrderDetail($id) {
		$res = $this->delete ( 'user_orders_detail', ' id="' . $id . '" ' );
		return $res;
	}
}
