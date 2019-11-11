<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:38
 */
namespace app\common\model;

use Think\Model;
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
        return $this->add($data, false, true);
    }

    /**
     * 获取已经使用的订单金额
     * @param int $codeId
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPayingMoneyList($codeId, $money)
    {

        $fileds = [
            'order_pay_price',
        ];

        $where = [
            'code_id' => $codeId,
            'status' => self::WAITEPAY,
            'order_price' => $money
        ];
        return $this->where($where)->field($fileds)->select();
    }

    public function getOrderInfo($orderId)
    {
        return $this->find($orderId);
    }

    public function getList($userId = null)
    {
        $where = [];
        if($userId)
        {
            $where['gema_userid'] = $userId;
        }
        return $this->where($where)->order("id desc")->page($_GET['p'].',25')->select();
    }

    public function getListPage($userId = null)
    {
        $where = [];
        if($userId)
        {
            $where['gema_userid'] = $userId;
        }
        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
        return $Page;
    }

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

    public function getUnpayOrder($orderId)
    {
        $where['id'] = $orderId;
        $where['status'] = self::WAITEPAY;

        return $this->where($where)->find();
    }

    public function getOrderId($userId, $type, $money)
    {
        $where['gema_userid'] = $userId;
        $where['code_type'] = $type;
        $where['order_pay_price'] = $money;

        return $this->where($where)->value("id", 0);
    }

    /**
     * 获取正在支付的订单
     */
    public function getPayingOrder($codeId, $money)
    {
        $where['code_id'] = $codeId;
        $where['order_pay_price'] = $money;
        $where['status'] = self::WAITEPAY;
        $where['add_time']  = array('gt', request()->time()-self::EXPREIDTIME);
        return $this->where($where)->find();
    }

    public function closeOrder($tradeNo)
    {
        $where["out_trade_no"] = $tradeNo;
        $info = $this->where($where)->find();
        if(empty($info))
        {
            return false;
        }

        if($info["status"] == self::WAITEPAY)
        {
            $where["out_trade_no"] = $tradeNo;
            $data["status"]       = self::CLOSED;
            return $this->where($where)->update($data);
        }

        //更新其它大于30分钟的订单
        $where = [];
        $where['add_time']  = array('lt', request()->time()-self::EXPREIDTIME);
        $where['status']    = self::WAITEPAY;
        $data["status"]     = self::CLOSED;
        $this->where($where)->update($data);

        //重新统计
        //更新code数据表PayingNum
        $GemapayCode = new \app\gemapay\model\GemapayCode();

        $codeId = $info->code_id;

        $where = [];
        $data = [];
        $where['id'] = $codeId;
        $data['paying_num'] = $this->getPayingOrder($codeId);
        $res = $GemapayCode->where($where)->update($data);
        //更新paying code数据表PayingNum
        $GemapayCodeMoneyPaying = new \app\gemapay\model\GemapayCodeMoneyPaying();

       // $data['paying_num'] = $this->getPayingOrder($codeId, );
    }
}