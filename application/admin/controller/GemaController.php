<?php

namespace app\admin\controller;

use app\admin\model\GemaPayOrderModel;
use app\admin\logic\AdminLogic;
use app\admin\logic\UserLogic;
use app\common\logic\GemapayOrderLogic;
use app\common\Library\enum\CodeEnum;
use think\Request;
use think\Db;


class GemaController extends AdminController
{
    public function index(Request $request)
    {
         $status = $request->param('status', -1,'intval'); //状态

         return $this->common($status);
    }

    //已经完成但是未返款订单
    public function unbackOrder()
    {
        $status = GemaPayOrderModel::PAYED;
        $isUploadCredentials = GemaPayOrderModel::STATUS_NO;
        $isBack = GemaPayOrderModel::STATUS_NO;
        return $this->common($status, $isUploadCredentials, $isBack);
    }

    //已经返款订单但是没有审核
    public function alreadyBackOrder()
    {
        $status = GemaPayOrderModel::PAYED;
        $isUploadCredentials = GemaPayOrderModel::STATUS_YES;
        $isBack = GemaPayOrderModel::STATUS_NO;
        return $this->common($status, $isUploadCredentials, $isBack);
    }

    /**
     *已返款订单并且已审核
     */
    public function  hadBackOrder()
    {
        $status = GemaPayOrderModel::PAYED;
        $isBack = GemaPayOrderModel::STATUS_YES;
        return $this->common($status, false, $isBack);
    }

    public function common($status = -1, $isUploadCredentials = false, $isBack = false)
    {
        $request = Request::instance();
        $order_no = trim($request->param('order_no'));

        $map = [];
        $order_no && $map['order_no'] = ['like', '%' . $order_no . '%'];

        $adminLogic = new AdminLogic();

        $map    = array();
        if(session('user_auth.uid')!=1) {
            $a_uid = $adminLogic->tzUsers();
            if(is_array($a_uid) && $a_uid) {
                $map['gema_userid'] = ['in', $a_uid];
            }
        }

        //检测是否是团长
        $_map = array();
        $adminId = session('user_auth.uid');
        if($adminId) {
            if($adminLogic->checkIstz($adminId)) {
                $_map['admin_id'] = $adminId;
            }

        }

        $groupId = $request->param('group_id', -1, 'intval');
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
        $startTime = $request->param('start_time');//, date("Y-m-d 00:00:00", time())
        $endTime = $request->param('end_time');
        if ($startTime && empty($endTime)) {
            $map['add_time'] = ['egt', strtotime($startTime)];
        }
        if (empty($startTime) && $endTime) {
            $map['add_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $map['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }
        //print_r($map);exit();
        $this->assign('groupId', $groupId);
        $fileds = [
            "o.*",
            "admin.nickname as adminnickname",
            "u.mobile",
            "u.account",
        ];

        $listData = Db::name('gemapay_order')->alias('o')->field($fileds)
            ->join("ysk_user u", "o.gema_userid=u.userid", "left")
            ->join("ysk_admin admin", "admin.id=u.add_admin_id", "left")
            ->where($map)->order('id desc')->paginate(10);

        //当前条件下订单总金额以及总提成
        $totalOrderPrice = Db::name('gemapay_order')->alias('o')
            ->join("ysk_user u", "o.gema_userid=u.userid", "left")->where($map)->sum('order_price');//订单
        $totalTc = Db::name('gemapay_order')->alias('o')
            ->join("ysk_user u", "o.gema_userid=u.userid", "left")->where($map)->sum('bonus_fee');//提成
        //用户组别
        $groups = Db::name('user_group')->where($_map)->field('id,parentid,name')->select();

        $list = $listData->items();
        $count = $listData->count();
        $page = $listData->render();

        foreach ($list as $key => $vals) {
            $list[$key]['s_type_name'] = Db::name('gemapay_code_type')->where(['id'=>$vals['code_type']])->value('type_name');
        }
        $userLogic = new UserLogic();
        $a_groups = $userLogic->getCategory($groups);

        $this->assign('groups', $a_groups);
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集

        $this->assign('totalOrderPrice', $totalOrderPrice);
        $this->assign('totalTc', $totalTc);
        // todo 重置分页参数 弊端后面子再改
        //($p->parameter['group_id']==-1) && $p->parameter['group_id']='';
        $groupId = $request->param('groupId');
        $this->assign('groupId', $groupId);

        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch('index');
    }

    public function sureBack(Request $request)
    {
        $id = trim($request->param('id'));

        $where['id'] = $id;
        $data['is_back'] = 1;
        $GemapayOrderModel = new GemaPayOrderModel();
        Db::startTrans();
        $order = $GemapayOrderModel->where($where)->find();
        if ($order['status'] != GemaPayOrderModel::PAYED && $order["is_back"] != GemaPayOrderModel::STATUS_NO) {
            $this->error('!失败');

        }

        $GemapayOrderModel->startTrans();
        $re = $GemapayOrderModel->where($where)->update($data);
        if (!$re) {
            Db::rollback();
            $this->error('失败!');
        }
        $userLogic = new UserLogic();
        $message = "确认返还,添加余额";
        if (false == $userLogic->accountLog($order['gema_userid'], 7, 1, $order['order_price'], $message)) {
            Db::rollback();
            $this->error('!失败!');
        }
        Db::commit();
        $this->success('成功', url('Gema/index'));

    }

    public function issueOrder(Request $request)
    {
        $id = trim($request->param('id'));
        $GemapayOrderLogic = new GemapayOrderLogic();
        $admin_id = session('user_auth.uid');
        $ret = $GemapayOrderLogic->setOrderSucessByAdmin($id, $admin_id);
        if($ret['code'] != CodeEnum::SUCCESS)
        {
            $this->error($ret['msg']);die();
        }
        $this->success('成功', url('Gema/index'));die();

    }
}
