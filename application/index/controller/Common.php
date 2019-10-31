<?php

namespace app\index\controller;;

use app\index\model\User;
use think\Controller;

class Common extends Controller {
    protected  $user_id;
    protected  $request;
	public function __construct(){
        //验证用户登录
        parent::__construct();
        $this->is_user();
        $this->request = request();
    }

    protected function is_user(){
        $UserModel =  new User();
        $this->user_id = session('user_id');
        if(!$this->user_id)
        {
            $this->redirect('/Login/login');
            exit();
        }

        $where['userid']=$this->user_id;
        $u_info=$UserModel->where($where)->field('status,session_id')->find();
        //判断用户是否在他处已登录
        $session_id=session_id();
        if($session_id != $u_info['session_id']){
           if(IS_AJAX){
                ajaxReturn('您的账号在他处登录，您被迫下线',0);
            }else{
                success_alert('您的账号在他处登录，您被迫下线',U('Login/logout'));
                exit();
            }
        }
    }


}

