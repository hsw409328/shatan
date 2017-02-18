<?php
final class UserFeedbackModel extends MysqliClass {
	
	public $_f = '*';
	
	public function setField($f = '*') {
		$this->_f = $f;
	}
	
	public function getUserFeedback($where = '', $orderby = '', $limit = '', $solo = '') {
		$sql = 'select ' . $this->_f . ' from `user_feedback` ';
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
	
	public function addUserFeedback($data) {

		$res = $this->insert ( 'user_feedback', $data );
		return $res;
	}
	
	public function setUserFeedback($id, $dataArray) {
		$res = $this->update ( 'user_feedback', $dataArray, ' id="' . $id . '" ' );
		return $res;
	}
	
	public function delUserFeedback($id) {
		$res = $this->delete ( 'user_feedback', ' id="' . $id . '" ' );
		return $res;
	}
}
