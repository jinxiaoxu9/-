<?php

namespace app\index\logic;

use app\index\model\User;

class HomeLogic
{
    public function getUserWorkInfo($userId)
    {
        $UserModel = new \app\index\model\UserModel();
        $UserGemaCodeLogic =  new CodeLogic();
        $codeTypes = $UserGemaCodeLogic->getcodeTypes($userId);
        $where['userid'] = $userId;
        $userInfo = $UserModel->find($where);

        //
        $data["code_infos"] = $codeTypes;
        //工作状态
        $data["work_status"] = $userInfo['work_status'];
        //用户余额
        $data["money"] = $userInfo['money'];
        //今日完成订单总额
        $data["today_finish_money"] = 10000.0;
        //今日订单分红
        $data["today_bonus"] = 1000.10;
        //今日完成订单数量
        $data["today_finish_order"] = 10;
        //今日总订单数量
        $data["today_total_order"] = 100;
        //未读消息
        $data["unread"] = 3;
        //今日成功率
        $data["today_success_rate"] = sprintf("%.2f", ($data["today_finish_order"]*100)/$data["today_total_order"])."%";

        //不同类型的费率
        return $data;
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
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '你已经开工！'];
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
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '你已经停工！'];
        }
    }
}