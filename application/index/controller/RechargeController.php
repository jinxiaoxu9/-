<?php
namespace app\index\Controller;

use think\Request;
use think\db;


class RechargeController extends CommonController {

	//充值记录管理
    public function chongzhijilu(){
		$uid = $this->user_id;

		$relist = Db::name('recharge')->where(array('uid'=>$uid))->order('id desc')->select();
		
	    $this->assign('relist',$relist);
        return $this->fetch();
    }
	
	//充值方式
	public function chongzhi(){
		
		return $this->fetch();
	}
	
	//充值方式
	public function yhkcz(){
		
		$conf = Db::name('system')->where(array('id'=>1))->find();
		$this->assign('conf',$conf);
		return $this->fetch();
	}
	
	//支付宝充值页面
	public function zfbcz(){
		
		return $this->fetch();
	}

	//充值处理私有方法
	private function rc_up($type,$uid,$arr=''){
		
			$sava['account'] = trim($arr['account']);
			$sava['name'] = trim($arr['uname']);
			$sava['price'] = trim($arr['price']);
			$sava['marker'] = trim($arr['marker']);
			if($type ==1){
				$sava['way'] = 1;//支付宝
			}elseif($type ==2){
				$sava['way'] = 2;//微信
			}elseif($type ==3){
				$sava['way'] = 3;//银行卡
			}
			
			$sava['addtime'] = time();
			$sava['status'] = 1;//充值状态
			$sava['uid'] = $uid;
			$re = M('recharge')->add($sava);
			if($re){
				return 1;
			}else{
				return 2;
			}
	}
	
	//微信充值页面
	public function recharge_wx(){
		
		return $this->fetch();
	}

    public function alipay_rc(Request $request){
        if($request->isPost()){
            $uid = $this->user_id;
//			$rlist = M('user')->where(array('account'=>$account))->find();
//			if(empty($rlist)){
//				$data['status'] = 0;
//				$data['msg'] = '该会员不存在';
//				ajaxReturn($data);exit;
//			}
            $type = 1;
            $arr = $request->post();
            $arr['account'] = session('user_login.mobile');//充值账号改为手机
            $st = $this->rc_up($type,$uid,$arr);
            if($st ==1){
                $data['status'] = 1;
                $data['msg'] = '充值提交成功';
                ajaxReturn($data);exit;
            }else{
                $data['status'] = 0;
                $data['msg'] = '充值提交失败';
                ajaxReturn($data);exit;
            }


        }else{
            $data['status'] = 0;
            $data['msg'] = '非法操作';
            ajaxReturn($data);exit;
        }
    }
	//从微信充值提交
	public function wx_rc(Request $request){
		if($request->post()){
			$uid = $this->user_id;
//			$rlist = M('user')->where(array('account'=>$account))->find();
//			if(empty($rlist)){
//				$data['status'] = 0;
//				$data['msg'] = '该会员不存在';
//				ajaxReturn($data);exit;
//			}
			$type = 2;
			$arr = $request->post();
            $arr['account'] = session('user_login.mobile');//充值账号改为手机
			$st = $this->rc_up($type,$uid,$arr);
			if($st ==1){
				$data['status'] = 1;
				$data['msg'] = '充值提交成功';
				ajaxReturn($data);exit;
			}else{
				$data['status'] = 0;
				$data['msg'] = '充值提交失败';
				ajaxReturn($data);exit;
			}
			
			
		}else{
			$data['status'] = 0;
			$data['msg'] = '非法操作';
			ajaxReturn($data);exit;
		}
	}

    //从银行卡充值提交
    public function bank_rc(Request $request){
        if($_POST){
            $uid = $this->user_id;
            $type = 3;
            $arr = $request->post();
            $arr['account'] = session('user_login.mobile');//充值账号改为手机
            $st = $this->rc_up($type,$uid,$arr);
            if($st ==1){
                $data['status'] = 1;
                $data['msg'] = '充值提交成功';
                ajaxReturn($data);exit;
            }else{
                $data['status'] = 0;
                $data['msg'] = '充值提交失败';
                ajaxReturn($data);exit;
            }
        }else{
            $data['status'] = 0;
            $data['msg'] = '非法操作';
            ajaxReturn($data);exit;
        }
    }

	
	
	
	
	//银行卡充值页面
	public function recharge_bank(){
		
		return $this->fetch();
	}
	

	

}