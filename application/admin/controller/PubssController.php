<?php

namespace app\admin\controller;


use app\admin\model\GroupModel;
use think\Controller;
use think\Request;

class PubssController extends Controller
{

    /**
     * 架构函数
     * @param Request $request Request对象
     * @access public
     */
    public function _initialize()
    {
        $request = Request::instance();
        $s_name_module = strtolower($request->module());
        $s_name_controller = strtolower($request->controller());
        $s_name_action = strtolower($request->action());

        $this->assign('s_name_module', $s_name_module);
        $this->assign('s_name_controller', $s_name_controller);
        $this->assign('s_name_action', $s_name_action);
        parent::_initialize();
    }

    /**
     * 后台登陆
     */
    public function login()
    {
        $admin_id = session('admin_id');
        if (!empty($admin_id)) {//已经登录
            return redirect(url("admin/Index/index"));
        }
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $username = $param['username'];
            $password = $param['password'];

            // 图片验证码校验
            // if (!$this->check_verify(I('post.verify'))) {
            //     $this->error('验证码输入错误！');
            // }

            // 验证用户名密码是否正确
            // 

            $AdminLogic = new \app\admin\logic\AdminLogic();;
            $user_info   = $AdminLogic->login($username,$password);
            if (!isset($user_info['auth_id'])) {
                $this->error($user_info, url('admin/Pubss/login'));
            }
             // 验证该用户是否有管理权限
            $group = new GroupModel();
            $where['id']   = $user_info['auth_id'];
            $account_info   = $group->where($where)->find();
            if (!$account_info) {
                $this->error('该用户没有管理员权限', url('admin/Pubss/login'));
            }

            // 设置登录状态
            $uid = $AdminLogic->auto_login($user_info);

            // 跳转
            if ($uid) {
                $this->success('登录成功！', url('admin/Index/index'));
            } else {
                $this->logout();
            }
        } else {
            $this->assign('meta_title', '管理员登录');
            return $this->fetch();
        }
    }

    /**
     * 注销
     * @author jry <bbs.sasadown.cn>
     */
    public function logout()
    {
        session('admin_id', null);
        session('group_id', null);
        $this->success('退出成功！', url('login'));
    }

    /**
     * 图片验证码生成，用于登录和注册
     * @author jry <bbs.sasadown.cn>
     */
    public function verify($vid = 1)
    {
        $verify         = new Verify();
        $verify->length = 4;
        $verify->entry($vid);
    }

    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     * @return boolean 检测结果
     */
    public function check_verify($code, $vid = 1)
    {
        $verify = new Verify();
        return $verify->check($code, $vid);
    }
}
