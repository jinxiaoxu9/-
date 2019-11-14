<?php

namespace app\index\controller;

use app\index\logic\WithdrawLogic;
use think\Controller;
use think\db;
use think\Request;

/**
 * 提现控制器
 * Class WithdrawController
 * @package app\index\Controller
 */
class WithdrawController extends CommonController
{
    /**
     * 申请提现
     */
    public function add()
    {
        $params = $this->request->post('');
        //基本参数校验
        $checkParams = $this->validParams($params, ['bankcard_id','price']);
        if ($checkParams['status'] != 1) {
            ajaxReturn($checkParams['message'], $checkParams['status']);
        }
        $params['uid'] = $this->user_id;
        unset($params['token']);
        $ret = $this->logicWithdraw->add($params);
        ajaxReturn($ret['message'], $ret['status']);
    }

    /**
     * 提现记录
     */
    public function withdrawList()
    {
        $WithdrawLogic = new WithdrawLogic();
        $lists = $WithdrawLogic->getLists($this->user_id);
        $data['withdraw_list'] = $lists;
        ajaxReturn('成功',1,'', $data);
    }

}