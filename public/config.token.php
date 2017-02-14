<?php
class WechatToken {
	
	/**
	 * 如果Token超时，重新设定token
	 */
	public static function setToken() {
		$ac_token = Run::getHttpRes ( BASE_ACCESS_TOKEN_URL );
		$ac_token = json_decode ( $ac_token, true );
		$token = $ac_token ['access_token'];
		self::saveTokenFile ( $token );
		return $token;
	}
	
	/**
	 * 保存token
	 */
	public static function saveTokenFile($token) {
		file_put_contents ( APP_PATH . 'public/token/token.inc', $token );
	}
	
	/**
	 * 读取token
	 */
	public static function readTokenFile() {
		$a = file_get_contents ( APP_PATH . 'public/token/token.inc' );
		return $a;
	}
	
	/**
	 * 
	 * 发送模板消息 
	 * @param string $token
	 * @param string josn $str
	 */
	public static function _sendWechatTemplate($str) {
		$token = self::readTokenFile ();
		$service_send_url = 'http://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';
		$res = Run::getHttpPostRes ( $str, $service_send_url . $token, array ('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen ( $str ) ) );
		self::_checkToken ( $res, '_sendWechatTemplate', $str );
	}
	
	/**
	 * 
	 * 发送普通消息 
	 * @param string $token
	 * @param string josn $str
	 */
	public static function _sendWechatCustom($str) {
		$token = self::readTokenFile ();
		$service_send_url = 'http://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';
		$res = Run::getHttpPostRes ( $str, $service_send_url . $token, array ('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen ( $str ) ) );
		$res = self::_checkToken ( $res, '_sendWechatCustom', $str );
		return $res;
	}
	
	/**
	 * 
	 * 判断token是否过期
	 * @param 回调结果 $res
	 * @param 要执行的方法 $func
	 * @param 要传送的数据 $str
	 */
	public static function _checkToken($res, $func, $str) {
		$res = json_decode ( $res, true );
		//如果超时，则重新获取token
		if (isset ( $res ['errcode'] ) && ($res ['errcode'] == '42001' || $res ['errcode'] == '41001' || $res ['errcode'] == '40014')) {			
			$token = self::setToken ();
			$res = self::$func ( $str );
		} else {
			return $res;
		}
	}
	
	/**
	 * JS-SDK token
	 */
	public static function setJsToken($token) {
		//		$token = self::readTokenFile ();
		$_jsapi_ticket_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $token . '&type=jsapi';
		$_jsapi_ticket = Run::getHttpRes ( $_jsapi_ticket_url );
		$_jsapi_ticket = json_decode ( $_jsapi_ticket, true );
		self::saveJsTokenFile ( time () . ' ' . $_jsapi_ticket ['ticket'] );
		return $_jsapi_ticket ['ticket'];
	}
	
	/**
	 * 保存js-token
	 */
	public static function saveJsTokenFile($token) {
		file_put_contents ( APP_PATH . 'public/token/token.js.inc', $token );
	}
	
	/**
	 * 读取js-token
	 */
	public static function readJsTokenFile() {
		$a = file_get_contents ( APP_PATH . 'public/token/token.js.inc' );
		return $a;
	}
}