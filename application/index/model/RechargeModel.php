<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 银行卡模型
 * Class BankCardModel
 * @package app\index\model
 */
class RechargeModel extends BaseModel
{
    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_recharge';

    //充值中
    const STATUS_ING = 0;

    //已经关闭
    const STATUS_CLOSE = 2;

    //充值完成
    const STATUS_OK = 2;

    //充值失败
    const STATUS_FAILED = 3;
}
