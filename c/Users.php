<?php

final class UsersController extends Base
{

    //登录
    public function login()
    {
        $_oid = ParamsController::getSessionParams('openid');

        $obj = new UsersModel ();
        $w = ' open_id="' . $_oid . '" ';
        $res = $obj->getUsers($w, '', '1', '1');
        if (!empty($res)) {

            ParamsController::localSetParams('userDetail', $res);
            return $res;
        } else {
            ParamsController::unsetSession('userDetail');
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
        $wx_info = ParamsController::getSessionParams('open_wx_info');
        $_mobile = Run::req('mobile');
        $_validate = Run::req('validate');
        $_utype = Run::req('utype');
        $_cnum = Run::req('cnum');
        if ($_utype == '9') {
            if (empty($_cnum)) {
                $this->_jsonEn('0', '柜子编号不能为空');
            } else {
                $obj = new CabinetController();
                $rs = $obj->getCabinetDetail(strtoupper($_cnum));
                if (empty($rs)) {
                    $this->_jsonEn('0', '柜子编号不存在');
                }

            }
        }

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
        $dataArray ['user_type'] = $_utype;
        $dataArray ['u_nickname'] = $wx_info['nickname'];
        $dataArray ['u_head_photo'] = $wx_info['headimgurl'];
        $dataArray ['created_at'] = date('Y-m-d H:i:s');
        $dataArray ['u_lately_cabinet'] = $_cnum;

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
        $data ['created_at'] = date('Y-m-d H:i:s');
        $rs = $obj->addUsersInfoSys($data);
        return $rs;
    }

    private function _checkMobileValidateNum($_mobile, $_num)
    {
        $_validate_num = ParamsController::getSessionParams($_mobile . '_mobile');
        if ($_validate_num == $_num) {
            return true;
        } else {
            $this->_jsonEn('0', '验证码错误');
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

    //用户ID
    public function getUserByUid($_uid)
    {
        $obj = new UsersModel();
        $w = 'id="' . $_uid . '"';
        $res = $obj->getUsers($w, '', '1', '1');
        return $res;
    }

    //发送短信
    public function sendMobileNum()
    {
        $mobile = Run::req('mobile');
        if (strlen($mobile) != 11) {
            $this->_jsonEn('0', '请检查手机号');
        }
        $rs = WangYiCloudController::sendMobileValidate($mobile);
        if ($rs) {
            ParamsController::localSetParams($mobile . '_mobile', $rs);
            $this->_jsonEn('1', '发送成功');
        } else {
            $this->_jsonEn('0', '发送失败，请检查手机号');
        }
    }

    //保存意见反馈
    public function addFeedback()
    {
        $user = $this->getUserDetail();
        $obj = new UserFeedbackModel();
        $msg = Run::req('msg');
        if (empty($msg)) {
            $this->_jsonEn('0', '内容不能为空');
        }
        $rs = $obj->addUserFeedback([
            'uid' => $user['id'],
            'uname' => $user['u_nickname'],
            'msg' => $msg,
            'created_at' => date('Y-m-d H:i:s')]);
        if ($rs) {
            $this->_jsonEn('1', '感谢您的反馈，我们将及时处理');
        } else {
            $this->_jsonEn('0', '系统出现错误');
        }
    }

}