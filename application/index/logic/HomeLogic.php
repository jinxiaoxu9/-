<?php

namespace app\index\logic;

use Common\Library\enum\CodeEnum;
use app\index\model\ConfigModel;
use app\index\model\User;

class HomeLogic
{
    public function getUserWorkInfo($userId)
    {
        $UserModel = new \app\index\model\UserModel();
        $where['userid'] = $userId;
        $userInfo = $UserModel->find($where);

    }

    /**
     * 开始工作
     * @param $userId
     * @return array
     */
    public function startWork($userId)
    {
        $UserModel = new \app\index\model\UserModel();

        $where['userid'] = $userId;

        $data['work_status'] = $UserModel::STATUS_YES;
        $ret = $UserModel->isUpdate(true, $where)->save($data);
        if($ret)
        {
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '开工成功！'];
        }
        else
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }

    /**
     * 停止工作
     * @param $userId
     * @return array
     */
    public function stopWork($userId)
    {
        $UserModel = new \app\index\model\UserModel();

        $where['userid'] = $userId;

        $data['work_status'] = $UserModel::STATUS_NO;
        $ret = $UserModel->isUpdate(true, $where)->save($data);
        if($ret)
        {
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '停工成功！'];
        }
        else
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }
}