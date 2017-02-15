<?php
final class CgRelationModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getCgRelation($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `c_g_relation` ';
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
	
	public function addCgRelation($data) {

		$res = $this->insert ( 'c_g_relation', $data );
		return $res;
	}
	
	public function setCgRelation($id, $dataArray) {
		$res = $this->update ( 'c_g_relation', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delCgRelation($id) {
		$res = $this->delete ( 'c_g_relation', ' id="' . $id . '" ' );
		return $res;
	}
}
