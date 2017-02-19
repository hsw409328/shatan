<?php

final class WebTypeEuiController extends Base
{

    public function getWebTypeList()
    {
        $obj = new WebTypeModel ();
        $obj->setField('*,parentId as _parentId');
        $res = $obj->getWebType();
        $res [] = array("id" => "0", "parentId" => "0", "sName" => "最高级", "wStatus" => "1");
        $obj->setField('count(id) as total');
        $totalRes = $obj->getWebType('', '', '', '1');
        $_tmpRes = array('total' => $totalRes ['total'], 'rows' => $res, "footer" => array("name" => "Total Record:", "record" => $totalRes ['total'], "iconCls" => "icon-sum"));
        echo json_encode($_tmpRes);
    }

    public function getWebTypeParentLevel()
    {
        $obj = new WebTypeModel();
        $obj->setField('id,sName as text');
        $rs = $obj->getWebType('parentId=0');
        echo json_encode($rs);
        exit;
    }

    public function getWebTypeParentChil()
    {
        $pid = Run::req('_pid');
        $obj = new WebTypeModel();
        $obj->setField('id,sName as text');
        $rs = $obj->getWebType('parentId=' . $pid);
        echo json_encode($rs);
        exit;
    }

    public function getCateSelectHtml()
    {
        $_s = Run::req('_s');
        $_pid = Run::req('_pid');
        $_pid = $_pid ? $_pid : '0';
        $res = $this->getInfiniteHtml($_s);
        echo $res;
    }

    public function getChildSelectHtml()
    {
        $_name = Run::req('_name');
        $_name = $_name ? $_name : 'wType';

        $_fid = Run::req('_fid');
        $_fid = $_fid ? $_fid : '1';
        $_sw = Run::req('_sw');
        $_sw = $_sw ? $_sw : '4';

        $data = $this->getInfiniteHtml($_sw, $_fid);

        $_html = '<select name="' . $_name . '">';
        foreach ($data as $ok => $ov) {
            $_html .= "<option value='{$ov['id']}'>{$ov['sName']}</option>";
        }
        $_html .= '</select>';
        echo $_html;
    }

    public function getWebTypeAll($_s = '')
    {
        $obj = new WebTypeModel();
        $res = $obj->getWebType();
        if ($_s == '1') {
            $newR = array();
            foreach ($res as $k => $v) {
                $newR [$v ['id']] = $v ['sName'];
            }
            $res = $newR;
        }
        return $res;
    }

    public function getInfiniteHtml($_s = '1', $fid = '0')
    {
        $_data = $this->getWebTypeAll();

        switch ($_s) {
            case '1' :
                $res = $this->getTreeUl($_data, '0');
                break;
            case '2' :
                $res = $this->getTreeOption($_data, '0');
                break;
            case '3' :
                $res = $this->getTreeParent($_data, '0');
                break;
            case '4' :
                $res = $this->getTreeChild($_data, $fid);
                break;
            default :
                $res = $this->getTreeUl($_data, '0');
                break;
        }

        return $res;
    }

    public function getSelectHtml($data, $name = "wType")
    {
        $_html = '<select name="' . $name . '">';
        foreach ($data as $ok => $ov) {
            $_html .= "<option value='{$ov['id']}'>{$ov['sName']}</option>";
        }
        $_html .= '</select>';
        return $_html;
    }

    public function getTreeChild($data, $parentId)
    {
        $result = array();
        $parentIds = array($parentId);
        do {
            $cids = array();
            $flag = false;
            foreach ($parentIds as $parentId) {
                for ($i = count($data) - 1; $i >= 0; $i--) {
                    $node = $data [$i];
                    if ($node ['parentId'] == $parentId) {
                        array_splice($data, $i, 1);
                        $result [] = $node;
                        $cids [] = $node ['id'];
                        $flag = true;
                    }
                }
            }
            $parentIds = $cids;
        } while ($flag === true);
        return $result;
    }

    public function getTreeParent($data, $id)
    {
        $result = array();
        $obj = array();
        foreach ($data as $node) {
            $obj [$node ['id']] = $node;
        }

        $value = isset ($obj [$id]) ? $obj [$id] : null;
        while ($value) {
            $id = null;
            foreach ($data as $node) {
                if ($node ['id'] == $value ['parentId']) {
                    $id = $node ['id'];
                    $result [] = $node ['id'];
                    break;
                }
            }
            if ($id === null) {
                $result [] = $value ['parentId'];
            }
            $value = isset ($obj [$id]) ? $obj [$id] : null;
        }
        unset ($obj);
        return $result;
    }

    public function getTreeUl($data, $parentId)
    {
        $stack = array($parentId);
        $child = array();
        $added_left = array();
        $added_right = array();
        $html_left = array();
        $html_right = array();
        $obj = array();
        $loop = 0;
        foreach ($data as $node) {
            $pid = $node ['parentId'];
            if (!isset ($child [$pid])) {
                $child [$pid] = array();
            }
            array_push($child [$pid], $node ['id']);
            $obj [$node ['id']] = $node;
        }

        while (count($stack) > 0) {
            $id = $stack [0];
            $flag = false;
            $node = isset ($obj [$id]) ? $obj [$id] : null;
            if (isset ($child [$id])) {
                $cids = $child [$id];
                $length = count($cids);
                for ($i = $length - 1; $i >= 0; $i--) {
                    array_unshift($stack, $cids [$i]);
                }
                $obj [$cids [$length - 1]] ['isLastChild'] = true;
                $obj [$cids [0]] ['isFirstChild'] = true;
                $flag = true;
            }
            if ($id != $parentId && $node && !isset ($added_left [$id])) {
                if (isset ($node ['isFirstChild']) && isset ($node ['isLastChild'])) {
                    $html_left [] = '<li class="first-child last-child">';
                } else if (isset ($node ['isFirstChild'])) {
                    $html_left [] = '<li class="first-child">';
                } else if (isset ($node ['isLastChild'])) {
                    $html_left [] = '<li class="last-child">';
                } else {
                    $html_left [] = '<li>';
                }
                // | <a href='javascript:void(0);' onclick='del(\"WebType\",\"delWebType\",\"{$node['id']}\")'>删除</a>
                $html_left [] = ($flag === true) ? "<div>{$node['id']}、{$node['sName']}</div><ul>" : "<div>{$node['id']}、{$node['sName']} <a href='?v=website.type.add&i={$node['id']}'>编辑</a></div>";
                $added_left [$id] = true;
            }
            if ($id != $parentId && $node && !isset ($added_right [$id])) {
                $html_right [] = ($flag === true) ? '</ul></li>' : '</li>';
                $added_right [$id] = true;
            }

            if ($flag == false) {
                if ($node) {
                    $cids = $child [$node ['parentId']];
                    for ($i = count($cids) - 1; $i >= 0; $i--) {
                        if ($cids [$i] == $id) {
                            array_splice($child [$node ['parentId']], $i, 1);
                            break;
                        }
                    }
                    if (count($child [$node ['parentId']]) == 0) {
                        $child [$node ['parentId']] = null;
                    }
                }
                array_push($html_left, array_pop($html_right));
                array_shift($stack);
            }
            $loop++;
            if ($loop > 5000)
                return $html_left;
        }
        unset ($child);
        unset ($obj);
        return implode('', $html_left);
    }

    public function getTreeOption($data, $parentId)
    {
        $_name = Run::req('_name');
        $_name = empty($_name) ? 'wType' : $_name;
        $stack = array($parentId);
        $child = array();
        $added = array();
        $options = array();
        $obj = array();
        $loop = 0;
        $depth = -1;
        foreach ($data as $node) {
            $pid = $node ['parentId'];
            if (!isset ($child [$pid])) {
                $child [$pid] = array();
            }
            array_push($child [$pid], $node ['id']);
            $obj [$node ['id']] = $node;
        }

        while (count($stack) > 0) {
            $id = $stack [0];
            $flag = false;
            $node = isset ($obj [$id]) ? $obj [$id] : null;
            if (isset ($child [$id])) {
                for ($i = count($child [$id]) - 1; $i >= 0; $i--) {
                    array_unshift($stack, $child [$id] [$i]);
                }
                $flag = true;
            }
            if ($id != $parentId && $node && !isset ($added [$id])) {
                $node ['depth'] = $depth;
                $options [] = $node;
                $added [$id] = true;
            }
            if ($flag == true) {
                $depth++;
            } else {
                if ($node) {
                    for ($i = count($child [$node ['parentId']]) - 1; $i >= 0; $i--) {
                        if ($child [$node ['parentId']] [$i] == $id) {
                            array_splice($child [$node ['parentId']], $i, 1);
                            break;
                        }
                    }
                    if (count($child [$node ['parentId']]) == 0) {
                        $child [$node ['parentId']] = null;
                        $depth--;
                    }
                }
                array_shift($stack);
            }
            $loop++;
            if ($loop > 5000)
                return $options;
        }
        unset ($child);
        unset ($obj);
        $_html = '<select name="' . $_name . '"><option value="0">|一级分类</option>';
        $_strH = '';
        foreach ($options as $ok => $ov) {
            $_strH = str_repeat('——', $ov ['depth']);
            $_html .= "<option value='{$ov['id']}'>|{$_strH}{$ov['sName']}</option>";
        }
        $_html .= '</select>';
        //return $options;
        return $_html;
    }

    public function getWebTypeId($_id)
    {
        $obj = new WebTypeModel();
        $res = $obj->getWebType("id='{$_id}'", '', '1', '1');
        return $res;
    }

    public function saveWebType()
    {
        $_id = Run::req('id');

        $_d ['parentId'] = Run::req('parentId');
        $_d ['wStatus'] = Run::req('wStatus');
        $_d ['sName'] = Run::req('sName');
        $_d ['sUrl'] = Run::req('sUrl');

        $sObj = new WebTypeModel();
        if ($_id) {
            $res = $sObj->setWebType($_id, $_d);
        } else {
            $res = $sObj->addWebType($_d);
        }

        if ($res) {
            echo '操作成功';
        } else {
            echo '操作失败';
        }
    }

    public function delWebType()
    {
        $_id = Run::req('id');
        $sObj = new WebTypeModel();
        $res = $sObj->delWebType($_id);
        if ($res) {
            echo 1;
        } else {
            echo 0;
        }
    }

}