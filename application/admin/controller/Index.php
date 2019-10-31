<?php

namespace Admin\Controller;

use Think\Controller;

class Index extends Admin
{

    public function index()
    {
        $this->todaySuccessOrderInfo();
        $this->todayAddUsersCount();
        $this->UsersCount();
        $this->totalCharge();
        $this->totalTx();
        $this->totalOrderPrice();
        $this->totalBonusFee();
        $this->orderDealCount();
        $this->orderDealMoneys();
        $this->orderUnpayCount();
        $this->orderUnpayMoneys();
        $this->assign('meta_title', "首页");
        $this->display();
    }

    /**
     * 今日成功金额
     */
    public function todaySuccessOrderInfo(){
        $startTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $map['add_time'] = ['between', [$startTime, $endTime]];
        $map['status'] =1;
        $adminId = session('user_auth.uid');
        //如果是团长就统计团长的会员  和组无关  可能是团长的会员单没有入团长的组
        checkIstz($adminId) &&  $map['gema_userid'] = ['in', tzUsers()];
        $success= M('gemapay_order')->field('sum(order_price) success_price,count(id) as success_nums')
            ->where($map)->find();
        $this->assign('todaySuccessOrderInfo',$success);
    }



    /**
     * 今日新增会员数目
     */
    public function todayAddUsersCount()
    {
        $startTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $where['userid'] = ['in', tzUsers()];
        $where['reg_date'] = ['between', [$startTime, $endTime]];
        $ret = M('user')->where($where)->count();
        $this->assign('todayAddUsersCount',$ret);
    }

    /**
     * 全网总会员数：
     */
    public function UsersCount()
    {
        $ret = count(tzUsers());
        $this->assign('UsersCount',$ret);
    }


    /**
     * 总充值金额：
     */
    public function totalCharge()
    {
        $where['id'] = ['in', tzUsers()];
        $ret = M('recharge')->where($where)->sum('price');
        $this->assign('totalCharge',$ret);
    }


    /**
     * 总提现金额
     */
    public function totalTx()
    {
        $where['id'] = ['in', tzUsers()];
        $ret = M('withdraw')->where($where)->sum('price');
        $this->assign('totalTx',$ret);
    }

    /**
     * 网总余额
     */
    public function totalOrderPrice()
    {
        $where['id'] = ['in', tzUsers()];
        $ret = M('gemapay_order')->where($where)->sum('order_price');
        $this->assign('totalOrderPrice',$ret);
    }

    /**
     * 全网总佣金
     */
    public function totalBonusFee()
    {
        $where['id'] = ['in', tzUsers()];
        $ret = M('gemapay_order')->where($where)->sum('bonus_fee');
        $this->assign('totalBonusFee',$ret);
    }


    /**
     * 已完成订单量：
     */
    public function orderDealCount()
    {
        $where['id'] = ['in', tzUsers()];
        $where['status'] =1;
        $ret = M('gemapay_order')->where($where)->count();
        $this->assign('orderDealCount',$ret);
    }


    /**
     * 已完成订单金额
     */
    public function orderDealMoneys()
    {
        $where['id'] = ['in', tzUsers()];
        $where['status'] =1;
        $ret = M('gemapay_order')->where($where)->sum('order_price');
        $this->assign('orderDealMoneys',$ret);

    }


    /**
     * 已匹配订单量：
     */
    public function orderUnpayCount()
    {
        $where['id'] = ['in', tzUsers()];
        $where['status'] =0;
        $ret = M('gemapay_order')->where($where)->count();
        $this->assign('orderUnpayCount',$ret);
    }


    /**
     * 已匹配订单金额
     */
    public function orderUnpayMoneys()
    {
        $where['id'] = ['in', tzUsers()];
        $where['status'] =0;
        $ret = M('gemapay_order')->where($where)->sum('order_price');
        $this->assign('orderUnpayMoneys',$ret);
    }




    /***********************************end***************************************************/


    //获取会员数据统计
    public function getUserCount()
    {
        $user = D('User');

        $user_total = $user->count();

        $start = strtotime(date('Y-m-d'));

        $end = $start + 86400;

        $where = "reg_date BETWEEN {$start} AND {$end}";

        $user_count = $user->where($where)->count();
        $countmoney = $user->sum('money');
        $this->assign('countmoney', $countmoney);
        $this->assign('user_total', $user_total);

        $this->assign('user_count', $user_count);


    }

    public function getmoneyCount()
    {

        $resum = M('recharge')->sum('price');
        $wisum = M('withdraw')->sum('price');
        $this->assign('wisum', $wisum);
        $this->assign('resum', $resum);
    }

    public function getorderCount()
    {
//		$sucorder_count = M('userrob')->where(array('status'=>2))->count();
//		$nollorder_count = M('userrob')->where(array('status'=>1))->count();
//
//		$finishorder_count = M('userrob')->where(array('status'=>3))->count();
//		$finishorder_money = M('userrob')->where(array('status'=>3))->sum('price');
//
//		$sucorder_money = M('userrob')->where(array('status'=>2))->sum('price');
//		$dd_ordern_admin = M('roborder')->where(array('status'=>1))->sum('price');
//		$dd_orderm_admin = M('roborder')->where(array('status'=>1))->count();

        $sumyj = M('somebill')->where(array('jl_class' => 1))->sum('num');

        $this->assign('sumyj', $sumyj);
        $this->assign('finishorder_count', $finishorder_count);
        $this->assign('finishorder_money', $finishorder_money);
        $this->assign('dd_ordern_admin', $dd_ordern_admin);
        $this->assign('dd_orderm_admin', $dd_orderm_admin);
        $this->assign('sucorder_money', $sucorder_money);
        $this->assign('sucorder_count', $sucorder_count);
        $this->assign('nollorder_count', $nollorder_count);


    }


    /**
     * 删除缓存
     */
    public function removeRuntime()
    {
        $file = new \Util\File();
        $result = $file->del_dir(RUNTIME_PATH);
        if ($result) {
            $this->success("缓存清理成功1");
        } else {
            $this->error("缓存清理失败1");
        }
    }


    /**
     * 修改登录密码
     */
    public function editPassword()
    {
        if ($_POST) {
            $data = I('post.');
            if (empty($data['old_password']) || empty($data['password']) || empty($data['repassword'])) {
                $this->error('参数不能为空');
            }
            if ($data['password'] != $data['repassword']) {
                $this->error('两次密码不一致');
            }
            $adminId = session('user_auth.uid');
            $adminInfo = M('admin')->find($adminId);

            if (user_md5($data['old_password'], null) != $adminInfo['password']) {
                $this->error('原密码错误');
            }

            //修改密码
            $newpassword = user_md5($data['password'], null);
            $ret = M('admin')->where(['id' => $adminId])->setField('password', $newpassword);
            if ($ret !== false) {
                session('user_auth', null);
                $this->success('修改成功', U('Pubss/login'));
            }
            $this->error('修改失败');
        }
        $this->display();
    }
}