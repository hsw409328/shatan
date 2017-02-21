<?php
final class CabinetLngLatModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getCabinetLngLat($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `cabinet_lng_lat` ';
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
	
	public function addCabinetLngLat($data) {

		$res = $this->insert ( 'cabinet_lng_lat', $data );
		return $res;
	}
	
	public function setCabinetLngLat($id, $dataArray) {
		$res = $this->update ( 'cabinet_lng_lat', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delCabinetLngLat($id) {
		$res = $this->delete ( 'cabinet_lng_lat', ' id="' . $id . '" ' );
		return $res;
	}
}
