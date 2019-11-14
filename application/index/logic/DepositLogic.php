<?php

namespace app\index\logic;

use Admin\Model\AdminBankModel;
use app\index\model\RechargeModel;

class DepositLogic
{
    public function getList($userId)
    {
        $RechargeModel = new RechargeModel();
        $where['uid'] = $userId;
        $lists =  $RechargeModel->getList($where ,'*','add_time desc' ,10);
        return $lists;
    }

    public function apply($user_id, $bankName, $bankAccount, $bankNumber, $money, $name)
    {
        $RechargeModel = new RechargeModel();
        $data['uid'] = $user_id;
        $data['status'] = $RechargeModel::STATUS_ING;
        $data['price'] = $money;
        $data['name'] = $name;
        $data['addtime'] = request()->time();

    }

    public function getDepositBankInfo()
    {
        $AdminBankModel = new AdminBankModel();
        $where['admin_id'] = 1;
        $bankInfo = $AdminBankModel->where($where)->find();
        return $bankInfo;
    }

}