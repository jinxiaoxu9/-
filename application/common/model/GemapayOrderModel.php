<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:38
 */
namespace app\common\model;

use think\Model;

class GemapayOrderModel extends Model
{
    const WAITEPAY = 0;
    const PAYED = 1;
    const CLOSED = 2;

    const EXPREIDTIME = 30*60;
    /**
     * 增加订单
     * @param int $userid
     * @param float $money
     * @param string $orderNum
     * @param int $codeId
     * @param float $payMoney
     * @return int|string
     */
    function addGemaPayOrder($userid, $money, $orderNum, $codeId, $payMoney, $qrCode, $gemaUsername, $type, $tradeNo)
    {
        $data = [
            'add_time' => time(),
            'status' => self::WAITEPAY,
            'gema_userid' => $userid,
            'order_price' => $money,
            'order_no' => $orderNum,
            'code_id' => $codeId,
            'order_pay_price' => $payMoney,
            'qr_image' => $qrCode,
            'gema_username' => $gemaUsername,
            'code_type' => $type,
            'out_trade_no' => $tradeNo,
        ];
        return $this->insert($data, false, true);
    }

    /**
     * 设置订单成功
     * @param $orderId
     * @param string $note
     * @param float $fee
     * @return GemapayOrderModel
     */
    public function setOrderSucess($orderId, $note = "", $fee = 0.00)
    {
        $where['id'] = $orderId;
        $data['bonus_fee'] = $fee;
        $data['status'] = self::PAYED;
        $data['pay_time'] = time();
        $data['note'] = $note;
        $data['sure_ip'] = get_userip();
        return $this->where($where)->update($data);
    }

}