<?php
final class UsersEuiController extends Base {
	
	public function getUsersList() {
		$_p = Run::req ( 'page' );
		$_p = (empty ( $_p ) || $_p < 0) ? 0 : $_p - 1;
		$_page = Run::req ( 'rows' );
		$_page_prev = intval ( $_p ) * intval ( $_page );
		$limit = $_page_prev . ',' . $_page;
		$ueObj = new UsersModel ();
		$ueObj->setField('*,(select sName from website_type where id=user_type) as user_type_name');
		$w = $this->getWhere ();
		$res = $ueObj->getUsers ( $w, '', $limit );
		$ueObj->setField ( 'count(id) as total' );
		$totalRes = $ueObj->getUsers ( $w, '', '', '1' );
		$_tmpRes = array ('total' => $totalRes ['total'], 'rows' => $res );
		echo json_encode ( $_tmpRes );
	}
	
	private function getWhere() {
		$w = ' 1 ';
		$_name = Run::req ( '_name' );
		$_id = Run::req ( '_id' );
		if (! $_name && ! $_id) {
			$w = '';
		} else {
			$w = '1';
		}
		if ($_name) {
			$w .= ' and mobile_num="' . $_name . '" ';
		}
		if ($_id) {
			$w .= ' and id="' . $_id . '" ';
		}
		return $w;
	}
	
	public function getUserInfo() {
		$_html = '';
		$obj = new UserInfoModel ();
		$_id = Run::req ( '_uid' );
		$w = 'uId="' . $_id . '"';
		$res = $obj->getUserInfo($w,'uId','1','1');
		$_html.='<h3>姓名：'.$res['uXm'].'</h3><h3>电话：'.$res['uTel'].'</h3><h3>邮箱：'.$res['uEmail'].'</h3><h3>头像：<img src="'.$res['uImgPath'].'" width="100px;"></h3><h3>简介：'.$res['uDesc'].'</h3><h3>更新时间：'.$res['createTime'].'</h3>';
		echo $_html;
	}

}