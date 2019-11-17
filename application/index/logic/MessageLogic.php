<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\GemapayOrderModel;
use app\index\model\UserInviteSettingModel;
use app\index\model\UserMessageModel;
use app\index\model\UserModel;
use think\Db;

class MessageLogic
{
    /**
     * 获取消息列表
     * @param $userId
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($userId)
    {
        $UserMessageModel = new UserMessageModel();

        $where['user_id'] = $userId;
        $lists =  $UserMessageModel->getList($where ,'*','add_time desc' ,10);

        return $lists;
    }

    /**
     * 删除消息
     * @param $userId
     * @param $messageId
     * @return array
     */
    public function delMessage($userId, $messageId)
    {
        $UserMessageModel = new UserMessageModel();
        if (empty($messageId))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '参数错误'];
        }

        $ret = $UserMessageModel->where(['id' => $messageId, 'user_id' => $userId])->delete();

        //再校验一下
        if ($ret == false)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '操作失败'];
        }
        return ['code' => CodeEnum::SUCCESS, 'msg' => '操作成功'];
    }

    /**
     * 阅读消息
     * @param $userId
     * @param $messageId
     * @return array
     */
    public function readMessage($userId, $messageId)
    {
        $UserMessageModel = new UserMessageModel();
        if (empty($messageId))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '参数错误'];
        }
        $where['id'] = $messageId;
        $where['user_id'] = $userId;

        $data['is_read'] = $UserMessageModel::STATUS_YES;
        $data['read_time'] = request()->time();
        $UserMessageModel->isUpdate(true, $where)->save($data);
        $info = $UserMessageModel->where(['id' => $messageId, 'user_id' => $userId])->find();
        return $info;
    }

    /**
     * 阅读所有消息
     * @param $userId
     * @param $messageId
     * @return array
     */
    public function readAllMessage($userId)
    {
        $UserMessageModel = new UserMessageModel();
        $where['user_id'] = $userId;
        $data['is_read'] = $UserMessageModel::STATUS_YES;
        $ret = $UserMessageModel->isUpdate(true, $where)->save($data);

        //再校验一下
        if ($ret == false)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '操作失败'];
        }
        return ['code' => CodeEnum::SUCCESS, 'msg' => '操作成功'];
    }
}