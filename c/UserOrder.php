<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/18
 * Time: 14:54
 */
final class UserOrderController extends Base
{

    /**
     * 首页获取可以归还的订单
     * @return array|null
     */
    public function countUserIsReturn()
    {
        $user = $this->getUserDetail();
        $w = 'uid="' . $user['id'] . '" and is_return = 0 and is_pay = 1';
        $obj = new UserOrderModel();
        $rs = $obj->getUserOrder($w, '', '', '1');
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }

    /**
     * 获取用户订单列表，支付完成的
     * @return array|null
     */
    public function getUserOrder()
    {
        $user = $this->getUserDetail();
        $w = 'uid="' . $user['id'] . '" and is_pay = 1';
        $obj = new UserOrderModel();
        $rs = $obj->getUserOrder($w);
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }

    /**
     * 获取子订单详细
     * @param $oid
     * @param $uid
     * @return array|null
     */
    public function getUserOrderDetail($oid, $uid)
    {
        $w = 'uid="' . $uid . '" and oid="' . $oid . '"';
        $obj = new UserOrderDetailModel();
        return $obj->getUserOrderDetail($w, '', '', '1');
    }

    /**
     * 获取单个订单
     * @param $oid
     * @param $uid
     * @return array|null
     */
    public function getUserOrderOne($oid, $uid)
    {
        $w = 'uid="' . $uid . '" and oid="' . $oid . '"';
        $obj = new UserOrderModel();
        return $obj->getUserOrder($w, '', '', '1');
    }

    /**
     * 归还操作
     */
    public function returnUserOrder()
    {
        $oid = Run::req('oid');
        $content = Run::req('content');
        $user = $this->getUserDetail();
        $oRs = $this->getUserOrderOne($oid, $user['id']);
        $overtime_money = $this->_sumOverTimeMoney($oRs['rent_date_end']);
        if ($overtime_money[0] != 0) {
            if ($overtime_money[0] < $oRs['deposit_money']) {
                $data['overtime_money'] = floatval($overtime_money[0]);
                $data['overtime_desc'] = $overtime_money[1];
            } else {
                $data['overtime_money'] = $oRs['deposit_money'];
                $data['overtime_desc'] = '超时扣款大于押金，为异常订单，退款0';
                $data['is_abnormal'] = 1;
            }
        } else {

        }
        $data['refund_money'] = $oRs['deposit_money'];
        $data['return_date'] = date('Y-m-d H:i:s');
        $data['is_return'] = 1;
        $data['order_bak'] = $content;
        $obj = new UserOrderModel();
        $rs = $obj->setUserOrder($oRs['id'], $data);
        if ($rs) {
            $this->_jsonEn('1', '归还成功');
        } else {
            $this->_jsonEn('0', '归还失败');
        }
    }

    /**
     * 计算超时扣款
     */
    private function _sumOverTimeMoney($end_return_date)
    {
        $end_time = strtotime($end_return_date);
        $current_time = time();
        if ($end_time >= $current_time) {
            //最后归还时间大于当前归还时间不扣款
            return [0, '未超时'];
        }
        //计算小时
        $h = intval(($current_time - $end_time) / (60 * 60));
        if ($h <= 6) {
            //6小时内不扣款
            return [0, '未超时'];
        }
        if ($h > 6 && $h <= 24) {
            //6-24小时内 扣50
            return [50, '超时' . $h . '小时'];
        }

        //超过一天的，每天加一百，向上取整
        $d = ceil($h / 24);
        $t = 0;
        for ($i = 1; $i <= $d; $i++) {
            $t += 100;
        }
        return [$t, '超时' . $d . '天，每天100'];
    }

    /**
     * 通过商品号获取订单
     * @param $gnum
     * @return array
     */
    public function getUserOrderDetailByGNum($gnum)
    {
        $obj = new GoodsGridRelationModel();
        $obj->setField('pwd');
        $rs = $obj->getGoodsGridRelation('g_num="' . $gnum . '"', '', '', '1');
        if (empty($rs)) {
            Run::show_msg('未找到商品');
        }
        $w = 'gnum="' . $gnum . '" and pwd<>"" ';
        $obj = new UserOrderDetailModel();
        $uodRs = $obj->getUserOrderDetail($w, '', '', '1');
        if (empty($uodRs)) {
            Run::show_msg('未找到订单');
        }
        $oRs = $this->getUserOrderOne($uodRs['oid'], $uodRs['uid']);
        return ['rs' => $oRs, 'odRs' => $uodRs];
    }

    /**
     * 添加坏的订单记录
     */
    public function addBadOrder()
    {
        $oid = Run::req('oid');
        $user = $this->getUserDetail();
        $uid = Run::req('uid');
        $img = Run::req('img');
        $damage_explain = Run::req('content');
        $damage_m = Run::req('money');

        $data['oid'] = $oid;
        $data['uid'] = $uid;
        $data['damage_money'] = $damage_m;
        $data['damage_standard'] = '';
        $data['damage_img'] = $img;
        $data['damage_explain'] = $damage_explain;

        $obj = new UserOrderDamageModel();
        $obj->addUserOrderDamage($data);

        //同步更新订单 损坏扣款 损坏状态 退款 真实退款
        $uoObj = new UserOrderModel();
        $uoRs = $this->getUserOrderOne($oid, $uid);
        $new_money = floatval($uoRs['overtime_money']) + floatval($data['damage_money']);

        if ($new_money < floatval($uoRs['deposit_money'])) {
            $real_refund_money = floatval($uoRs['deposit_money']) - $new_money;
        } else {
            $real_refund_money = 0;
        }

        $_tmp = [
            'damage_money' => floatval($data['damage_money']),
            'is_damage' => 1,
            'refund_money' => $uoRs['deposit_money'],
            'real_refund_money' => $real_refund_money,
        ];
        $uoObj->setUserOrder($uoRs['id'], $_tmp);
        //更新订单详情 损坏状态 损坏扣款
        $uodObj = new UserOrderDetailModel();
        $uodRs = $this->getUserOrderDetail($oid, $uid);
        $_tmp = [
            'is_damage' => '1',
            'damage_money' => floatval($data['damage_money']),
        ];
        $uodObj->setUserOrderDetail($uodRs['id'], $_tmp);

        $this->_jsonEn('1', '保存成功');
    }

    /**
     * 结算完成订单操作
     */
    public function endOrder()
    {
        $oid = Run::req('oid');
        $uid = Run::req('uid');
        $sign = Run::req('sign');
        if (empty($sign)) {
            $this->_jsonEn('0', '请选择验收状态');
        }
        $rs = $this->getUserOrderOne($oid, $uid);
        if (empty($rs)) {
            $this->_jsonEn('0', '订单查询失败');
        }
        if ($sign == 'good') {
            //如果是完好订单，直接退全额
            $obj = new UserOrderModel();
            $data['refund_money'] = $rs['deposit_money'];
            $data['real_refund_money'] = floatval($rs['deposit_money']) - floatval($rs['overtime_money']);

            $rs['refund_money'] = $data['refund_money'];
            $rs['real_refund_money'] = $data['real_refund_money'];

            $obj->setUserOrder($rs['id'], $data);
        }
        //var_dump($rs);exit;
        if (floatval($rs['real_refund_money']) == 0) {
            $obj = new UserOrderModel();
            $obj->setUserOrder($rs['id'], ['is_end' => 1]);
            $this->_jsonEn('1', '强制结算，无需要退款，退款金额为0');
        }

        //检验用户
        $uObj = new UsersController();
        $uRs = $uObj->getUserByUid($rs['uid']);
        if (empty($uRs)) {
            $this->_jsonEn('0', '用户未找到');
        }

        //退款操作
        $obj = new UserOrderRefundModel();
        $rRs = $obj->getUserOrderRefund('oid="' . $rs['oid'] . '"', '', '', '1');
        if (empty($rRs)) {
            $data ['oid'] = $rs['oid'];
            $data ['uid'] = $rs['uid'];
            $data ['refund_oid'] = $this->SysOrderId('TQ');
            $data ['wx_code'] = $rs['wx_code'];
            $data ['wx_refund_code'] = '';
            $data ['total_money'] = $rs['total_money'];
            $data ['refund_money'] = $rs['real_refund_money'];
            $data ['real_refund_money'] = '';
            $data ['sts'] = '0';
            $data ['created_at'] = date('Y-m-d H:i:s');
            $rRs = $obj->addUserOrderRefund($data);
            if ($rRs) {
                $wxObj = new WechatPayController();
                $refund_rs = $wxObj->refundOperation($data ['oid'], $data ['refund_oid'], $data ['total_money'], $data ['refund_money'], $uRs['open_id']);
            } else {
                $this->_jsonEn('0', '添加退款订单失败');
            }
        } else {
            if ($rRs['sts'] == '0') {
                $wxObj = new WechatPayController();
                $refund_rs = $wxObj->refundOperation($rRs ['oid'], $rRs ['refund_oid'], $rRs ['total_money'], $rRs ['refund_money'], $uRs['open_id']);
            } else {
                $this->_jsonEn('0', '该订单已经退款成功');
            }
        }
        if ($refund_rs) {
            $obj = new UserOrderModel();
            $obj->setUserOrder($rs['id'], ['is_end' => 1]);
            $this->_jsonEn('1', '微信申请退款成功');
        } else {
            $this->_jsonEn('0', '微信申请退款失败');
        }
    }

    /**
     * 强制结算
     */
    public function forceSetOrder()
    {
        $oid = Run::req('oid');
        $uid = Run::req('uid');
        $rs = $this->getUserOrderOne($oid, $uid);
        if (empty($rs)) {
            $this->_jsonEn('0', '订单查询失败');
        }
        $obj = new UserOrderModel();
        $obj->setUserOrder($rs['id'], ['is_end' => 1]);
        $this->_jsonEn('1', '强制结算成功');
    }

    /**
     * 查询异常订单
     */
    public function getUserOrderByAbnormal()
    {
        $obj = new UserOrderModel();
        $w = 'is_pay=1 and is_abnormal=1 and is_pickup=1 and is_end=0';
        $rs = $obj->getUserOrder($w);
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }

    /**
     * 查询损坏订单
     */
    public function getUserOrderByDamage()
    {
        $obj = new UserOrderModel();
        $w = 'is_pay=1 and is_damage=1 and is_end=1';
        $rs = $obj->getUserOrder($w);
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;

    }

    /**
     * 根据订单号，查询坏的订单
     */
    public function getUserOrderDamageByOid($_oid)
    {
        $obj = new UserOrderDamageModel();
        $rs = $obj->getUserOrderDamage('oid="' . $_oid . '"', '', '', '1');
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }
}