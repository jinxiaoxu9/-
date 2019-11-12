<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\User;
use app\index\model\UserModel;

class SecurityLogic
{
    /**
     * 通过用户id检测安全码
     * @param $userId
     * @param $security
     */
    public function checkSecurityByUserId($userId, $security)
    {
        if (empty($security))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '安全码不能为空'];
        }

        $UserModel = new UserModel();
        $userInfo = $UserModel->find($userId);
        if(empty($userInfo['security_pwd']))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '请先前往个人资料设置安全码'];
        }

        if((pwdMd5($security,$userInfo['security_salt']) != $userInfo['security_pwd']))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '安全码错误'];
        }

        return ['code' => CodeEnum::SUCCESS, 'msg' => 'OK'];
    }

    /**
     * 用户修改安全码
     * @param $userId
     * @param $security
     * @return array
     */
    public function changeSecurity($userId, $security, $re_security, $old_security)
    {
        $UserModel = new User();
        $userInfo = $UserModel->find($userId);
        if(!empty($userInfo['security_pwd']))
        {
            if(empty($old_security))
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '请输入旧安全码'];
            }

            if(pwdMd5($old_security.$userInfo['security_salt']) != $userInfo['security_pwd'])
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '旧安全码错误'];
            }
        }

        if($security != $re_security)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '安全码不一致'];
        }

        if(strlen($security) < 4 )
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '安全码必须大于4位'];
        }


        $where['userid'] = $userId;
        $data['security_salt'] = strrand(4);
        $data['security_pwd'] = pwdMd5($re_security.$data['security_salt']);

        $ret = $UserModel->where($where)->save($data);
        if(!$ret)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '保存失败,请稍后再试'];
        }

        return ['code' => CodeEnum::SUCCESS, 'msg' => '修改成功'];
    }

    /**
     * 判断用户是否设置过安全码
     * @param $userId
     * @return bool
     */
    public function checkHadSetSecurity($userId)
    {
        $UserModel = new User();
        $userInfo = $UserModel->find($userId);
        if(empty($userInfo['security_pwd']))
        {
            return false;
        }

        return true;
    }
}