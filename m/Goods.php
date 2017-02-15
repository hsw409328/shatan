<?php
final class GoodsModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getGoods($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `goods` ';
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
	
	public function addGoods($data) {

		$res = $this->insert ( 'goods', $data );
		return $res;
	}
	
	public function setGoods($id, $dataArray) {
		$res = $this->update ( 'goods', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delGoods($id) {
		$res = $this->delete ( 'goods', ' id="' . $id . '" ' );
		return $res;
	}
}
