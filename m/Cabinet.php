<?php
final class CabinetModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getCabinet($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `cabinet` ';
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
	
	public function addCabinet($data) {

		$res = $this->insert ( 'cabinet', $data );
		return $res;
	}
	
	public function setCabinet($id, $dataArray) {
		$res = $this->update ( 'cabinet', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delCabinet($id) {
		$res = $this->delete ( 'cabinet', ' id="' . $id . '" ' );
		return $res;
	}
}
