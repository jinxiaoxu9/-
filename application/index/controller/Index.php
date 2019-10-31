<?php
namespace app\index\controller;
use app\common\library\enum\CodeEnum;
use think\Controller;

class Index extends Controller
{

	public function register(){
		if($this->request->isPost())
		{
			$u_yqm = trim(I('post.pid'));
			$setting = M('user_invite_setting')->where(array('code'=>$u_yqm))->find();

			if(empty($setting)){
				$re_data['status'] = 0;
				$re_data['message'] = "推荐人不存在！";				
				 ajaxReturn($re_data);exit;
			}
			
			$username = trim(I('post.username'));
			$mobile = trim(I('post.mobile'));
			$login_pwd = trim(I('post.login_pwd'));
			//$safety_pwd = trim(I('post.safety_pwd'));
			$salt = strrand(4);
			$cuser= M('user')->where(array('account'=>$mobile))->find();
			$muser= M('user')->where(array('mobile'=>$mobile))->find();
			if(!empty($cuser) || !empty($muser)){
				$re_data['status'] = 1;
				$re_data['message'] = "手机号已经被注册";																			
				ajaxReturn($re_data);exit;
			}

            $code= M('user_invite_setting')->where(array('code'=>$u_yqm))->find();
            if(empty($code))
            {
                $re_data['status'] = 1;
                $re_data['message'] = "邀请码不存在";
                ajaxReturn($re_data);exit;
            }

			$data['pid'] = $setting['user_id'];
			$data['gid'] = 0;
			$data['ggid'] = 0;
			$data['account'] = $mobile;
			$data['mobile'] = $mobile;
			$data['u_yqm'] = $u_yqm;

            $data['add_admin_id'] = $code['admin_id'];

			$data['username'] = $username;
			$data['login_pwd'] = pwd_md5($login_pwd,$salt);
			$data['login_salt'] = $salt;
			$data['reg_date'] = time();
			$data['reg_ip'] = get_userip();
			$data['status'] = 1;			
			$path=$sonelist['path'];
            if(empty($path)){
                $data['path']='-'.$sonelist['userid'].'-';
            }else{
                $data['path']=$path.$sonelist['userid'].'-';
            }
			//$data['user_credit']= 5;
			$data['use_grade']= 1;
			$data['u_ztnum']= 0;	
			$data['tx_status']= 1;	
			
			$ure_re = M('user')->add($data);
			if($ure_re){
				if($sonelist['pid'] != '' || $sonelist['pid'] != 0){
					M('user')->where(array('userid'=>$sonelist['userid']))->setInc('u_ztnum',1);//增加会员直推数
				}
				$re_data['status'] = 1;
				$re_data['message'] = "注册成功!";																			
				ajaxReturn($re_data);exit;
			}else{
				$re_data['status'] = 1;
				$re_data['message'] = "网络错误";
                ajaxReturn("注册成功!",'1',$re_data);exit;
			}	
		}
	}

    public function dologin()
    {
        if ($this->request->isPost() || 1)
        {
            $account = $this->request->get('account');
            $password = $this->request->get('password');
            $IndexLogic = new \app\index\logic\IndexLogic();
            // 验证用户名密码是否正确
            $res = $IndexLogic->login($account, $password);
            if ($res['code'] == CodeEnum::ERROR)
            {
                ajaxReturn($res['msg'],0);
            }
            ajaxReturn('登录成功',1,'', U('User/index'));
        }
    }

    /**
     * 注销
     * 
     */
    public function logout()
    {
        ajaxReturn('登录成功',1, U('User/index'));
    }

    /**
     * 图片验证码生成，用于登录和注册
     * 
     */
    public function verify()
    {
        set_verify();
    }

    public function restpassword(){
     /*   if(!IS_AJAX)
            return ;

        $mobile=I('post.mobile');
        $code=I('post.code');
        $password=I('post.password');
        $reppassword=I('post.passwordmin');
        if(empty($mobile)){
            ajaxReturn('手机号码不能为空');
        }
        if(empty($code)){
            ajaxReturn('验证码不能为空');
        }
        if(empty($password)){
            ajaxReturn('密码不能为空');
        }
        if($password  != $reppassword){
            ajaxReturn('两次输入的密码不一致');
        }

        if(!check_sms($code,$mobile)){
            ajaxReturn('验证码错误或已过期');
        }

        $user=D('User');
        $mwhere['mobile']=$mobile;
        $userid=$user->where($mwhere)->getField('userid');
        if(empty($userid)){
            ajaxReturn('手机号码错误或不在系统中');
        }

        $where['userid']=$userid;
        //密码加密
        $salt=user_salt();
        $data['login_pwd']=$user->pwdMd5($password,$salt);
        $data['login_salt']=$salt;
        $res=$user->field('login_pwd,login_salt')->where($where)->save($data);
        if($res){
            session('sms_code',null);
            ajaxReturn('修改成功',1,U('Login/logout'));
        }
        else{
            ajaxReturn('修改失败');
        }
        */
    }




}
