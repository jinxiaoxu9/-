<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;
use app\index\model\SomebillModel;

/**
 * 用户资产控制器
 * Class Belongs
 * @package app\index\controller
 */
class BillController extends CommonController
{
    /**
     * 用户资金账变记录
     */
    public function changeLog()
    {
           $where['uid'] = $this->user_id;
           $SomebillModel = new SomebillModel();
           $list = $SomebillModel->getLists($where,'*','addtime desc',10);
           $data['list'] = $list;
           ajaxReturn('success',CodeEnum::SUCCESS,'',$data);
    }

    /**
     * bill tpyes
     */
    public function getBillTypes()
    {
        $list = \app\common\library\enum\MoneyOrderTypes::getMoneyOrderTypes();
        $data['type_list'] = $list;
        ajaxReturn('success',CodeEnum::SUCCESS,'',$data);
    }

}