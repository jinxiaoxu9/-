<?php
namespace app\admin\logic;

use app\admin\model\Group;


class GroupLogic
{
    /**
     * 检查部门功能权限
     * @author jry <bbs.sasadown.cn>
     */
    public function checkMenuAuth()
    {
        $current_col = CONTROLLER_NAME; // 当前菜单
        $user_col   = D('Admin/Menu')->getCol(); // 获得当前登录用户信息
        if ($user_col !== '1') {
            if(!in_array($current_col,$user_col)){
                return false;
            }

            return true;
        } else {
            return true; // 超级管理员无需验证
        }
        return false;
    }
}