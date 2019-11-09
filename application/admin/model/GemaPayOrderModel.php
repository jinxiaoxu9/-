<?php
namespace app\admin\model;

use think\Model;

class GemaPayOrderModel extends Model
{
    protected $pk = 'id';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'ysk_gemapay_order';

    const WAITEPAY = 0;
    const PAYED = 1;
    const CLOSED = 2;

    const EXPREIDTIME = 1800;

    //打开中
    const STATUS_ON = 0;

    const STATUS_NO = 0;

    const STATUS_YES = 0;
    //关闭中
    const STATUS_OFF = 1;
    //支付中
    const STATUS_PAYING = 1;
    //空闲中　
    const STATUS_NOPAYING = 0;
    //关闭中
    const STATUS_CLOSE = 2;
}