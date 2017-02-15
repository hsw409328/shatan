<?php
final class CabinetStsModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getCabinetSts($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `cabinet_sts` ';
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
	
	public function addCabinetSts($data) {

		$res = $this->insert ( 'cabinet_sts', $data );
		return $res;
	}
	
	public function setCabinetSts($id, $dataArray) {
		$res = $this->update ( 'cabinet_sts', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delCabinetSts($id) {
		$res = $this->delete ( 'cabinet_sts', ' id="' . $id . '" ' );
		return $res;
	}
}
