<?php


namespace app\admin\model;

use think\Model;

class RechargeModel extends Model
{
    protected $pk = 'id';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'ysk_recharge';

    //充值中
    const STATUS_ING = 0;

    //已经关闭
    const STATUS_CLOSE = 2;

    //充值完成
    const STATUS_OK = 2;

    //充值失败
    const STATUS_FAILED = 3;
}