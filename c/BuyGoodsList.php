<?php

/**
 * Created by PhpStorm.
 * User: MrHao
 * Date: 2017/2/17
 * Time: 14:04
 */
final class BuyGoodsListController extends Base
{
    //获取用户默认柜子
    public function getUserDefaultCabinet()
    {
        $user = $this->getUserDetail();
        return $user['u_lately_cabinet'];
    }

    //更新用户默认柜子
    public function updateUserDefaultCabinet()
    {
        $_cnum = Run::req('cnum');
        $_cnum = strtoupper($_cnum);
        $obj = new CabinetController();
        $rs1 = $obj->getCabinetDetail($_cnum);
        if (empty($rs1)) {
            $this->_jsonEn('0', '柜子编号不存在');
        }
        $user = $this->getUserDetail();
        $obj = new UsersModel();
        $user['u_lately_cabinet'] = $_cnum;
        $rs = $obj->setUsers($user['id'], $user);
        if ($rs) {
            ParamsController::localSetParams('userDeatil', $user);
            $this->_jsonEn('1', '更新成功');
        } else {
            $this->_jsonEn('0', '更新失败');
        }
    }

    //通过柜子获取可购买的类目
    public function getCabinetShopType($c_num)
    {
        $obj = new CgRelationModel();
        $obj->setField('st_num,c_grid_area,c_num');
        $rs = $obj->getCgRelation('c_num="' . $c_num . '"');
        if (empty($rs)) {
            $rs = [];
        }
        return $rs;
    }

    //检查是否有货
    public function checkStGoods($cnum, $stnum, $gridnum, $_array_return = false)
    {
        $obj = new GoodsGridRelationModel();
        $gridnum = explode(',', $gridnum);
        $gridnum = array_filter($gridnum);
        $gridnum = implode('","', $gridnum);
        if (!$_array_return) {
            $obj->setField('count(id) as num ');
            $s = 1;
        } else {
            $s = '';
        }
        $w = 'c_num="' . $cnum . '" and st_num="' . $stnum . '" and grid_num in ("' . $gridnum . '") and sts="4"';
        $rs = $obj->getGoodsGridRelation($w, '', '', $s);
        if ($_array_return) {
            return $rs;
        }
        return $rs['num'];
    }

    /**
     * 给用户随机发送有商品的格子与密码  支付完成回调回来之后才发送，否则不发送
     * @param $cnum
     * @param $stnum
     */
    public function sendUserGridGoods($cnum, $stnum)
    {

    }

    /**
     * 创建订单
     */
    public function createOrder()
    {
        $user = $this->getUserDetail();
        $_oid = $this->SysOrderId();
        $obj = new CgRelationModel();
        $cgRs = $obj->getCgRelation('c_num="' . RouteClass::getParams('3') . '" and st_num="' . RouteClass::getParams('4') . '"', '', '', '1');
        if (empty($cgRs)) {
            Run::show_msg('柜子与类目不匹配');
        }
        $obj = new CabinetController();
        $cRs = $obj->getCabinetDetail($cgRs['c_num']);
        $obj = new ShopTypeController();
        $stRs = $obj->getShopTypeDetail($cgRs['st_num']);

        $_d = date('Y-m-d H:i:s');
        $_end_d = date('Y-m-d H:i:s', strtotime('+' . $stRs['st_day'] . 'day '));

        $grid_goods = $this->checkStGoods($cgRs['c_num'], $cgRs['st_num'], $cgRs['c_grid_area'], true);
        if (empty($grid_goods)) {
            Run::show_msg('没有物品了，请选择其它物品');
        } else {
            $gnum = $grid_goods[0]['g_num'];
            $grid = $grid_goods[0]['grid_num'];
        }

        $obj = new GoodsModel();
        $gRs = $obj->getGoods('g_num="' . $gnum . '"', '', '', '1');
        if (empty($gRs)) {
            Run::show_msg('商品不存在');
        }

        $data ['oid'] = $_oid;
        $data ['uid'] = $user['id'];
        $data ['cnum'] = $cgRs['c_num'];
        $data ['stnum'] = $cgRs['st_num'];
        $data ['rent_date_start'] = $_d;
        $data ['rent_date_end'] = $_end_d;
        $data ['city'] = '';
        $data ['city_name'] = $cRs['c_city'];
        $data ['address'] = $cRs['c_area'] . $cRs['c_hotel'] . $cRs['c_address'];
        $data ['total_money'] = floatval($gRs['g_deposit']) + floatval($gRs['g_rent']);
        $data ['rent_money'] = floatval($gRs['g_rent']);
        $data ['deposit_money'] = floatval($gRs['g_deposit']);
        $data ['refund_money'] = '0.00';
        $data ['real_refund_money'] = '0.00';
        $data ['overtime_money'] = '0.00';
        $data ['overtime_desc'] = '';
        $data ['damage_money'] = '0.00';
        $data ['is_pay'] = '0';
        $data ['is_pickup'] = '0';
        $data ['is_return'] = '0';
        $data ['is_abnormal'] = '0';
        $data ['is_damage'] = '0';
        $data ['is_end'] = '0';
        $data['order_bak'] = '';
        $data ['real_pay_money'] = $data ['total_money'];
        $data['wx_code'] = '';

        $obj = new UserOrderModel();
        $rs = $obj->addUserOrder($data);
        if ($rs) {
            //创建子订单
            $odRs = $this->createOrderDetail($data, $gRs, $grid);
            return ['oRs' => $data, 'odRs' => $odRs];
        } else {
            Run::show_msg('创建订单失败');
        }
    }

    private function createOrderDetail($orderData, $gRs, $gridnum)
    {
        $obj = new UserOrderDetailModel();
        $data['oid'] = $orderData['oid'];
        $data['uid'] = $orderData['uid'];
        $data['gnum'] = $gRs['g_num'];
        $data['gnum_name'] = $gRs['g_name'];
        $data['gridnum'] = $gridnum;
        $data['stnum'] = $gRs['g_st_num'];
        $data['cnum'] = $orderData['cnum'];
        $data['pwd'] = '';
        $data['price'] = $gRs['g_rent'];
        $data['deposit'] = $gRs['g_deposit'];
        $data['is_damage'] = '0';
        $data['damage_money'] = '0.00';
        $obj->addUserOrderDetail($data);
        return $data;
    }

    /**
     * 发起支付的时候请求
     */
    public function pay()
    {
        $oid = Run::req('oid');
        $obj = new UserOrderModel();
        $rs = $obj->getUserOrder('oid="' . $oid . '"', '', '', '1');
        $payObj = new WechatPayController();
        $user = $this->getUserDetail();
        $rs = $payObj->pay($rs['oid'], $rs['real_pay_money'], $user['open_id']);
        if (!$rs) {
            $this->_jsonEn('0', '发起支付失败');
        }
        $this->_jsonEn('1', $rs);
    }

    public function getUserOrder()
    {
        $oid = RouteClass::getParams('3');
        $user = $this->getUserDetail();
        $odObj = new UserOrderDetailModel();
        $obj = new UserOrderModel();
        $w = 'oid="' . $oid . '" and uid="' . $user['id'] . '"';
        $rs = $obj->getUserOrder($w, '', '', '1');
        if (empty($rs)) {
            Run::show_msg('订单号与用户不匹配', '1', '/');
        }
        $odRs = $odObj->getUserOrderDetail($w, '', '', '1');
        return ['rs' => $rs, 'odRs' => $odRs];
    }
}