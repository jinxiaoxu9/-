<?php

namespace Admin\Controller;

use Gemapay\Model\GemapayOrderModel;
use Gemapay\Logic\GemapayOrderLogic;
use Common\Library\enum\CodeEnum;

class Gema extends Admin
{
    public function index()
    {
        $status = I('status', -1,'intval'); //状态
         $this->common($status);
    }

    //已经完成但是未返款订单
    public function unbackOrder()
    {
        $status = GemapayOrderModel::PAYED;
        $isUploadCredentials = GemapayOrderModel::STATUS_NO;
        $isBack = GemapayOrderModel::STATUS_NO;
        $this->common($status, $isUploadCredentials, $isBack);
    }

    //已经返款订单但是没有审核
    public function alreadyBackOrder()
    {
        $status = GemapayOrderModel::PAYED;
        $isUploadCredentials = GemapayOrderModel::STATUS_YES;
        $isBack = GemapayOrderModel::STATUS_NO;
        $this->common($status, $isUploadCredentials, $isBack);
    }

    /**
     *已返款订单并且已审核
     */
    public function  hadBackOrder()
    {
        $status = GemapayOrderModel::PAYED;
        $isBack = GemapayOrderModel::STATUS_YES;
        $this->common($status, false, $isBack);
    }

    public function common($status = -1, $isUploadCredentials = false, $isBack = false)
    {
        $order_no = trim(I('get.order_no'));
        $order = M('gemapay_order');
        $map = [];
        $order_no && $map['order_no'] = ['like', '%' . $order_no . '%'];

        if(session('user_auth.uid')!=1)
        {
            $map['gema_userid'] = ['in', tzUsers()];
        }

        //检测是否是团长
        $adminId = session('user_auth.uid');
        checkIstz($adminId) && $_map['admin_id'] = $adminId;
        $groupId = I('group_id', -1, 'intval');
        $this->assign('groupId', $groupId);
//        ($groupId != -1) && $map['b.group_id'] = $groupId;

        //分组逻辑
        $userGetGroupsObj = new UserController();
        $groups = $userGetGroupsObj::getGroups($groupId);
        if ($groups !== "") {
            $map['group_id'] = array("in", $groups . "");
        }
        //新增其他条件

        ($status != -1) && $map['o.status'] = $status;

        if($isUploadCredentials !== false)
        {
            $map['is_upload_credentials'] = $isUploadCredentials;
        }

        if($isBack !== false)
        {
            $map['is_back'] = $isBack;
        }

        $this->assign('status', $status);
        //时间

        $startTime = I('start_time/s',date("Y-m-d 00:00:00", time()));
        $endTime = I('end_time/s');
        if ($startTime && empty($endTime)) {
            $map['add_time'] = ['egt', strtotime($startTime)];
        }
        if (empty($startTime) && $endTime) {
            $map['add_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $map['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }

        $this->assign('groupId', $groupId);
        $count = $order->alias('o')->where($map)
            ->join("ysk_user u on o.gema_userid=u.userid", "left")
            ->count();
        $p = getpagee($count, 10);
        $fileds = [
            "o.*",
            "admin.nickname as adminnickname",
            "u.mobile",
        ];

        $list = $order->alias('o')->field($fileds)
            ->join("ysk_user u on o.gema_userid=u.userid", "left")
            ->join("ysk_admin admin on u.add_admin_id = admin.id", "left")
            ->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

        //当前条件下订单总金额以及总提成
        $totalOrderPrice = $order->alias('o')
            ->join("ysk_user u on o.gema_userid=u.userid", "left")->where($map)->sum('order_price');//订单
        $totalTc = $order->alias('o')
            ->join("ysk_user u on o.gema_userid=u.userid", "left")->where($map)->sum('bonus_fee');//提成
        //用户组别
        $adminId = session('user_auth.uid');
        checkIstz($adminId) && $_map['admin_id'] = $adminId;
        $groups = M('user_group')->where($_map)->field('id,parentid,name')->select();

        $this->assign('groups', getCategory($groups));
        $this->assign('count', $count);
        $this->assign('info', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('totalOrderPrice', $totalOrderPrice);
        $this->assign('totalTc', $totalTc);
        //todo 重置分页参数 弊端后面子再改
        //($p->parameter['group_id']==-1) && $p->parameter['group_id']='';
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display('index');
    }

    public function sureBack()
    {
        $id = trim(I('get.id'));

        $where['id'] = $id;
        $data['is_back'] = 1;
        $GemapayOrderModel = new GemapayOrderModel();
        $order = $GemapayOrderModel->where($where)->find();
        if ($order['status'] != $GemapayOrderModel::PAYED && $order["is_back"] != $GemapayOrderModel::STATUS_NO) {
            $this->error('失败');

        }

        $GemapayOrderModel->startTrans();
        $re = $GemapayOrderModel->where($where)->save($data);
        if (!$re) {
            $GemapayOrderModel->rollback();
            $this->error('失败');
        }

        $message = "确认返还,添加余额";
        if (false == accountLog($order['gema_userid'], 7, 1, $order['order_price'], $message)) {
            $GemapayOrderModel->rollback();
            $this->error('失败');
        }
        $this->success('成功', U('Gema/index'));

    }

    public function issueOrder()
    {
        $id = trim(I('get.id'));
        $GemapayOrderLogic = new GemapayOrderLogic();
        $ret = $GemapayOrderLogic->setOrderSucessByAdmin($id, $this->admin_id);
        if($ret['code'] != CodeEnum::SUCCESS)
        {
            $this->error($ret['msg']);die();
        }
        $this->success('成功', U('Gema/index'));die();

    }
}
