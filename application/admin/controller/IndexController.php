<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\GemaPayOrderModel;
use app\admin\model\UserModel;
use app\admin\model\RechargeModel;
use app\admin\model\WithdrawModel;
use app\admin\model\AdminModel;
use think\Request;

class IndexController extends AdminController
{

    public function index()
    {
        $AdminLogic = new \app\admin\logic\AdminLogic();
        $tzUserID = $AdminLogic->tzUsers();

        $this->todaySuccessOrderInfo($tzUserID);
        $this->todayAddUsersCount($tzUserID);
        $this->UsersCount($tzUserID);
        $this->totalCharge($tzUserID);
        $this->totalTx($tzUserID);
        $this->totalOrderPrice($tzUserID);
        $this->totalBonusFee($tzUserID);
        $this->orderDealCount($tzUserID);
        $this->orderDealMoneys($tzUserID);
        $this->orderUnpayCount($tzUserID);
        $this->orderUnpayMoneys($tzUserID);
        $this->assign('meta_title', "首页");
        return $this->fetch();
    }

    /**
     * 今日成功金额
     * @param object|string $tzUserID 用户
     * @throws \think\exception\DbException
     */
    public function todaySuccessOrderInfo($tzUserID){
        $startTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $map['add_time'] = ['between', [$startTime, $endTime]];
        $map['status'] =1;
        $adminId = $this->admin_id;
        $AdminLogic = new \app\admin\logic\AdminLogic();
        //如果是团长就统计团长的会员  和组无关  可能是团长的会员单没有入团长的组
        $AdminLogic->checkIstz() &&  $map['gema_userid'] = ['in', $tzUserID];

        $gemapayOrder = new GemaPayOrderModel();
        $success= $gemapayOrder->field('sum(order_price) success_price,count(id) as success_nums')
            ->where($map)->find();
        $this->assign('todaySuccessOrderInfo',$success);
    }


    /** 今日新增会员数目
     * @param object|string $tzUserID  用户
     */
    public function todayAddUsersCount($tzUserID)
    {
        $startTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $where['userid'] = ['in', $tzUserID];
        $where['reg_date'] = ['between', [$startTime, $endTime]];
        $user = new UserModel();
        $ret = $user->where($where)->count();
        $this->assign('todayAddUsersCount',$ret);
    }

    /** 全网总会员数
     * @param object|string $tzUserID 用户
     */
    public function UsersCount($tzUserID)
    {
        $ret =$tzUserID ? count($tzUserID) : 0;
        $this->assign('UsersCount',$ret);
    }


    /**总充值金额
     * @param object|string $tzUserID 用户
     */
    public function totalCharge($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $recharge = new RechargeModel();
        $ret = $recharge->where($where)->sum('price');
        $this->assign('totalCharge',$ret);
    }


    /**
     * 总提现金额
     * @param object|string $tzUserID 用户
     */
    public function totalTx($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $Withdraw = new WithdrawModel();
        $ret = $Withdraw->where($where)->sum('price');
        $this->assign('totalTx',$ret);
    }

    /**
     * 网总余额
     * @param object|string $tzUserID 用户
     */
    public function totalOrderPrice($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $gemapayOrder = new GemaPayOrderModel();
        $ret = $gemapayOrder->where($where)->sum('order_price');
        $this->assign('totalOrderPrice',$ret);
    }

    /**
     * 全网总佣金
     * @param object|string $tzUserID 用户
     */
    public function totalBonusFee($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $gemapayOrder = new GemaPayOrderModel();
        $ret = $gemapayOrder->where($where)->sum('bonus_fee');
        $this->assign('totalBonusFee',$ret);
    }


    /**
     * 已完成订单量：
     * @param object|string $tzUserID 用户
     */
    public function orderDealCount($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $where['status'] =1;
        $gemapayOrder = new GemaPayOrderModel();
        $ret = $gemapayOrder->where($where)->count();
        $this->assign('orderDealCount',$ret);
    }


    /**
     * 已完成订单金额
     * @param object|string $tzUserID 用户
     */
    public function orderDealMoneys($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $where['status'] =1;
        $gemapayOrder = new GemaPayOrderModel();
        $ret = $gemapayOrder->where($where)->sum('order_price');
        $this->assign('orderDealMoneys',$ret);

    }


    /**
     * 已匹配订单量：
     * @param object|string $tzUserID 用户
     */
    public function orderUnpayCount($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $where['status'] =0;
        $gemapayOrder = new GemaPayOrderModel();
        $ret = $gemapayOrder->where($where)->count();
        $this->assign('orderUnpayCount',$ret);
    }


    /**
     * 已匹配订单金额
     * @param object|string $tzUserID 用户
     */
    public function orderUnpayMoneys($tzUserID)
    {
        $where['id'] = ['in', $tzUserID];
        $where['status'] =0;
        $gemapayOrder = new GemaPayOrderModel();
        $ret = $gemapayOrder->where($where)->sum('order_price');
        $this->assign('orderUnpayMoneys',$ret);
    }




    /***********************************end***************************************************/


    //获取会员数据统计
    public function getUserCount()
    {
        $user = new UserModel();

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
        $recharge = new RechargeModel();
        $withdraw = new WithdrawModel();
        $resum = $recharge->sum('price');
        $wisum = $withdraw->sum('price');
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

       /* 上面没有定义变量注释  2019-11-05
       $sumyj = M('somebill')->where(array('jl_class' => 1))->sum('num');

        $this->assign('sumyj', $sumyj);
        $this->assign('finishorder_count', $finishorder_count);
        $this->assign('finishorder_money', $finishorder_money);
        $this->assign('dd_ordern_admin', $dd_ordern_admin);
        $this->assign('dd_orderm_admin', $dd_orderm_admin);
        $this->assign('sucorder_money', $sucorder_money);
        $this->assign('sucorder_count', $sucorder_count);
        $this->assign('nollorder_count', $nollorder_count);*/


    }


    /**
     * 删除缓存
     */
    public function removeRuntime()
    {
        /*$file = new \Util\File();
        $result = $file->del_dir(RUNTIME_PATH);*/
        $start = time();//页面打开时间
        function delFile($dirname,$fileType){
            $dir=opendir($dirname);
            while (($file=readdir($dir))!=false){
                $filename=$dirname. DS .$file;
                if($file!=".." && $file!="." && $file!=".gitignore"){
                    if(is_dir($filename)){
                        delFile($filename,$fileType);
                    }
                    if(is_file($filename) && in_array(strrchr($file,'.'),$fileType)){
                        @unlink($filename);
                    }
                }
            }
        }
        delFile(ROOT_PATH . 'runtime',array(".php",".log")); //目录,文件类型
        $diff = time() - $start;
        return $this->success("缓存清理成功,用时{$diff}秒");

    }


    /**
     * 修改登录密码
     */
    public function editPassword(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            if (empty($data['old_password']) || empty($data['password']) || empty($data['repassword'])) {
                $this->error('参数不能为空');
            }
            if ($data['password'] != $data['repassword']) {
                $this->error('两次密码不一致');
            }
            $adminId = session('user_auth.uid');
            $admin = new AdminModel();
            $adminInfo = $admin->find($adminId);

            if (user_md5($data['old_password'], null) != $adminInfo['password']) {
                $this->error('原密码错误');
            }

            //修改密码
            $newpassword = user_md5($data['password'], null);
            $ret = $admin->where(['id' => $adminId])->setField('password', $newpassword);
            if ($ret !== false) {
                session('user_auth', null);
                $this->success('修改成功', url('pubss/login'));
            }
            $this->error('修改失败');
        }

        return $this->fetch();
    }
}