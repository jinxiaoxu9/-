<?php
namespace app\admin\logic;

use app\admin\model\Group;
use app\admin\model\Menu;


class GroupLogic
{
    /**
     * 检查部门功能权限
     * @author jry <bbs.sasadown.cn>
     */
    public function checkMenuAuth( $controller_name = '')
    {
        $current_col = $controller_name; // 当前菜单
        $menu = new \app\admin\logic\MenuLogic();
        $user_col   = $menu->getCol(); // 获得当前登录用户信息

        if ($user_col !== 1) {
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