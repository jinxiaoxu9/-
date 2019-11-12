<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;


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
        $ret = $this->logicBankCard->add($params);
        ajaxReturn($ret['message'], $ret['status']);
    }

    /**
     * 银行卡列表
     */
    public function index()
    {
        $ret = $this->modelBankcard->getBankCards(['uid' => $this->user_id]);
        ajaxReturn('success', CodeEnum::SUCCESS, '', $ret);
    }




}