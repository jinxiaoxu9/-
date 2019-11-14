<?php

namespace app\index\logic;

use app\index\model\RechargeModel;

class DepositLogic
{
    public function getList($userId)
    {
        $RechargeModel = new RechargeModel();
        $where['uid'] = $userId;
        $lists =  $RechargeModel->getList($where ,'*','addtime desc' ,10);
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

        $data['account_name'] = $bankAccount;
        $data['account_num'] = $bankNumber;
        $data['bank_name'] = $bankName;
        $res  = $RechargeModel->save($data);
        if($res)
        {
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '成功！'];
        }
        else
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }

    public function getDepositBankInfo()
    {
        $AdminBankModel = new \app\index\model\AdminBankModel();
        $where['admin_id'] = 1;
        $filelds= [
            "account_name",
            "account_num",
            "bank_name"
        ];
        $bankInfo = $AdminBankModel->field($filelds)->where($where)->find();
        return $bankInfo;
    }

}