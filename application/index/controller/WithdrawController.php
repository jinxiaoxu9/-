<?php
namespace app\index\Controller;

use think\Controller;
use think\db;
use think\Request;


class WithdrawController extends CommonController {

	//提现记录管理
    public function index(){
		$uid = $this->user_id;
		$welist = Db::name('withdraw')->where(array('uid'=>$uid))->order('id desc')->select();
		$this->assign('welist',$welist);
        return $this->fetch();
    }
	
	//提现页面
	public function tixian(){
		
		return $this->fetch();
	}
	
	//提现处理
	public function drawup(Request $request){
		if($request->isPost()){
			$uid = $this->user_id;
			$ulist = M('user')->where(array('userid'=>$uid))->find();
			/*******这里写提现条件********/
			
			$save['uid'] = $uid;
			$save['account'] = trim($request->param('account'));
			$save['name'] = trim($request->param('uname'));
			$save['way'] = trim($request->param('way'));
			$save['price'] = trim($request->param('price'));
			$save['addtime'] = time();
			$save['status'] = 1;
			if($save['way'] == '微信'){
				if($save['account'] != $ulist['wx_no']){
					$data['status'] = 0;
					$data['msg'] = '请使用绑定的微信账号';
					ajaxReturn($data);exit;
				}
				
			}elseif($save['way'] == '支付宝'){
				if($save['account'] != $ulist['alipay']){
					$data['status'] = 0;
					$data['msg'] = '请使用绑定的支付宝账号';
					ajaxReturn($data);exit;
				}
				
			/*}elseif($save['way'] == '银行卡'){
				
				$data['status'] = 0;
				$data['msg'] = '没有此提现类型';
				ajaxReturn($data);exit;
				
			}else{
				$data['status'] = 0;
				$data['msg'] = '没有此提现类型';
				ajaxReturn($data);exit;*/
			}

			$clist = Db::name('system')->where(array('id'=>1))->find();
			
			if($save['price'] < $clist['mix_withdraw']){
				
				$data['status'] = 0;
				$data['msg'] = '最小提现额度'.$clist['mix_withdraw'].'元';
				ajaxReturn($data);exit;
				
			}
			
			if($save['price'] > $clist['max_withdraw']){
				
				$data['status'] = 0;
				$data['msg'] = '最大提现额度'.$clist['max_withdraw'].'元';
				ajaxReturn($data);exit;
				
			}
			
			
			$pipei_sum_price = Db::name('userrob')->where(array('uid'=>$uid,'status'=>3))->sum('price');
			$rech_sum_price = Db::name('recharge')->where(array('uid'=>$uid,'status'=>3))->sum('price');
			
			$blz = $pipei_sum_price / $rech_sum_price;
			
			$cblz = $clist['tx_yeb'] / 100;
			
			if($blz < $cblz){
				
				$data['status'] = 0;
				$data['msg'] = '您的匹配收款额度不足';
				ajaxReturn($data);exit;
								
			}


			if($save['price'] > $ulist['money']){
				$data['status'] = 0;
				$data['msg'] = '账户余额不足';
				ajaxReturn($data);exit;
			}
			
			$re = Db::name('withdraw')->add($save);
			
			$ure =  Db::name('user')->where(array('userid'=>$uid))->setDec('money',$save['price']);//直接扣除提现金额
			
			if($re && $ure){
				
				$data['status'] = 1;
				$data['msg'] = '提现已提交';
				ajaxReturn($data);exit;
				
			}else{
				
				$data['status'] = 0;
				$data['msg'] = '非法操作';
				ajaxReturn($data);exit;
				
			}
			
			
		}else{
			$data['status'] = 0;
			$data['msg'] = '非法操作';
			ajaxReturn($data);exit;
		}
		
	}


}