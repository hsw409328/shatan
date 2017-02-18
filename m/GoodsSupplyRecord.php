<?php
final class GoodsSupplyRecordModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getGoodsSupplyRecord($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `goods_supply_record` ';
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
	
	public function addGoodsSupplyRecord($data) {

		$res = $this->insert ( 'goods_supply_record', $data );
		return $res;
	}
	
	public function setGoodsSupplyRecord($id, $dataArray) {
		$res = $this->update ( 'goods_supply_record', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delGoodsSupplyRecord($id) {
		$res = $this->delete ( 'goods_supply_record', ' id="' . $id . '" ' );
		return $res;
	}
}
