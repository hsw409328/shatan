<?php
class WechatClass {
	
	public function valid() {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature ()) {
			echo $echoStr;
			exit ();
		}
	}
	
	public function responseMsg() {
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		if (! empty ( $postStr )) {
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			$MsgType = $postObj->MsgType; //消息类型
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$keyword = trim ( $postObj->Content );
			$time = time ();
			if ($MsgType == 'event') {
				$MsgEvent = $postObj->Event; //获取事件类型  
				if ($MsgEvent == 'subscribe') {
					
					//订阅事件
					$msgType = 'news';
					 $textTpl = "<xml>
											<ToUserName><![CDATA[{$fromUsername}]]></ToUserName>
											<FromUserName><![CDATA[{$toUsername}]]></FromUserName>
											<CreateTime>{$time}</CreateTime>
											<MsgType><![CDATA[{$msgType}]]></MsgType>
											<ArticleCount>1</ArticleCount>
											<Articles>
												<item>
													<Title><![CDATA[乐趣宠：宠物行业服务平台]]></Title> 
													<Description><![CDATA[乐趣宠，为宠物店提供一站式货品采购平台，为店员提供免费报销的医疗保险，以及宠物店装修金融分期服务。]]></Description>
													<PicUrl><![CDATA[https://mmbiz.qlogo.cn/mmbiz/Jv1Jeb9nF8BicVyiczDkMhceGiaBFZzeHFAIIwMzepCvN3JHD6lzgQbdnrspxLzxRsv3HI3Fu23jrxcMyqeEdJsNg/0]]></PicUrl>
													<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzI5NjQwMTE1Ng==&tempkey=UimqXKT%2B9ImFyaWEY9wNdi75cq0fjFj%2FrX3f8knccBFX%2F%2FfT8E%2FywCsbHG%2FManu41UzbKCQW%2Fum5hJBZi9apib5ITlzsK6NnydHAXpAfBRcTkECMSu9E659tvggds0yHGHwBB3HCP4FdwXe8QOHhAA%3D%3D&#rd]]></Url>
												</item>	
											</Articles>
											</xml> ";
											echo $textTpl;
											
					
					/*$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";
					$msgType = "text";
					$contentStr = '欢迎您加入乐趣宠，我们是宠物行业从业者服务平台~';
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
					echo $resultStr;					*/
					exit ();
				} elseif ($MsgEvent == 'CLICK') {
					//点击事件  
					$EventKey = $postObj->EventKey; //菜单的自定义的key值，可以根据此值判断用户点击了什么内容，从而推送不同信息  
					$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";
					switch ($EventKey) {
						case 'TEL_APPLY' :
							$contentStr = '如果您是宠物店、宠物医院，或者品牌厂商，均可把合作需求直接留言，工作人员会主动与您联系。';
							$msgType = "text";
							$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
							break;
						case 'ONLINE_DOCTOR' :
							$current_h = date ( 'H', $time );
							if ($current_h >= 10 && $current_h <= 22) {
								$contentStr = '亲，我是宠物医生，为您解答各类问题，说吧，啥事？';
							} else {
								$contentStr = '亲，宠物医生已经休息了，请在10:00-22:00咨询。';
							}
							$msgType = "text";
							$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
							break;
						case 'ABOUT_WE' :
							$msgType = 'news';
					 		$resultStr = "<xml>
											<ToUserName><![CDATA[{$fromUsername}]]></ToUserName>
											<FromUserName><![CDATA[{$toUsername}]]></FromUserName>
											<CreateTime>{$time}</CreateTime>
											<MsgType><![CDATA[{$msgType}]]></MsgType>
											<ArticleCount>1</ArticleCount>
											<Articles>
												<item>
													<Title><![CDATA[乐趣宠：宠物行业服务平台]]></Title> 
													<Description><![CDATA[乐趣宠，为宠物店提供一站式货品采购平台，为店员提供免费报销的医疗保险，以及宠物店装修金融分期服务。]]></Description>
													<PicUrl><![CDATA[https://mmbiz.qlogo.cn/mmbiz/Jv1Jeb9nF8BicVyiczDkMhceGiaBFZzeHFAIIwMzepCvN3JHD6lzgQbdnrspxLzxRsv3HI3Fu23jrxcMyqeEdJsNg/0]]></PicUrl>
													<Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MzI5NjQwMTE1Ng==&tempkey=UimqXKT%2B9ImFyaWEY9wNdi75cq0fjFj%2FrX3f8knccBFX%2F%2FfT8E%2FywCsbHG%2FManu41UzbKCQW%2Fum5hJBZi9apib5ITlzsK6NnydHAXpAfBRcTkECMSu9E659tvggds0yHGHwBB3HCP4FdwXe8QOHhAA%3D%3D&#rd]]></Url>
												</item>	
											</Articles>
											</xml> ";
							break;
					}
					echo $resultStr;
					exit ();
				}
			
			} elseif ($MsgType == 'text') {
				/*$ac_token = $this->getAccessToken();
				$kf_list = $this->getOnlineKF($ac_token);*/
				$_content = $postObj->Content;
				if(strpos($_content, "京宠展")!==false){
					$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";
					$msgType = "text";
					$contentStr = '京宠展·萌工社·保险及商品代金券登记<a href="http://mgs.momoday.net/s/20160315/?_city=100001#wechat_redirect">点击这里</a>';
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				}else if(strpos($_content, "宠博会")!==false){
					$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";
					$msgType = "text";
					$contentStr = '宠博会·萌工社·保险及商品代金券登记<a href="http://mgs.momoday.net/s/20160315/?_city=200001#wechat_redirect">点击这里</a>';
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				}else if(strpos($_content, "亚宠会")!==false){
					$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";
					$msgType = "text";
					$contentStr = '第三届亚洲宠物博览会·萌工社·保险及商品代金券登记<a href="http://mgs.momoday.net/s/20160315/?_city=300001#wechat_redirect">点击这里</a>';
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				}
				/*if(strpos($_content, "阿姨帮")!==false){
					$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";
					$msgType = "text";
					$contentStr = '亲，真是不好意思啊，您没有获得免费洁齿SPA名额。表难过，299元标准价下单体验，请<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4d7da85f048550a5&redirect_uri=http%3A%2F%2Fwx.momoday.net%2Fe%2Fwechat%2Fwechat.callback.php%3Ftpl%3Dv1.index&response_type=code&scope=snsapi_base&state=mmd#wechat_redirect">点击此处</a>';
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				}else{
					$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<TransInfo>
								   <KfAccount>![CDATA[%s]]</KfAccount>
								</TransInfo>
							</xml>";
					$contentStr = 'meijun@pet_momoda';
					$msgType = 'transfer_customer_service';
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );					
				}*/
				echo $resultStr;
				exit ();
			}
		} else {
			exit ();
		}
	}
	
	private function getAccessToken() {
		$appid = 'wxbd92752eaebee0b5';
		$appsecret = '78188a2ad760a5095f1d07d77ab4df9b';
		$base_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
		$service_send_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';
		$ac_token = $this->getHttpRes ( $base_access_token_url );
		$ac_token = json_decode ( $ac_token, true );
		return isset ( $ac_token ['access_token'] ) ? $ac_token ['access_token'] : null;
	}
	
	private function getOnlineKF($ac_token) {
		$url = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=' . $ac_token;
		$res = $this->getHttpRes ( $url );
		return $res;
	}
	
	private function getHttpRes($url, $params = NULL) {
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
	
	private function getHttpPostRes($data, $url, $header) {
		$ch = curl_init ();
		
		curl_setopt ( $ch, CURLOPT_URL, $url ); //定义表单提交地址
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //定义提交类型 1：POST ；0：GET
		curl_setopt ( $ch, CURLOPT_HEADER, 1 ); //定义是否显示状态头 1：显示 ； 0：不显示
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header ); //定义请求类型
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); //定义是否直接输出返回流
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); //定义提交的数据
		$response = curl_exec ( $ch ); //接收返回信息
		if (curl_errno ( $ch )) { //出错则显示错误信息		
		//print curl_error($ch);
		}
		curl_close ( $ch ); //关闭curl链接
		return $response; //显示返回信息
	}
	
	private function checkSignature() {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$token = TOKEN;
		$tmpArr = array ($token, $timestamp, $nonce );
		sort ( $tmpArr );
		$tmpStr = implode ( $tmpArr );
		$tmpStr = sha1 ( $tmpStr );
		
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}

//你设置的TOKEN
define ( "TOKEN", "MGSMOMODA" );
$wechatObj = new WechatClass ();
//第一次验证一定要开启，之后就可以注释了
//$wechatObj->valid ();
//接收用户发过来的消息 
$wechatObj->responseMsg ();
?>