<?php

final class UsersController extends Base
{

    //登录
    public function login()
    {
        //$this->checkValiNum();

        $_oid = ParamsController::getSessionParams('openid');

        $obj = new UsersModel ();
        $w = ' open_id="' . $_oid . '" ';
        $res = $obj->getUsers($w, '', '1', '1');

        if (empty($res)) {
            ParamsController::localSetParams('userDetail', $res);
            return $res;
        } else {
            return [];
        }
    }

    public function logout()
    {
        ParamsController::unsetSession('userDetail');
        Run::show_msg();
    }

    //注册
    public function reg()
    {
        //$this->checkValiNum ();

        $_mobile = Run::req('mobile');
        $_validate = Run::req('validate');
        $checkRes = $this->checkMobile($_mobile);
        if ($checkRes) {
            $this->_jsonEn('0', '手机号已经存在');
        }

        if (!$_mobile) {
            $this->_jsonEn('0', '手机不能为空');
        }
        $this->_checkMobileValidateNum($_mobile, $_validate);

        $obj = new UsersModel ();
        $dataArray ['id'] = $this->Sysid();
        $dataArray ['open_id'] = ParamsController::getSessionParams('openid');
        $dataArray ['mobile_num'] = $_mobile;
        $dataArray ['user_type'] = Run::req('utype');
        $dataArray ['u_nickname'] = '';
        $dataArray ['u_head_photo'] = '';
        $dataArray ['created_at'] = date('Y-m-d H:i:s');

        $res = $obj->addUsers($dataArray);
        if ($res) {
            $this->_addUsersInfoSys($dataArray['id']);
            ParamsController::localSetParams('userDetail', $dataArray);
            $this->_jsonEn('1', '注册成功');
        } else {
            $this->_jsonEn('0', '注册失败');
        }
    }

    private function _addUsersInfoSys($_uid)
    {
        $obj = new UsersInfoSysModel();
        $data ['uid'] = $_uid;
        $rs = $obj->addUsersInfoSys($data);
        return $rs;
    }

    private function _checkMobileValidateNum($_mobile, $_num)
    {
        $_validate_num = ParamsController::getSessionParams($_mobile . '_mobile');
        if ($_validate_num == $_num) {
            return true;
        } else {
            //$this->_jsonEn('0', '验证码错误');
        }
    }

    //检查用户名
    public function checkMobile($_mobile = '')
    {
        $obj = new UsersModel ();
        $w = 'mobile_num="' . $_mobile . '"';
        $res = $obj->getUsers($w, '', '1', '1');
        return $res;
    }

    //检查第三方识别
    public function checkOpenId($_openid = '')
    {
        $obj = new UsersModel ();
        $w = 'open_id="' . $_openid . '"';
        $res = $obj->getUsers($w, '', '1', '1');
        return $res;
    }

}