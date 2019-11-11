<?php
namespace app\index\controller;
use app\index\model\UserModel;
use think\Controller;

class CommonController extends Controller {
    protected  $user_id;
    protected  $request;
	public function __construct(){
        //验证用户登录
        parent::__construct();
        $this->is_user();
        $this->request = request();
    }

    protected function is_user(){
        $UserModel =  new UserModel();
        $token = $this->request->post("token");
        $where['token'] = $token;
        $userInfo = $UserModel->where($where)->find();

        if(!$userInfo)
        {
            ajaxReturn('请先登录,您被迫下线',0);
        }
        $this->user_id = $userInfo->userid;
    }
}

