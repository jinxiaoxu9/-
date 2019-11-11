<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\GemapayOrderModel;
use app\index\model\UserInviteSettingModel;
use app\index\model\UserModel;

class OrderLogic
{
    public function getList($userId)
    {
        $GemapayOrderModel = new GemapayOrderModel();
        return $GemapayOrderModel->getUserCodeOrder($userId);
    }
}