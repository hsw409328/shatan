<?php
final class WebTypeModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getWebType($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `website_type` ';
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
	
	public function addWebType($data) {
		
		$dataArray ['parentId'] = $data ['parentId'];
		$dataArray ['sName'] = $data ['sName'];
		$dataArray ['sUrl'] = $data ['sUrl'];
		$dataArray ['wStatus'] = $data ['wStatus'];
		
		$res = $this->insert ( 'website_type', $dataArray );
		return $res;
	}
	
	public function setWebType($id, $dataArray) {
		$res = $this->update ( 'website_type', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delWebType($id) {
		$res = $this->delete ( 'website_type', ' id="' . $id . '" ' );
		return $res;
	}
}