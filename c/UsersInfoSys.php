<?php

final class UsersInfoSysController extends Base
{
    /**
     * 根据用户ID查询用户附表信息
     * @param $_uid 用户ID
     * @return array|null
     */
    public function getUsersInfoSysUid($_uid)
    {
        $obj = new UsersInfoSysModel();
        return $obj->getUsersInfoSys('uid="' . $_uid . '"', '', '', '1');
    }

    /**
     * 根据柜子查询用户信息
     *
     */
    public function getUserInfoByCabinet($cnum)
    {
        $obj = new UsersInfoSysModel();
        return $obj->getUsersInfoSys('u_cabinet like "%' . $cnum . '%"', '', '', '1');
    }
}