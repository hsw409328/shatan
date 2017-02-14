<?php
final class CBagModel extends Mysql {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getCBag($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `cabinet_bag` ';
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
	
	public function addCBag($data) {

		$res = $this->insert ( 'cabinet_bag', $data );
		return $res;
	}
	
	public function setCBag($id, $dataArray) {
		$res = $this->update ( 'cabinet_bag', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delCBag($id) {
		$res = $this->delete ( 'cabinet_bag', ' id="' . $id . '" ' );
		return $res;
	}
}
