<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/26
 * Time: 13:45
 */
final class WechatMessage
{

    public static function run($str)
    {
        $token = WechatToken::readTokenFile();
        $url = SERVICE_MSG_URL . $token;
        $rs = Run::getHttpPostRes($str, $url, array('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($str)));
        return $rs;
    }

    public static function runTemplate($str)
    {
        $token = WechatToken::readTokenFile();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $token;
        $rs = Run::getHttpPostRes($str, $url, array('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($str)));
        return $rs;
    }

    public static function SendOrderPayTemplateMessage($open_id,$title,$r_s,$r_e,$cnum,$ads,$pwd)
    {
        $str = '{
                   "touser":"'.$open_id.'",
                   "template_id":"0qhtwQqXRENEzdZlgtKabrkP06flUVmYVBk0TcUzVUA",
                   "url":"",            
                   "data":{
                           "first": {
                               "value":"亲，您的订单支付成功。",
                               "color":"#173177"
                           },
                           "keyword1":{
                               "value":"'.$title.'",
                               "color":"#173177"
                           },
                           "keyword2": {
                               "value":"'.$r_s.' 至 '.$r_e.'",
                               "color":"#173177"
                           },
                           "keyword3": {
                               "value":"'.$cnum.'",
                               "color":"#173177"
                           },
                           "keyword4": {
                               "value":"'.$ads.'",
                               "color":"#173177"
                           },
                           "keyword5": {
                               "value":"'.$pwd.'",
                               "color":"#173177"
                           },
                           "remark":{
                               "value":"祝您在海边玩的开心！",
                               "color":"#173177"
                           }
                   }
               }';
        return self::runTemplate($str);
    }

    public static function SendText($openId, $msg)
    {
        $str = '{
                    "touser":"' . $openId . '",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"' . $msg . '"
                    }
                }';
        return self::run($str);
    }

    public static function SendImage($openId, $imgId)
    {
        $str = '{
                    "touser":"' . $openId . '",
                    "msgtype":"image",
                    "image":
                    {
                      "media_id":"' . $imgId . '"
                    }
                }';
        return self::run($str);
    }

    public static function SendMpNews($openId, $mediaId)
    {
        $str = '{
                    "touser":"' . $openId . '",
                    "msgtype":"mpnews",
                    "mpnews":
                    {
                         "media_id":"' . $mediaId . '"
                    }
                }';
        return self::run($str);
    }

    /**
     * @param $openId
     * @param $data
     *        [
     *            //标题 描述 跳转地址 图片
     *            ['title'=>'','description'=>'','url'=>'','picurl'=>'']
     *        ]
     */
    public static function SendNews($openId, $data)
    {
        $_tmpStr = '';
        foreach ($data as $dk => $dv) {
            $_tmpStr .= '{';
            foreach ($dv as $k => $v) {
                $_tmpStr .= '"' . $k . '":"' . $v . '",';
            }
            $_tmpStr .= '},';
        }
        $str = '{
                    "touser":"' . $openId . '",
                    "msgtype":"news",
                    "news":{
                        "articles": [' . $_tmpStr . ']
                    }
                }';
        return self::run($str);
    }
}