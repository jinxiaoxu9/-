<?php

namespace app\index\Controller;

use app\common\library\enum\CodeEnum;
use app\index\logic\MessageLogic;
use think\Controller;
use think\db;
use think\Request;

/**
 * 提现控制器
 * Class WithdrawController
 * @package app\index\Controller
 */
class MessageController extends CommonController
{
    /**
     * 消息列表
     */
    public function messageList()
    {
        $MessageLogic = new MessageLogic();
        $lists = $MessageLogic->getList($this->user_id);
        $data['message_list'] = $lists;
        ajaxReturn('成功',1,'', $data);
    }

    /**
     * 删除消息
     */
    public function delMessage()
    {
        $messageId = $this->request->post('message_id');
        $MessageLogic = new MessageLogic();
        $res = $MessageLogic->delMessage($this->user_id, $messageId);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }

    /**
     * 阅读信息
     */
    public function readMessage()
    {
        $messageId = $this->request->post('message_id');
        $MessageLogic = new MessageLogic();
        $info = $MessageLogic->readMessage($this->user_id, $messageId);
        $data['info'] = $info;
        ajaxReturn('成功',1,'', $data);
    }

    /**
     * 一键阅读
     */
    public function readAllMessage()
    {
        $MessageLogic = new MessageLogic();
        $res = $MessageLogic->readAllMessage($this->user_id);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }
}