<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;
use app\index\logic\BankCardLogic;


/**
 * 银行卡控制器
 * Class BankcardController
 * @package app\index\controller
 */
class BankCardController extends CommonController
{
    /**
     * 添加提现银行卡
     */
    public function add()
    {
        $params = $this->request->post('');
        //基本参数校验
        $checkParams = $this->validParams($params, ['name', 'bankname', 'banknum']);
        if ($checkParams['status'] != 1) {
            ajaxReturn($checkParams['message'], $checkParams['status']);
        }
        $params['uid'] = $this->user_id;
        $BankCardLogic = new BankCardLogic();
        $ret = $BankCardLogic->add($params);
        ajaxReturn($ret['message'], $ret['code']);
    }

    /**
     * 银行卡列表
     */
    public function index()
    {
        $list = $this->modelBankcard->getBankCards(['uid' => $this->user_id]);
        $data['bank_list'] = $list;
        ajaxReturn('success', CodeEnum::SUCCESS, '', $data);
    }

    /**
     * 删除银行卡
     */
    public function delBank()
    {
        $bankId = $this->request->post('bank_id');
        $BankCardLogic = new BankCardLogic();
        $res = $BankCardLogic->delBank($this->user_id, $bankId);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }
}