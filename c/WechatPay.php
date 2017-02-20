<?php

final class WechatPayController extends Base
{

    /**
     * 微信创建虚拟订单-返回sign标识，用于客户端支付
     * @param $orderid 订单号
     * @param $m 金额
     * @return array
     */
    public function pay($orderid, $m, $openid)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

        $kD ['appid'] = APPID;
        $kD ['attach'] = 'JSAPI';
        $kD ['body'] = APP_TITLE . '在线支付';
        $kD ['mch_id'] = APP_PAY_MCHID;
        $kD ['nonce_str'] = strtoupper($this->getRandomString(32, '3'));
        $kD ['notify_url'] = APP_WEBSITE . '/e/PayCallback.php';
        $kD ['openid'] = $openid;
        $kD ['out_trade_no'] = $orderid;
        $kD ['spbill_create_ip'] = $this->_getIp();
        $kD ['total_fee'] = floatval($m) * 100;
        $kD ['trade_type'] = 'JSAPI';
        ksort($kD);
        $strSignTmp = '';
        foreach ($kD as $kk => $vv) {
            $strSignTmp .= $kk . '=' . $vv . '&';
        }
        $strSignTmp .= 'key=' . APP_PAY_STR;
        $sign = strtoupper(md5($strSignTmp));

        $strXml = "<xml>
                       <appid>{$kD['appid']}</appid>
                       <attach>{$kD ['attach']}</attach>
                       <body>{$kD ['body']}</body>
                       <mch_id>{$kD['mch_id']}</mch_id>
                       <nonce_str>{$kD ['nonce_str']}</nonce_str>
                       <notify_url>{$kD ['notify_url']}</notify_url>
                       <openid>{$kD ['openid']}</openid>
                       <out_trade_no>{$orderid}</out_trade_no>
                       <spbill_create_ip>{$kD ['spbill_create_ip']}</spbill_create_ip>
                       <total_fee>{$kD ['total_fee']}</total_fee>
                       <trade_type>{$kD ['trade_type']}</trade_type>
                       <sign>{$sign}</sign>
                    </xml>";
        $res = Run::getHttpPostRes($strXml, $url);
        $res = json_decode(json_encode(simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $startResult = $this->_startJsApiSign($res);
        if ($startResult != false) {
            $res ['newSign'] = $startResult ['sign'];
            $res ['timestamp'] = $startResult ['timestamp'];
            $res ['newRndstr'] = $startResult ['rndstr'];
        } else {
            $this->_jsonEn('0', '发起支付验证失败');
        }
        return $res;
    }

    //客户端JSAPI发起支付的验证签名
    private function _startJsApiSign($res)
    {
        if ($res ['return_code'] == 'SUCCESS') {
            $rndStr = strtoupper($this->getRandomString(32, '3'));
            $arr = array(
                'appId' => $res ['appid'],
                'timeStamp' => time(),
                'nonceStr' => $rndStr,
                'package' => 'prepay_id=' . $res ['prepay_id'],
                'signType' => 'MD5'
            );
            ksort($arr);
            $str = '';
            foreach ($arr as $k => $v) {
                $str .= $k . '=' . $v . '&';
            }
            $str .= 'key=' . APP_PAY_STR;
            $res ['sign'] = strtoupper(md5($str));
            $res ['timestamp'] = strval($arr ['timeStamp']);
            $res ['rndstr'] = $rndStr;
        } else {
            $res = false;
        }
        return $res;
    }

    /**
     *
     * 支付成功回调
     */
    public function payCallBack($params, $_t = '')
    {
        //更新订单状态 交易流水号
        //更新商品格子状态为已租未取货 5
        //更新订单详情，发放密码
        //
        $res = json_decode(json_encode(simplexml_load_string($params, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $saveRes = null;
        if ($res ['return_code'] == 'SUCCESS') {
            $obj = new UserOrderModel();
            $orderRes = $obj->getUserOrder('oid="' . $res ['out_trade_no'] . '"', '', '', '1');
            if (empty ($orderRes)) {

            } else {
                $orderRes ['pay_time'] = date('Y-m-d H:i:s');
                $orderRes ['wx_code'] = $res ['transaction_id'];
                if ($orderRes ['is_pay'] == '0') {
                    $orderRes ['is_pay'] = '1';
                    //更新支付订单
                    $saveRes = $obj->setUserOrder($orderRes ['id'], $orderRes);
                    //查询订单详细
                    $obj = new UserOrderDetailModel();
                    $odRs = $obj->getUserOrderDetail('oid="' . $orderRes['oid'] . '"', '', '', '1');
                    //查询商品格子状态
                    $ggrObj = new GoodsGridRelationModel();
                    $ggrObj->setField('id,pwd');
                    $w = 'c_num="' . $odRs['cnum'] . '" and st_num="' . $odRs['stnum'] .
                        '" and g_num="' . $odRs['gnum'] . '" and grid_num="' . $odRs['gridnum'] . '"';
                    $ggrRs = $ggrObj->getGoodsGridRelation($w, '', '', '1');

                    //更新密码给用户
                    $obj->setUserOrderDetail($odRs['id'], ['pwd' => $ggrRs['pwd']]);

                    //更新格子商品状态
                    $ggrObj->setGoodsGridRelation($ggrRs['id'], ['updated_at' => date('Y-m-d H:i:s'), 'sts' => '5']);


                    echo '<xml>
						  <return_code><![CDATA[SUCCESS]]></return_code>
						  <return_msg><![CDATA[OK]]></return_msg>
						</xml>';
                }
            }
        } else {

        }
    }

    /**
     * 微信退款操作
     */
    public function refundOperation($order_id, $refund_order_id, $total_money, $refund_money, $open_id)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

        $data['appid'] = APPID;
        $data['mch_id'] = APP_PAY_MCHID;
        $data['nonce_str'] = $this->getRandomString(32, '3');
        $data['out_trade_no'] = $order_id;
        $data['out_refund_no'] = $refund_order_id;
        $data['total_fee'] = floatval($total_money) * 100;
        $data['refund_fee'] = floatval($refund_money) * 100;
        $data['op_user_id'] = APP_PAY_MCHID;

        ksort($data);
        $strSignTmp = '';
        foreach ($data as $kk => $vv) {
            $strSignTmp .= $kk . '=' . $vv . '&';
        }
        $strSignTmp .= 'key=' . APP_PAY_STR;
        $sign = strtoupper(md5($strSignTmp));

        $str = "<xml>
                   <appid>{$data['appid']}</appid>
                   <mch_id>{$data['mch_id']}</mch_id>
                   <nonce_str>{$data['nonce_str']}</nonce_str>
                   <op_user_id>{$data['op_user_id']}</op_user_id>
                   <out_refund_no>{$data['out_refund_no']}</out_refund_no>
                   <out_trade_no>{$data['out_trade_no']}</out_trade_no>
                   <refund_fee>{$data['refund_fee']}</refund_fee>
                   <total_fee>{$data['total_fee']}</total_fee>                   
                   <sign>{$sign}</sign>
                </xml>";

        //var_dump($str);

        $rs = $this->curlPostSSL($url, $str);
        //$rs = Run::getHttpPostRes($str, $url);
        //var_dump($rs);
        if ($rs) {
            $res = json_decode(json_encode(simplexml_load_string($rs, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            if ($res ['return_code'] == 'SUCCESS') {
                if ($res['result_code'] == 'SUCCESS') {
                    //退款单状态更新
                    $obj = new UserOrderRefundModel();
                    $rs = $obj->getUserOrderRefund('refund_oid="' . $res['out_refund_no'] . '"', '', '', '1');
                    $obj->setUserOrderRefund($rs['id'], [
                        'wx_refund_code' => $res['refund_id'],
                        'real_refund_money' => floatval($res['refund_fee']) / 100,
                        'sts' => '1',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    return true;
                } else {
                    $this->wlog(APP_PATH . 'public/log/refund-order', $data['out_refund_no'] . '-退款失败-' . $res['err_code']);
                    return false;
                }
            } else {
                $this->wlog(APP_PATH . 'public/log/refund-order', $data['out_refund_no'] . '-退款失败-return-error');
                return false;
            }
        } else {
            $this->wlog(APP_PATH . 'public/log/refund-order', $data['out_refund_no'] . '-退款失败');
            return false;
        }
    }

    /**
     * @name ssl Curl Post数据
     * @param string $url 接收数据的api
     * @param string $vars 提交的数据
     * @param int $second 要求程序必须在$second秒内完成,负责到$second秒后放到后台执行
     * @return string or boolean 成功且对方有返回值则返回
     */
    public function curlPostSSL($url, $vars, $second = 30, $aHeader = array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        //curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/cert.pem');
        //默认格式为PEM，可以注释
        //curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/private.pem');

        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch, CURLOPT_SSLCERT, APP_PATH . 'public/cert/all.pem');
        curl_setopt($ch, CURLOPT_SSLCERT, '/var/www/cert/all.pem');

        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            $this->wlog(APP_PATH . 'public/log/refund-order', "call faild, errorCode:$error");
            curl_close($ch);
            return false;
        }
    }


}