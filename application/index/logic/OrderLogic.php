<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\GemapayOrderModel;
use app\index\model\UserInviteSettingModel;
use app\index\model\UserModel;
use think\Db;

class OrderLogic
{
    public function getList($userId, $status)
    {
        $GemapayOrderModel = new GemapayOrderModel();
        if($status != -1)
        {
            $where['status'] = $status;
        }
        $where['gema_userid'] = $userId;
        $lists =  $GemapayOrderModel->getList($where ,'*','add_time desc' ,10);

        if (!empty($lists))
        {
            $statusMs = ['未支付', '已支付', '已关闭'];
            foreach ($lists as $k => $V) {
                $lists[$k]['status'] = $statusMs[$V['status']];
            }
        }
        return $lists;
    }

    public function uplaodCredentials($userId, $orderId, $credentials)
    {
        $GemapayOrderModel = new GemapayOrderModel();
        if (empty($orderId))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '参数错误'];
        }
        $orderInfo = $GemapayOrderModel->where(['id' => $orderId, 'gema_userid' => $userId])->find();
        //再校验一下
        if ($orderInfo['status'] == 1 && $orderInfo['credentials'] == 0)
        {
            $update['credentials'] = $credentials;
            $update['is_upload_credentials'] = 1;
            $ret = Db::name('gemapay_order')->where(['id' => $orderId])->update($update);
            if ($ret !== false) {
                return ['code' => CodeEnum::SUCCESS, 'msg' => '操作成功'];
            }
        }
        return ['code' => CodeEnum::ERROR, 'msg' => '操作失败'];
    }
}