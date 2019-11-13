<?php
/**
 *
 */

namespace app\common\logic;

use app\common\library\enum\CodeEnum;
use app\common\model\GemapayCodeModel;
use app\common\model\GemapayOrderModel;
use app\index\logic\SecurityLogic;
use Think\Db;
use Think\Cache;
class GemapayOrderLogic
{
    /**
     * 生成訂單
     * @param $money
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function createOrder($money, $tradeNo,$codeType)
    {
        $GemapayCode = new GemapayCodeModel();
        $GemaPayOrder = new GemapayOrderModel();
        $GemapayCode->startTrans();

        //获取可以使用二维码
        $codeInfos = $GemapayCode->getAviableCode($money, $codeType);
        //如果匹配不到整数,去匹配小数点
        if (empty($codeInfos)) {
            $payPrices = $this->getAvaibleMoneys($money);
            foreach ($payPrices as $price) {
                $codeInfos = $GemapayCode->getAviableCode($price, $codeType);
                if (!empty($codeInfos)) {
                    $reallPayMoney = $price;
                    break;
                }
            }

            if (empty($codeInfos)) {
                $GemapayCode->rollback();
                return ['code' => CodeEnum::ERROR, 'msg' => '没有可用二维码'];
            }
        } else {
            $reallPayMoney = $money;
        }
        $userIds = [];
        foreach ($codeInfos as $code) {
            $userIds[] = $code['user_id'];
        }

        $userIds = array_unique($userIds);
        sort($userIds);
        $lastUserId = cache("last_userid");
        if (empty($lastUserId)) {
            $lastUserId = $userIds[0];
        } else {
            $flag = false;
            foreach ($userIds as $key => $userId) {
                if ($userId > $lastUserId) {

                    $flag = true;
                    $lastUserId = $userId;
                    break;
                }
            }
            if ($flag == false) {
                $lastUserId = $userIds[0];
            }
        }
        foreach ($codeInfos as $code) {
            if ($code['user_id'] == $lastUserId) {
                $codeInfo = $code;
                break;
            }
        }
        cache('last_userid', $lastUserId);
        $insId = $GemaPayOrder->addGemaPayOrder($codeInfo['user_id'], $money, $tradeNo, $codeInfo['id'], $reallPayMoney, $codeInfo['qr_image'], $codeInfo['user_name'], $codeInfo['type'], $tradeNo);
        $GemapayCodeModel = new GemapayCodeModel();
        if (!empty($codeInfo['id'])) {
            $GemapayCodeModel->incTodayOrder($codeInfo['id']);
        }
        if (empty($insId)) {
            $GemapayCode->rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '更新订单数据失败'];
        }
        //抢单成功,扣除余额
        $message = "抢单成功,扣除余额";
        if (false == accountLog($codeInfo['user_id'], 7, 0, $money, $message)) {
            $GemapayCode->rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '更新数据失败'];
        }
        $GemapayCode->commit();
        //匹配收款二维码
        $payImage = "http://" . $_SERVER['HTTP_HOST'] . "/";
        $ret['id'] = $insId;
        $ret['pay_image'] = $payImage . $codeInfo['raw_qr_image'];
        $ret['reall_pay_amount'] = $reallPayMoney;
        return ['code' => CodeEnum::SUCCESS, 'msg' => 'SUCCESS', 'data' => $ret];
    }

    /**
     * 用户完成订单
     * @param $orderId
     * @param $note
     * @param $userid
     */
    public function setOrderSucessByUser($orderId, $userid, $security)
    {
        //判断订单状态
        $GemaPayOrder = new \app\index\model\GemapayOrderModel();
        $SecurityLogic = new SecurityLogic();
        //判断交易密码
        $result = $SecurityLogic->checkSecurityByUserId($userid, $security);
        if($result['code'] == CodeEnum::ERROR)
        {
           // return $result;
        }
        //判断订单是否属于用户
        $where['id'] = $orderId;
        $where['status'] = $GemaPayOrder::WAITEPAY;
        $where['gema_userid'] = $userid;
        $orderInfo = $GemaPayOrder->where($where)->find();
        if(empty($orderInfo))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '订单信息有误'];
        }

        return $this->setOrderSucess($orderInfo, "用户手动调单");
    }

    /**
     * 管理员强制完成订单
     * @param $orderId
     * @param $adminId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setOrderSucessByAdmin($orderId, $adminId)
    {
        $GemaPayOrder = new GemapayOrderModel();

        $where['id'] = $orderId;
        $where['status'] = ['neq', $GemaPayOrder::PAYED];

        $orderInfo = $GemaPayOrder->where($where)->find();
        if(empty($orderInfo))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '订单信息有误'];
        }

        return $this->setOrderSucess($orderInfo, "后台管理员补单");
    }

    /**
     * 设置订单为成功状态
     * @param $orderId
     * @param string $note
     * @return array
     */
    public function setOrderSucess($orderInfo, $note)
    {
        $GemapayCodeModel = new \app\common\model\GemapayCodeModel();
        $GemapayCodeTypeModel = new \app\common\model\GemapayCodeTypeModel();
        $GemapayOrderModel = new \app\common\model\GemapayOrderModel();
        $codeInfo = $GemapayCodeModel->find($orderInfo["code_id"]);
        if(empty($codeInfo))
        {
            $bonusPrecent = 0.0;
        }
        else
        {
            if(empty($codeInfo["bonus_points"]))
            {
                $codeTypeInfo = $GemapayCodeTypeModel->find($codeInfo["type"]);
                if(!empty($codeTypeInfo))
                {
                    $bonusPrecent = $codeTypeInfo["defualt_bonus_points"]/1000;
                }
            }
            else
            {
                $bonusPrecent = $codeInfo["bonus_points"]/1000;
            }
        }

        //计算佣金比例


        $bonus = $orderInfo["order_price"] * $bonusPrecent;
        Db::startTrans();
        $notSendBonus = config('not_send_bonus');
        if(!empty($bonus) && empty($notSendBonus))
        {
            $message = "订单完成,增加佣金";
            $res = accountLog($orderInfo['gema_userid'], \app\common\library\enum\MoneyOrderTypes::ORDER_BONUS,
                \app\common\library\enum\MoneyOrderTypes::OP_ADD, $bonus, $message);
            if($res == false)
            {
                Db::rollback();
                return ['code' => CodeEnum::ERROR, 'msg' => '更新数据失败'];
            }
        }

        if($orderInfo['status'] == $GemapayOrderModel::CLOSED)
        {
            $message = "后台强制完成订单,扣除佣金";
            $res = accountLog($orderInfo['gema_userid'], \app\common\library\enum\MoneyOrderTypes::ORDER_FORCE_FINISH,
                \app\common\library\enum\MoneyOrderTypes::OP_SUB, $orderInfo["order_price"], $message);
            if($res == false)
            {
                Db::rollback();
                return ['code' => CodeEnum::ERROR, 'msg' => '更新数据失败'];
            }
        }



        $res = $GemapayOrderModel->setOrderSucess($orderInfo['id'], $note, $bonus);
        if($res == false)
        {
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '更新数据失败'];
        }

        $postData['out_trade_no']= $orderInfo['out_trade_no'];//这是第三方提交过来的订单号回传过去
        //向回调地址发起请求
        $ret = httpRequest(config('notify_url')."/api/notify/notify?channel=GumaPay",'post',$postData);
        if($ret == false)
        {
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '网络错误'];
        }

        Db::commit();

        return ['code' => CodeEnum::SUCCESS, 'msg' => '数据更新成功'];
    }

    public function getPaylink($id)
    {
        return config('custom.gumapay_link')."?id=".$id;
    }


    /**
     * 取消订单
     * @param $order
     */
    public function  cancleOrder($order){
        $GemapayOrderModel = new GemapayOrderModel();
        $where["order_no"] = $order;
        $order = $GemapayOrderModel->where($where)->find();
        if(empty($order) || $order['status'] != $GemapayOrderModel::WAITEPAY)
        {
            return false;
        }
          //取消订单
        $statusRet = $GemapayOrderModel->where(['id'=>$order['id']])->setField('status',2);
        if($statusRet!==false){
            //记录日志
            $message ="关闭订单：".$order['order_no'];
            return accountLog($order['gema_userid'], \app\common\library\enum\MoneyOrderTypes::ORDER_FORCE_FINISH,
                \app\common\library\enum\MoneyOrderTypes::OP_ADD,$order['order_pay_price'],$message);

        }
        return false;
    }

    /**
     * 获取可用金额列表
     * @param $money
     * @return array
     */
    protected function getAvaibleMoneys($money)
    {
        $data = [];
        $limit = GemapayCodeModel::MONEY_LIMIT_NUM;
        $moneyStart = $money - $limit * 0.01/5;
        for($i=0; $i<=$limit; $i++)
        {
            if($moneyStart + $i*0.01 != $money)
            {
                $data[] = floatval($moneyStart + $i*0.01);
            }
        }

        return $data;
    }

}