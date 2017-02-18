<?php
final class UserOrderDamageModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getUserOrderDamage($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `user_orders_damage` ';
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
	
	public function addUserOrderDamage($data) {

		$res = $this->insert ( 'user_orders_damage', $data );
		return $res;
	}
	
	public function setUserOrderDamage($id, $dataArray) {
		$res = $this->update ( 'user_orders_damage', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delUserOrderDamage($id) {
		$res = $this->delete ( 'user_orders_damage', ' id="' . $id . '" ' );
		return $res;
	}
}
