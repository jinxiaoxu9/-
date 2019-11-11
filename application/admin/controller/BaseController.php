<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use think\View;
use think\Config;

/**
 * Class BaseController
 * @package think
 */
class BaseController extends Controller
{
    /**
     * 架构函数
     * @param Request $request Request对象
     * @access public
     */
    public function _initialize()
    {
        parent::_initialize();
        $session_admin_id = session('user_auth.uid');
        if (!empty($session_admin_id)) {
            $user = \app\admin\model\Admin::name('admin')->where(['id' => $session_admin_id])->find();
            $this->assign("admin", $user);
        } else {
            if ($this->request->isPost()) {
                $this->error("您还没有登录！", url("admin/Pubss/login"));
            } else {
                header("Location:" . url("admin/Pubss/login"));
                exit();
            }
        }
        $request = Request::instance();
        $s_name_module = strtolower($request->module());
        $s_name_controller = strtolower($request->controller());
        $s_name_action = strtolower($request->action());

        $this->assign('s_name_module', $s_name_module);
        $this->assign('s_name_controller', $s_name_controller);
        $this->assign('s_name_action', $s_name_action);
    }

}