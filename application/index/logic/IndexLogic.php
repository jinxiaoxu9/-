<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\User;

class IndexLogic
{

    /**
     * 用户登录
     *
     */
    public function login($account, $password, $map = null)
    {
        //去除前后空格
        $account = trim($account);
        if (!isset($account) || empty($account)) {
            return ['code' => CodeEnum::ERROR, 'msg' => '账号不能为空'];
        }
        if (!isset($password) || empty($password)) {
            return ['code' => CodeEnum::ERROR, 'msg' => '密码不能为空'];
        }

        $map['mobile|account|userid'] = array('eq', $account);
        $UserModel =  new User();
        $user_info = $UserModel->where($map)->find();
        if (!$user_info)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '账号用户不存在'];
        }
        elseif ($user_info['status'] <= 0)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '您的账号已锁定，请联系管理员!'];
        }
        else
        {
            if (pwdMd5($password, $user_info['login_salt']) != $user_info['login_pwd'])
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '账号或密码错误！'];
            }
            else
            {
                $data['token'] = md5(time()."password");
                $UserModel->where($map)->save($data);

                return ['code' => CodeEnum::SUCCESS, 'msg' => '登录成功', 'data'=>$data];
            }
        }
    }
}