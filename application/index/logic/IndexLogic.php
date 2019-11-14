<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\UserInviteSettingModel;
use app\index\model\UserModel;
use app\index\logic\UserLogic;
use think\Db;

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

        $map['mobile|account'] = array('eq', $account, 'or');

        $UserModel =  new UserModel();

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
            if (pwdMd5($password, $user_info['login_salt']) != $user_info['login_pwd'])
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

    /**
     * @param $mobile
     * @param $username
     * @param $login_pwd
     * @param $inventCode
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register($mobile, $username, $login_pwd, $inventCode)
    {
        $UserInviteSetting = new UserInviteSettingModel();
        $setting = $UserInviteSetting->where(array('code'=>$inventCode))->find();

        if(empty($setting)){
            return ['code' => CodeEnum::ERROR, 'msg' =>$inventCode. '!推荐人不存在！' ];
        }

        if(!preg_match("/^1[34578]\d{9}$/", $mobile))
        {
            return ['code' => CodeEnum::ERROR, 'msg' =>'手机号码格式不正确！' ];
        }
        $salt = strrand(4);
        $UserModel =  new UserModel();
        $cuser= $UserModel->where(array('account'=>$mobile))->find();
        $muser= $UserModel->where(array('mobile'=>$mobile))->find();
        if(!empty($cuser) || !empty($muser)){
            return ['code' => CodeEnum::ERROR, 'msg' => '手机号已经被注册！'];
        }

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
        $data['login_pwd'] = pwdMd5($login_pwd,$salt);
        $data['login_salt'] = $salt;
        $data['reg_date'] = time();
        $data['reg_ip'] = get_userip();
        $data['status'] = 1;
        //$data['user_credit']= 5;
        $data['use_grade']= 1;
        $data['tx_status']= 1;

        $ure_re = Db::name('user')->insert($data);
        if($ure_re)
        {
            return ['code' => CodeEnum::SUCCESS, 'msg' => '注册成功！'];
        }
        else
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }
}