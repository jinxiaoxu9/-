<?php
namespace app\admin\controller;


use think\Controller;
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
        $request = Request::instance();
        $s_name_module = strtolower($request->module());
        $s_name_controller = strtolower($request->controller());
        $s_name_action = strtolower($request->action());

        $this->assign('s_name_module', $s_name_module);
        $this->assign('s_name_controller', $s_name_controller);
        $this->assign('s_name_action', $s_name_action);
        parent::_initialize();
    }

}