<?php
final class UsersInfoSysModel extends Mysql {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getUsersInfoSys($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `users_info_sys` ';
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
	
	public function addUsersInfoSys($data) {

		$res = $this->insert ( 'users_info_sys', $data );
		return $res;
	}
	
	public function setUsersInfoSys($id, $dataArray) {
		$res = $this->update ( 'users_info_sys', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delUsersInfoSys($id) {
		$res = $this->delete ( 'users_info_sys', ' id="' . $id . '" ' );
		return $res;
	}
}
