<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;

class UserController extends CommonController
{








    /****************************************end******************************************/







    public function test()
    {
        echo LOGIC_LAYER_NAME;
    }


    public function index()
    {
        $UserLogic = new UserLogic();
        $data = $UserLogic->getIndexInfo($where['userid'] = $this->user_id);
        $this->assign('userNavConfig', $data['config']);
        $this->assign('list', $data['userinfo']);
        $this->display();
    }


    public function getpage(&$m, $where, $pagesize = 10)
    {
        $m1 = clone $m;//浅复制一个模型
        $count = $m->where($where)->count();//连惯操作后会对join等操作进行重置
        $m = $m1;//为保持在为定的连惯操作，浅复制一个模型
        $p = new Think\Page($count, $pagesize);
        $p->lastSuffix = false;
        $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $p->setConfig('prev', '上一页');
        $p->setConfig('next', '下一页');
        $p->setConfig('last', '末页');
        $p->setConfig('first', '首页');

        $p->parameter = I('get.');

        $m->limit($p->firstRow, $p->listRows);

        return $p;
    }

    //重置密码
    public function set_password()
    {
        if ($_POST) {

            $uid = $this->user_id;
            $ulist = M('user')->where(array('userid' => $uid))->find();
            $salt = $ulist['login_salt'];

            $password = trim(I('post.password'));
            $sava['login_pwd'] = pwd_md5($password, $salt);
            $re = M('user')->where(array('userid' => $uid))->save($sava);
            if ($re) {
                $data['status'] = 1;
                $data['msg'] = '设置成功';
                ajaxReturn($data);
                exit;
            } else {
                $data['status'] = 0;
                $data['msg'] = '设置失败';
                ajaxReturn($data);
                exit;
            }

        } else {
            $data['status'] = 0;
            $data['msg'] = '网络错误';
            ajaxReturn($data);
            exit;
        }
    }



    public function security()
    {
        $SecurityLogic = new SecurityLogic();
        $haveSecurity = $SecurityLogic->checkHadSetSecurity($this->user_id);
        $this->assign('hava_security', $haveSecurity);
        $this->display();
    }

    public function updatesSecurity()
    {
        if ($_POST) {
            $SecurityLogic = new SecurityLogic();
            $security = trim(I('post.new_security'));
            $re_security = trim(I('post.re_new_security'));
            $old_security = trim(I('post.old_security'));
            $res = $SecurityLogic->changeSecurity($this->user_id, $security, $re_security, $old_security);
            if ($res['code'] == CodeEnum::ERROR) {
                $data['status'] = 0;
                $data['msg'] = '保存失败,' . $res['msg'];
                ajaxReturn($data);
            }
            $data['status'] = 1;
            $data['msg'] = '保存成功';
            ajaxReturn($data);
            exit;
        }
    }


    public function Setpwd()
    {
        $type = trim(I('type'));

        if ($type == 1) {
            $title = '修改登录密码';
        } else {
            $title = '修改交易密码';
        }
        if (IS_AJAX) {
            $user = D('Home/User');
            $user_object = D('Home/User');
            $uid = $this->user_id;
            $pwd = trim(I('pwd'));
            $pwdrpt = trim(I('pwdrpt'));
            $type = trim(I('pwdtype'));
            if ($pwdrpt == '') {
                ajaxReturn('新密码不能为空哦', 0);
            }
            $account = M('user')->where(array('userid' => $uid))->Field('account,mobile,login_pwd')->find();
            //验证初始密码
            $user_info = $user_object->Savepwd($account['mobile'], $pwd, $type);
            $salt = substr(md5(time()), 0, 3);
            if ($type == 1) {
                //密码加密
                $data['login_pwd'] = $user->pwdMd5($pwdrpt, $salt);
                $data['login_salt'] = $salt;
            } else {
                $data['safety_pwd'] = $user->pwdMd5($pwdrpt, $salt);
                $data['safety_salt'] = $salt;
            }
            $res_Sapwd = M('user')->where(array('userid' => $uid))->save($data);
            if ($res_Sapwd) {
                ajaxReturn('密码修改成功', 1, '/User/Personal');
            } else {
                ajaxReturn('密码修改失败', 0);
            }
        }
        $this->assign('title', $title);
        $this->assign('type', $type);
        $this->display();
    }

    /**
     * 修改密码
     */
    public function updatepassword()
    {
        if (!IS_AJAX)
            return;

        $password_old = I('post.old_pwdt');
        $password = I('post.new_pwd');
        $passwordr = I('post.rep_pwd');
        $two_password = I('post.new_pwdt');
        $two_passwordr = I('post.rep_pwdt');
        if (empty($password_old)) {
            ajaxReturn('请输入登录密码');
            return;
        }
        if ($password != $passwordr) {
            ajaxReturn('两次输入登录密码不一致');
            return;
        }

        if ($two_password != $two_passwordr) {
            ajaxReturn('两次输入交易密码不一致');
        }

        $user = D('User');
        $user->startTrans();
        //验证旧密码
        if (!$user->check_pwd_one($password_old)) {
            ajaxReturn('旧登录密码错误');
        }

        if (empty($data)) {
            ajaxReturn("请输入要修改的密码");
        }
        $user_id = $this->user_id;
        $where['userid'] = $user_id;
        $res = $user->where($where)->save($data);

        if ($res) {
            $user->commit();
            ajaxReturn("修改成功", 1);
        } else {
            $user->rollback();
            ajaxReturn("修改失败");
        }

    }
}