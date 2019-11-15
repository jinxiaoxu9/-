<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 8/28/19
 * Time: 10:32 PM
 */

namespace app\common\library\enum;


class MoneyOrderTypes
{
    //操作方式,添加
    const OP_ADD = 1;

    //操作方式,减少
    const OP_SUB = 0;

    //充值
    const DEPOSIT = 1;

    //提现
    const WITHDRAW = 2;

    //抢单成功,押金
    const ORDER_DEPOSIT = 3;

    //关闭订单,押金返回
    const ORDER_DEPOSIT_BACK = 4;

    //订单完成,添加利润
    const ORDER_BONUS = 5;

    //后台强制完成订单
    const ORDER_FORCE_FINISH = 6;

    public static function getMoneyOrderTypes()
    {
        return [
            self::DEPOSIT => "充值",
            self::WITHDRAW => "提现",
            self::ORDER_DEPOSIT => "抢单成功,押金",
            self::ORDER_DEPOSIT_BACK => "关闭订单,押金返回",
            self::ORDER_BONUS => "订单完成,添加利润",
            self::ORDER_FORCE_FINISH => "后台强制完成订单",
        ];
    }

}