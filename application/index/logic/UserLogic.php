<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\UserModel;
use think\Db;
//use app\index\model\ConfigModel;
use app\index\model\User;

class UserLogic extends BaseLogic
{


    /**
     * 注册逻辑
     * @param array $param
     */
    public function  register($param=[]){
        $inventCode = $param['invent_code'];
        $mobile  = $param ['mobile'];
        $username  = $param ['username'];
        $login_pwd  = $param ['login_pwd'];

        $UserInviteSetting = new \app\index\model\UserInviteSettingModel();
        $setting = $UserInviteSetting->where(array('code'=>$inventCode))->find();

        if(empty($setting)){
            return ['status' => CodeEnum::ERROR, 'message' =>$inventCode. '!推荐人不存在！' ];
        }
        $salt = strrand(4);
        $UserModel =  new \app\index\model\UserModel();
        $cuser= $UserModel->where(array('account'=>$mobile))->find();
        $muser= $UserModel->where(array('mobile'=>$mobile))->find();
        if(!empty($cuser) || !empty($muser)){
            return ['status' => CodeEnum::ERROR, 'message' => '手机号已经被注册！'];
        }

        $userLogic = new UserLogic();
        $data['pid'] = $setting['user_id'];
        $data['gid'] = 0;
        $data['ggid'] = 0;
        $data['account'] = $mobile;
        $data['mobile'] = $mobile;
        $data['u_yqm'] = $inventCode;
        $data['add_admin_id'] = $setting['admin_id'];
        //以下值要有默认值
        $data['email'] = $data['security_pwd'] = $data['usercard'] = $data['security_salt'] = $data['rz_st'] = '';
        $data['tx_status'] = $data['userqq'] = $data['u_ztnum'] = $data['group_id'] = 0;
        $data['zsy'] = 0.00;
        $data['username'] = $username;
        $data['login_pwd'] = $userLogic->pwd_md5($login_pwd,$salt);
        $data['login_salt'] = $salt;
        $data['reg_date'] = time();
        $data['reg_ip'] = $userLogic->get_userip();
        $data['status'] = 1;
        //$data['user_credit']= 5;
        $data['use_grade']= 1;
        $data['tx_status']= 1;

        $ure_re = Db::name('user')->insert($data);
        if($ure_re)
        {
            return ['status' => CodeEnum::SUCCESS, 'message' => '注册成功！'];
        }
        else
        {
            return ['status' => CodeEnum::ERROR, 'message' => '网络错误！'];
        }

    }


    /**
     * 登录逻辑
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

        $map['mobile|account'] = array('eq', $account, 'or');

        $UserModel =  new \app\index\model\UserModel();

        $user_info = $UserModel->where($map)->find();
        if (!$user_info)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '账号或密码错误'];
        }
        elseif ($user_info['status'] <= 0)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '您的账号已锁定，请联系管理员!'];
        }
        else
        {
            if (pwdMd5($password, $user_info['login_salt']) != $user_info['login_pwd']&&0)
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '账号或密码错误！'];
            }
            else
            {
                $data['token'] = md5(time()."password");
                $UserModel->where($map)->update($data);

                return ['code' => CodeEnum::SUCCESS, 'msg' => '登录成功', 'data'=>$data];
            }
        }
    }




    /*****************************************************************************/


    public function getIndexInfo($userId)
    {
        $UserModel = new User();
        $ConfigModel = new ConfigModel();

        $userInfo = $UserModel->where(array('userid' => $userId))->find();
        $conf = $ConfigModel->field('value')->where(['name' => 'USER_NAV'])->find();

        $data['config'] = json_decode($conf['value'], true);
        $data['userinfo'] = $userInfo;
        return $data;
    }

    public function updateLoginPassword($userId, $oldPassword, $newPassword, $newRePassword)
    {
        if (empty($oldPassword))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '请输入登录密码'];
        }

        if(count($newPassword)<6)
        {
            return ['code' => CodeEnum::ERROR, 'msg' =>'密码必须大于6位！' ];
        }

        if ($newPassword != $newRePassword) {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '两次输入登录密码不一致'];
        }

        $User = new UserModel();
        $User->startTrans();

        //验证旧密码
        if (!$User->check_pwd_one($oldPassword, $userId))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '旧登录密码错误'];
        }

        //=============登录密码加密==============
        if ($newPassword)
        {
            $salt = substr(md5(time()), 0, 3);
            $data['login_salt'] = $salt;
            $data['login_pwd'] = pwdMd5($newPassword, $salt);
        }

        $where['userid'] = $userId;
        $res = $User->isUpdate(true,$where)->save($data);
        if($res)
        {
            $User->commit();
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
        }
        else
        {
            $User->rollback();
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
        }
    }
}