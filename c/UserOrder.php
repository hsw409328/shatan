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
        $oRs = $this->getUserOrderDetail($oid, $user['id']);
        $obj = new UserOrderModel();
        $rs = $obj->setUserOrder($oRs['id'], ['is_return' => 1, 'return_date' => date('Y-m-d H:i:s'), 'order_bak' => $content]);
        if ($rs) {
            $this->_jsonEn('1', '归还成功');
        } else {
            $this->_jsonEn('0', '归还失败');
        }
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
        $w = 'gnum="' . $gnum . '" and pwd="' . $rs['pwd'] . '" ';
        $obj = new UserOrderDetailModel();
        $uodRs = $obj->getUserOrderDetail($w, '', '', '1');
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
        $uid = $user['id'];
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
        $_tmp = [
            'damage_money' => floatval($data['damage_money']),
            'is_damage' => 1,
            'refund_money' => $uoRs['deposit_money'],
            'real_refund_money' => floatval($uoRs['deposit_money']) - floatval($data['damage_money']),
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
        $rs = $this->getUserOrderOne($oid, $uid);
        if (empty($rs)) {
            $this->_jsonEn('0', '订单查询失败');
        }
        $obj = new UserOrderModel();
        $obj->setUserOrder($rs['id'], ['is_end' => 1]);
        //退款操作
    }
}