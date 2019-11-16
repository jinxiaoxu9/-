<?php

namespace app\index\controller;

use app\common\library\enum\CodeEnum;
use app\index\logic\DepositLogic;

/**
 * 提现控制器
 * Class WithdrawController
 * @package app\index\Controller
 */
class DepositController extends CommonController
{
    /**
     * 申请充值
     */
    public function apply()
    {
        $bankName = $this->request->post("bank_name","");
        $bankAccount = $this->request->post("bank_account","");
        $bankNumber = $this->request->post("bank_number","");
        $money = $this->request->post("money","");
        $name = $this->request->post("name","");

        $DepositLogic = new DepositLogic();
        $res = $DepositLogic->apply($this->user_id, $bankName, $bankAccount, $bankNumber, $money, $name);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }

    /**
     * 获取充值信息,银行卡卡号，开户行，
     */
    public function applyInfo()
    {
        $DepositLogic = new DepositLogic();
        $info = $DepositLogic->getDepositBankInfo();
        $data['info'] = $info;
        ajaxReturn('成功',1,'', $data);
    }

    /**
     * 充值记录
     */
    public function depositList()
    {
        $MessageLogic = new DepositLogic();
        $lists = $MessageLogic->getList($this->user_id);
        $data['deposit_list'] = $lists;
        ajaxReturn('成功',1,'', $data);
    }
}