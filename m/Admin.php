<?php
final class AdminModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getAdminUsers($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `admin_users` ';
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
	
	public function addAdminUsers($data) {
		
		$dataArray ['uName'] = $data ['uName'];
		$dataArray ['uPwd'] = $data ['uPwd'];
		$dataArray ['uType'] = $data ['uType'];
		$dataArray ['uStatus'] = $data ['uStatus'];
		$dataArray ['rbac'] = $data ['rbac'];
		
		$res = $this->insert ( 'admin_users', $dataArray );
		return $res;
	}
	
	public function setAdminUsers($id, $dataArray) {
		$res = $this->update ( 'admin_users', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delAdminUsers($id) {
		$res = $this->delete ( 'admin_users', ' id="' . $id . '" ' );
		return $res;
	}
}
