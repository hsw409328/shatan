<?php
header ( "Content-type:text/html;charset=utf-8" );
define ( 'APPID', '' );
define ( 'APPSECRET', '' );
define ( 'TOKEN_URL', 'https://api.weixin.qq.com/sns/oauth2/access_token' );
define ( 'AUTH_URL', 'https://open.weixin.qq.com/connect/oauth2/authorize' );
define ( 'REFRESH_TOKEN', 'https://api.weixin.qq.com/sns/oauth2/refresh_token' );
define ( 'WECHAT_REDIRECT', 'https://mp.weixin.qq.com/mp/redirect?url=' );
define ( 'BASE_ACCESS_TOKEN_URL', 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APPID . '&secret=' . APPSECRET . '' );
define ( 'SERVICE_MSG_URL', 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' );
define ( 'APP_PATH', dirname ( __DIR__ ) . DIRECTORY_SEPARATOR );
define ( 'IMG_WEBSITE', 'http://120.25.86.251:4869/' );
define ( 'APP_WEBSITE', 'http://' . $_SERVER ['HTTP_HOST'] . '/' );
define ( 'APP_NAME', 'lvyouapp' );
ini_set ( 'date.timezone', 'Asia/Shanghai' );

session_start ();

class Run {
	
	public static $SECRET_KEY = 'lvyouapp';
	
	public static $PAGE_ADMIN = 15;
	
	private static function checkTpl($view) {
		$file = 'v/' . $view . '.php';
		if (! file_exists ( $file )) {
			return false;
		}
		return $file;
	}
	
	private static function includeTpl($file) {
		global $_urlParams;
		require_once $file;
	}
	
	public static function checkBrowser() {
		if (! strpos ( $_SERVER ['HTTP_USER_AGENT'], "MicroMessenger" )) {
			die ( '<html>
				    <head>
				    	<title>抱歉，出错了</title>
				        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
				        <style>
							body{background:#e1e0de;line-height: 1.6;
							font-family: "Helvetica Neue",Helvetica,"Microsoft YaHei",Arial,Tahoma,sans-serif;text-align: center}
							img{margin-top:40px;}
							p{font-weight: 400;color: #000000;}							
						</style>
				    </head>
					<body>
						<img src="http://img.momopet.cn:4869/4a06f368048c265f1f1c18b99d59d237?f=png" />
						<p>请在微信客户端打开链接</p>
					</body>
				</html>' );
			exit ();
		}
	}
	
	public static function loadStart($view) {		
		$check_tpl_res = self::checkTpl ( $view );
		if ($check_tpl_res) {
			self::includeTpl ( $check_tpl_res );
			return true;
		} else {
			self::show_msg ( '', '', APP_WEBSITE );
		}
	}
	
	/**
	 * 
	 * 获取参数
	 * @param string $str
	 * @return 得到的结果
	 */
	public static function req($str) {
		$str = isset ( $_REQUEST [$str] ) ? addslashes ( $_REQUEST [$str] ) : '';
		return trim($str);
	}
	
	/**
	 * 
	 * 显示网页操作信息
	 * @param string $url 跳转URL
	 * @param string $msg 提示语
	 * @param int $single 标识位，0后退 1跳转
	 */
	public static function show_msg($msg = null, $single = 0, $url = './') {
		if ($msg != null) {
			$alert_str = "alert('{$msg}');";
		} else {
			$alert_str = "";
		}
		if ($single != 0) {
			echo "<script>{$alert_str}window.location.href='{$url}';</script>";
			exit ();
		} else {
			echo "<script>{$alert_str}history.go(-1);</script>";
			exit ();
		}
	}
	
	/**
	 * 
	 * CURL GET方式请求
	 * @param string $url
	 * @param Array $params
	 */
	public static function getHttpRes($url, $params = NULL) {
		$final_url = $url;
		if ($params != null) {
			$param = '?';
			foreach ( $params as $key => $value ) {
				$param .= $key . '=' . $value . '&';
			}
			$final_url .= $param;
		}
		$ch = curl_init ( $final_url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		if (strpos ( $final_url, 'https' ) === 0) {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		}
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
	
	/**
	 * 
	 * CURL POST方式请求
	 * @param string $url
	 * @param Array $params
	 */
	public static function getHttpPostRes($data, $url, $header = array()) {
		$ch = curl_init ();
		
		curl_setopt ( $ch, CURLOPT_URL, $url ); //定义表单提交地址
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //定义提交类型 1：POST ；0：GET
		curl_setopt ( $ch, CURLOPT_HEADER, 0 ); //定义是否显示状态头 1：显示 ； 0：不显示
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header ); //定义请求类型
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); //定义是否直接输出返回流
		if (strpos ( $url, 'https' ) === 0) {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		}
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); //定义提交的数据
		$response = curl_exec ( $ch ); //接收返回信息
		if (curl_errno ( $ch )) { //出错则显示错误信息		
		//print curl_error($ch);
		}
		curl_close ( $ch ); //关闭curl链接
		return $response; //显示返回信息
	}
	
	public static function ``($oid) {
		$obj = new ParamsController ();
		$obj->localSetParams ( 'openid', $oid );
		$userObj = new UsersController ();
		$userObj->loginUsers ();
	
		//		var_dump ( $_SESSION );
	//		exit ();
	}
	
	public static function c($b = '') {
		$obj = new ParamsController ();
		$user = $obj->getSessionParams ( 'userDetail' );
		return $user;
	}
	
	/**
	 * $telNum 电话
	 * $info 内容  必须带：感谢您对我们的支持！回0000拒收【萌工社】
	 */
	public static function sendMessage($telNum, $info, $isRndStr = '1') {
		if (! empty ( $info )) {
			$msg = $info;
		} else {
			$msg = 'test001';
		}
		$mixchar = array ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J', 'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T', 'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z' );
		$t = time ();
		$r = self::mixtelnum ( $telNum, $t, $mixchar );
		
		if ($isRndStr) {
			$baseUrl = 'http://182.92.188.45/mmd/comcmc/sycmsms.php';
		} else {
			$baseUrl = 'http://182.92.188.45/mmd/comcmc/mgsnotice.php';
		}
		$param = '?u=bjsy&y=utmb&t=' . $r . '&d=' . $t . '&n=' . urlencode ( $info ) . '&enc=true';
		
		$ch = curl_init ( $baseUrl . $param );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true );
		$output = curl_exec ( $ch );
		return $output;
	
	}
	
	public static function mixtelnum($telnum, $dtime = 0, $mixchar) {
		if (strlen ( $telnum ) != 11) {
			die ( '请输入合适的电话号码……' );
		}
		$p = $dtime % 42;
		$mixtel = $mixchar [$p + 3];
		for($i = 1; $i < 11; $i ++) {
			$_tmp = substr ( $telnum, $i, 1 );
			$mixtel .= $mixchar [intval ( $_tmp ) + $p + $i];
		}
		return $mixtel;
	}
	
	public static function getRndStr($len = 6) {
		$str = '0123456789';
		$n = $len;
		$len = strlen ( $str ) - 1;
		$s = '';
		for($i = 0; $i < $n; $i ++) {
			$s .= $str [rand ( 0, $len )];
		}
		return $s;
	}
	
	public static function getFormatDate($d = null, $f = 'Y-m-d') {
		$returnDate = '';
		if ($d == null) {
			$returnDate = date ( $f, time () );
		} else {
			if (! is_int ( $d )) {
				$returnDate = date ( $f, strtotime ( $d ) );
			} else {
				$returnDate = date ( $f, $d );
			}
		}
		return $returnDate;
	}
	
	//$count为总条目数，$page为当前页码，$page_size为每页显示条目数
	public static function getPageLabel($count, $page, $page_size) {
		$page_count = ceil ( $count / $page_size ); //计算得出总页数
		

		$init = 1;
		$page_len = 7;
		$max_p = $page_count;
		$pages = $page_count;
		
		//判断当前页码
		$page = (empty ( $page ) || $page < 0) ? 1 : $page;
		//获取当前页url
		$url = $_SERVER ['REQUEST_URI'];
		//去掉url中原先的page参数以便加入新的page参数
		$parsedurl = parse_url ( $url );
		$url_query = isset ( $parsedurl ['query'] ) ? $parsedurl ['query'] : '';
		if ($url_query != '') {
			$url_query = preg_replace ( "/(^|&)page=$page/", '', $url_query );
			$url = str_replace ( $parsedurl ['query'], $url_query, $url );
			if ($url_query != '') {
				$url .= '&';
			}
		} else {
			$url .= '?';
		}
		
		//分页功能代码
		$page_len = ($page_len % 2) ? $page_len : $page_len + 1; //页码个数
		$pageoffset = ($page_len - 1) / 2; //页码个数左右偏移量
		

		$navs = '';
		if ($pages != 0) {
			if ($page != 1) {
				$navs .= "<a class='alt_btn' href=\"" . $url . "page=1\">首页</a> "; //第一页
				$navs .= "<a class='alt_btn' href=\"" . $url . "page=" . ($page - 1) . "\">上页</a>"; //上一页
			} else {
				$navs .= "<span class='disabled'>首页</span>";
				$navs .= "<span class='disabled'>上页</span>";
			}
			if ($pages > $page_len) {
				//如果当前页小于等于左偏移
				if ($page <= $pageoffset) {
					$init = 1;
					$max_p = $page_len;
				} else //如果当前页大于左偏移
{
					//如果当前页码右偏移超出最大分页数
					if ($page + $pageoffset >= $pages + 1) {
						$init = $pages - $page_len + 1;
					} else {
						//左右偏移都存在时的计算
						$init = $page - $pageoffset;
						$max_p = $page + $pageoffset;
					}
				}
			}
			for($i = $init; $i <= $max_p; $i ++) {
				if ($i == $page) {
					$navs .= "<span class='current'>" . $i . '</span>';
				} else {
					$navs .= " <a class='alt_btn' href=\"" . $url . "page=" . $i . "\">" . $i . "</a>";
				}
			}
			if ($page != $pages) {
				$navs .= " <a class='alt_btn' href=\"" . $url . "page=" . ($page + 1) . "\">下页</a> "; //下一页
				$navs .= "<a class='alt_btn' href=\"" . $url . "page=" . $pages . "\">末页</a>"; //最后一页
			} else {
				$navs .= "<span class='disabled'>下页</span>";
				$navs .= "<span class='disabled'>末页</span>";
			}
			echo "$navs";
		}
	}
	
	public static function getIdCardSex($idcard) {
		if (empty ( $idcard )) {
			return '0';
		}
		//1男 0女
		if (strlen ( $idcard ) == 15) {
			$res = substr ( $idcard, 14, 1 ) % 2 ? '1' : '0';
		} else {
			$res = substr ( $idcard, 14, 3 ) % 2 ? '1' : '0';
		}
		return $res;
	}
	
	public static function getIdCardBirthday($idcard) {
		if (empty ( $idcard )) {
			return '19900101';
		}
		$res = strlen ( $idcard ) == 15 ? ('19' . substr ( $idcard, 6, 6 )) : substr ( $idcard, 6, 8 );
		return $res;
	}
	
	//日历
	public static function displayCalendar($date = '') {
			if($date==''){
				$date = date('Y-m');
			}
			$_d = date('d');
			$_oneWeek = date ( 'w', strtotime ( $date . '-01' ) );
			$_mTotal = date ( 't', strtotime ( $date ) );
			$_arrDate = array ('日', '一', '二', '三', '四', '五', '六' );
			$_tmpInt = $_oneWeek;
			$_tmpIntTwo = 1;
			$_html = '<tr>
						<th>日</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						<th>五</th>
						<th>六</th>
					</tr>';
			for($i = 1; $i <= $_mTotal; $i ++) {
				if ($_tmpIntTwo == 1) {
					$_html .= '<tr>';
					$_html .= str_repeat ( '<td></td>', $_tmpInt );
					for($j = $_tmpInt; $j <= 6; $j ++) {
						if($i==$_d){
							$_strCls = 'style="color:#eeddcc"';
						}else{
							$_strCls = '';
						}
						$_html .= '<td '.$_strCls.'>' . $i . '</td>';
						$i ++;
					}
					$_html .= '</tr>';
					$i --;
					$_tmpIntTwo = 0;
				} else {
					$_html .= '<tr>';
					for($j = 0; $j <= 6; $j ++) {
						if ($i > $_mTotal) {
							$_html .= '<td></td>';
						} else {
							if($i==$_d){
								$_strCls = 'style="background-color:#cc9022"';
							}else{
								$_strCls = '';
							}
							$_html .= '<td '.$_strCls.'>' . $i . '</td>';
						}
						$i ++;
					}
					$_html .= '</tr>';
					$i --;
				}
			}
			echo $_html;
	}

}

function D($obj = '') {
	try {
		$str = $obj . 'Model';
		$newObj = new $str ();
	} catch ( Exception $e ) {
		print_r ( $e->getMessage () );
		exit ();
	}
	return $newObj;
}

function C($obj = '') {
	try {
		$str = $obj . 'Controller';
		$newObj = new $str ();
	} catch ( Exception $e ) {
		print_r ( $e->getMessage () );
		exit ();
	}
	return $newObj;
}
?>
