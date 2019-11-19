<?php

namespace app\index\controller;

use app\common\library\enum\CodeEnum;
use app\index\logic\HomeLogic;

class HomeController extends CommonController
{
    /**
     * 抢单页面信息显示
     */
    public function index()
    {
        $HomeLogic = new HomeLogic();
        $info = $HomeLogic->getUserWorkInfo($this->user_id);
        $data['info'] = $info;

        ajaxReturn('操作成功', 1, '', $data);
    }

    /**
     * 前端每10s  请求接口返回的数据
     */
    public function queen()
    {

        $HomeLogic = new HomeLogic();
        //获取订单排队情况
        $data['qeeen'] = $HomeLogic->getUserOrderQueen();
        //当前用户的最新订单ID
        $gemaOrderModel = $this->modelGemapayOrder;
        $this->user_id = 647;
        $lastOrderInfo = $gemaOrderModel->getLastGemapayOrder(['status' => $gemaOrderModel::PAYED, 'gema_userid' => $this->user_id], 'id', ['id' => 'desc']);
        $data['userLastOrderId'] = $lastOrderInfo['id'];
        //最新十条订单数据
        $lastNewOrderLists = $gemaOrderModel->getOrderList(['status' => $gemaOrderModel::PAYED], 'id,order_no,gema_userid', ['add_time' => 'desc'], 10);
        $lastNewOrderLists = $lastNewOrderLists->toArray();
        $data['lastNewOrderLists '] = empty($lastNewOrderLists) ? [] : $lastNewOrderLists['data'];
        //当前用户是否最新未读取的站内信(inclue the system and the personal)
        $userMessageModel = $this->modelUserMessage;
        $data['unreadMessage'] = $userMessageModel->getUserMessageCount(['is_read' => $userMessageModel::UNREAD, 'user_id' => $this->user_id], 'id');
        ajaxReturn('success',CodeEnum::SUCCESS , '', $data);
    }


    /**
     * 开始工作
     */
    public function startWork()
    {
        $HomeLogic = new HomeLogic();
        $res = $HomeLogic->startWork($this->user_id);

        if ($res['code'] == CodeEnum::ERROR) {
            ajaxReturn($res['msg'], 0);
        }
        ajaxReturn('操作成功', 1, '');
    }

    /**
     * 停止工作
     */
    public function stopWork()
    {
        $HomeLogic = new HomeLogic();
        $res = $HomeLogic->stopWork($this->user_id);
        if ($res['code'] == CodeEnum::ERROR) {
            ajaxReturn($res['msg'], 0);
        }
        ajaxReturn('操作成功', 1, '');

    }
}