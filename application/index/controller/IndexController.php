<?php
namespace app\index\controller;

use app\common\library\enum\CodeEnum;
use think\Controller;
use app\index\logic\IndexLogic;


class IndexController extends Controller
{
    public function index()
    {
        ajaxReturn('test',1,'', '');
    }
	public function doregister()
    {
		if($this->request->isPost())
		{
            $invent_code = $this->request->post('invent_code');
            $username = $this->request->post('username');
            $mobile = $this->request->post('mobile');
            $password = $this->request->post('login_pwd');

            $indexLogic = new IndexLogic();
            $res = $indexLogic->register($mobile, $username, $password, $invent_code);
            if ($res['code'] == CodeEnum::ERROR)
            {
                ajaxReturn($res['msg'],0);
            }
            ajaxReturn('注册成功',1,'', url('User/index'));
		}
	}

    public function dologin()
    {
        if ($this->request->isPost())
        {
            $account = $this->request->post('account');
            $password = $this->request->post('password');
            $IndexLogic = new \app\index\logic\IndexLogic();
            // 验证用户名密码是否正确
            $res = $IndexLogic->login($account, $password);
            if ($res['code'] == CodeEnum::ERROR)
            {
                ajaxReturn($res['msg'],0);
            }
            ajaxReturn('登录成功',1,'', $res['data']);
        }
    }
}
