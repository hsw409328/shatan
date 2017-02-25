<?php

final class AdminEuiController extends Base
{

    public function getAdminList()
    {
        $_p = Run::req('page');
        $_p = (empty ($_p) || $_p < 0) ? 0 : $_p - 1;
        $_page = Run::req('rows');
        $_page_prev = intval($_p) * intval($_page);
        $limit = $_page_prev . ',' . $_page;
        $aeObj = new AdminModel ();
        $res = $aeObj->getAdminUsers('', '', $limit);
        $aeObj->setField('count(id) as total');
        $totalRes = $aeObj->getAdminUsers('', '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res);
        echo json_encode($_tmpRes);
    }

    public function getAdminComBox()
    {
        $t = Run::req('t');
        if ($t == '1') {
            $_arr = array(
                array('uType' => '1', 'uTypeName' => '系统管理员'),
                array('uType' => '2', 'uTypeName' => '普通管理员'),
            );
        } elseif ($t == '2') {
            $_arr = array(array('uStatus' => '1', 'uStatusName' => '正常'), array('uStatus' => '2', 'uStatusName' => '禁用'));
        }
        echo json_encode($_arr);
    }

    public function getAdminUsersId($_id)
    {
        $obj = new AdminModel();
        $res = $obj->getAdminUsers("id='{$_id}'", '', '1', '1');
        return $res;
    }

    public function loginAdminUsers()
    {
        $_an = Run::req('uname');
        $_aw = Run::req('pwd');
        $obj = new AdminModel();
        $res = $obj->getAdminUsers(" uName='{$_an}' and uPwd='{$_aw}' and uStatus='1' ", '', '1', '1');
        if (empty ($res)) {
            echo '0';
        } else {
            $_SESSION [APP_NAME . '_admin_'] = $res;
            echo 1;
        }
    }

    public function logoutAdmin()
    {
        unset ($_SESSION [APP_NAME . '_admin_']);
        Run::show_msg('', '', '../login.html');
    }

    public function checkIsLoginAuth()
    {
        $res = $_SESSION [APP_NAME . '_admin_'];
        echo json_encode($res);
    }

    public function checkIsLogin()
    {
        if (isset ($_SESSION [APP_NAME . '_admin_'])) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function checkUName($_name)
    {
        $obj = new AdminModel();
        $res = $obj->getAdminUsers(" uName='{$_name}' ", '', '1', '1');
        if (empty ($res)) {
            return true;
        } else {
            echo 0;
            exit ();
        }
    }

    public function saveAdminUsers()
    {
        $_id = Run::req('id');

        $_d ['uName'] = Run::req('uName');
        $_d ['uPwd'] = Run::req('uPwd');
        $_d ['uType'] = Run::req('uType');
        $_d ['uStatus'] = Run::req('uStatus');
        $_d ['rbac'] = Run::req('rbac');

        $sObj = new AdminModel();
        if ($_id) {
            $res = $sObj->setAdminUsers($_id, $_d);
        } else {
            $this->checkUName($_d ['uName']);
            $res = $sObj->addAdminUsers($_d);
        }

        if ($res) {
            echo 1;
        }
    }

    public function leftTree()
    {
        $_userTree = array(
            'text' => '用户',
            'state' => 'open',
            'children' => array(
                array('id' => 'user.reg.list', 'text' => '注册用户'),
                ['id' => 'users.info.sys.list', 'text' => '审核用户'],
                array('id' => 'user.feedback.list', 'text' => '意见反馈'),
            )
        );
        $_user_orderTree = array(
            'text' => '订单',
            'state' => 'open',
            'children' => array(
                ['id' => 'users.order.good.list', 'text' => '订单列表'],
                ['id' => 'users.order.damage.list', 'text' => '损坏订单'],
                ['id' => 'users.order.refund.list', 'text' => '退款订单'],
            )
        );
        $_goodsTree = [
            'text' => '柜子相关',
            'state' => 'open',
            'children' => [
                ['id' => 'cabinet.list', 'text' => '柜子列表'],
                ['id' => 'shop.type.list', 'text' => '商品类目'],
                ['id' => 'goods.list', 'text' => '商品列表'],
                ['id' => 'cg.relation.list', 'text' => '格子-类目关联'],
                ['id' => 'goods.grid.relation.list', 'text' => '初始化柜子商品'],
                ['id' => 'c.bag.list', 'text' => '配送袋关联'],
                ['id' => 'index.weather.list', 'text' => '配置天气'],
                ['id' => 'cabinet.lnglat.list', 'text' => '配置坐标与矩子'],
            ]
        ];
        $_sysTree = array(
            'text' => '系统',
            'state' => 'open',
            'children' => array(
                array('id' => 'sys.cate.list', 'text' => '系统分类'),
                array('id' => 'user.admin.list', 'text' => '管理员'),
            )
        );
        $_endTree = array(
            $_userTree,$_user_orderTree, $_goodsTree, $_sysTree
        );
        return $_endTree;
    }

    public function getLeftCombox()
    {
        $res = $this->leftTree();
        $_arr = array();
        foreach ($res as $k => $v) {
            $_arr[]['rbac'] = $v['text'];
        }
        echo json_encode($_arr);
    }


    public function getCheckLeftResult()
    {
        if (isset($_SESSION [APP_NAME . '_admin_'])) {
            $adminRes = $_SESSION [APP_NAME . '_admin_'];
            $res = $this->leftTree();
            $_arr = array();
            $_str = explode(',', $adminRes['rbac']);
            foreach ($res as $k => $v) {
                if (in_array($v['text'], $_str)) {
                    $_arr[] = $v;
                }
            }
            echo json_encode($_arr);
            exit;
        } else {
            echo '';
            exit();
        }
    }
}