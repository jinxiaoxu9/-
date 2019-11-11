<?php

namespace app\index\logic;

use Common\Library\enum\CodeEnum;
use app\index\model\ConfigModel;
use app\index\model\User;

class UserLogic
{
    public function getIndexInfo($userId)
    {
        $UserModel = new User();
        $ConfigModel = new ConfigModel();

        $userInfo = $UserModel->where(array('userid' => $userId))->find();
        $conf = $ConfigModel->field('value')->where(['name' => 'USER_NAV'])->find();

        $data['config'] = json_decode($conf['value'], true);
        $data['userinfo'] = $userInfo;
        return $data;
    }
}