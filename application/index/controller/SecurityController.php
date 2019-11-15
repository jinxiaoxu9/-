<?php

namespace app\index\controller;

use app\common\library\enum\CodeEnum;
use app\index\logic\DepositLogic;
use app\index\logic\SecurityLogic;
use app\index\logic\UserLogic;
use think\Controller;
use think\db;
use think\Request;

/**
 * 安全控制器
 * Class WithdrawController
 * @package app\index\Controller
 */
class SecurityController extends CommonController
{
    public function updateLoginPassword()
    {
        $UserLogic = new UserLogic();
        $oldPassword = $this->request->post('old_password');
        $newPassword = $this->request->post('new_password');
        $newRePassword = $this->request->post('re_new_password');
        $res = $UserLogic->updateLoginPassword($this->user_id, $oldPassword, $newPassword, $newRePassword);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }


    public function getSecurityInfo()
    {
        $SecurityLogic = new SecurityLogic();
        $status = $SecurityLogic->checkHadSetSecurity($this->user_id);
        $data['have_security'] = $status ? 1 : 0;
        ajaxReturn('成功',1,'', $data);
    }
    
    public function updateSecurityPassword()
    {
        $SecurityLogic = new SecurityLogic();

        $security = $this->request->post('security');
        $re_security = $this->request->post('re_security');
        $old_security = $this->request->post('old_security');

        $res = $SecurityLogic->changeSecurity($this->user_id, $security, $re_security, $old_security);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }
}