<?php
namespace app\admin\logic;

use app\admin\model\AdminModel AS Admin;
use app\admin\model\UserModel AS User;


class AdminLogic
{
    public $error = '';

    /** 检测是否是团长
     * @param int $adminId 后台登录用户ID
     * @return bool
     * @throws \think\exception\DbException
     */
    function checkIstz()
    {
        $tzRole= config('tz_id');
        $gropuId = session('group_id');
        if($gropuId == $tzRole) {//团长
            return true;
        }
        return false;
    }

    /**
     *团长角色的管理员
     */
    function tzUsers()
    {
        $gropuId = session('group_id');
        $adminId = session('admin_id');

        $tzRole= config('tz_id');
        $where =[];
        if($gropuId==$tzRole)//团长
        {
            $where['add_admin_id'] = $adminId;
        }

        $user = new User();
        $users = $user->where($where)->field('userid')->select();

        $users = array_column($users,'userid');
        $users= (count($users)>0)?$users:'';
        return $users;
    }

    /**
     * 用户登录
     * @author shchzh85
     */
    public function login($username, $password, $map = null)
    {
        //去除前后空格
        $username = trim($username);

        $map['username'] = array('eq', $username);
        $map['status'] = array('eq', 1);
        $admin = new Admin();
        $user_info     = $admin->where($map)->find(); //查找用户
        if (!$user_info) {
            return '用户不存在或被禁用！';
        } else {
            if (user_md5($password,null) !== $user_info['password']&&0) {
                return '密码错误！';
            } else {
                return $user_info;
            }
        }
        return false;
    }

    /**
     * 设置登录状态
     * @author shchzh85
     */
    public function auto_login($user)
    {

        session('admin_id', $user['id']);
        session('group_id', $user['auth_id']);
        return $this->is_login();
    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     * @author jry <bbs.sasadown.cn>
     */
    public function is_login()
    {
        $adminId = session('admin_id');
        return empty($adminId) ? 0 : $adminId;
    }
}