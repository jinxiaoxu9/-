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
    function checkIstz($adminId)
    {
        $admin = new Admin();
        $adminInfo = $admin->find($adminId);
        $tzRole= config('tz_id');
        if(isset($adminInfo['auth_id']) && $adminInfo['auth_id']==$tzRole) {//团长
            return true;
        }
        return false;
    }

    /**
     *团长角色的管理员
     */
    function tzUsers(){
        $adminId = session('user_auth.uid');

        $admin = new Admin();
        $adminInfo  = $admin->find($adminId);

        $tzRole= config('tz_id');
        $where =[];
        if($adminInfo['auth_id']==$tzRole) {//团长
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
        //匹配登录方式
        if (preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $username)) {
            $map['email'] = array('eq', $username); // 邮箱登陆
        } elseif (preg_match("/^1\d{10}$/", $username)) {
            $map['mobile'] = array('eq', $username); // 手机号登陆
        } else {
            $map['username'] = array('eq', $username); // 用户名登陆
        }
        $map['status'] = array('eq', 1);
        $admin = new Admin();
        $user_info     = $admin->where($map)->find(); //查找用户

        if (!$user_info) {
            return '用户不存在或被禁用！';
        } else {
            if (user_md5($password,null) !== $user_info['password']) {
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
        // 记录登录SESSION和COOKIES
        $auth = array(
            'uid'      => $user['id'],
            'username' => $user['username'],
            'nickname' => $user['nickname'],
            //'avatar'   => $user['avatar'],
        );
        session('user_auth', $auth);
        session('user_auth_sign', $this->data_auth_sign($auth));
        return $this->is_login();
    }

    /**
     * 数据签名认证
     * @param  array  $data 被认证的数据
     * @return string       签名
     * @author jry <bbs.sasadown.cn>
     */
    public function data_auth_sign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array) $data;
        }
        ksort($data); //排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code); // 生成签名
        return $sign;
    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     * @author jry <bbs.sasadown.cn>
     */
    public function is_login()
    {
        $user = session('user_auth');
        if (empty($user)) {
            return 0;
        } else {
            if (session('user_auth_sign') == $this->data_auth_sign($user)) {
                return $user['uid'];
            } else {
                return 0;
            }
        }
    }
}