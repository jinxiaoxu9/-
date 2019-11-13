<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 9/18/19
 * Time: 12:44 AM
 */
namespace app\api\controller;


use think\Controller;

class DeviceController extends Controller
{

    protected  $gemaLogic=null;

    public function __construct()
    {
        parent::__construct();
    }


    public function nolineNotify()
    {
        //判断类型根据每个类型去解析

        $string = $_GET["data"];
        $codeId = $_GET["code_id"];
        $data = explode(";", $string);
        $GemapayCode = new \app\gemapay\model\GemapayCode();
        $GemapayCollectOrder = new \app\gemapay\model\GemapayCollectOrder();
        $GemapayOrder = new \app\gemapay\model\GemapayOrder();
        Log::error($string . "code id is:" . $codeId);
        if (empty($codeId)) {
            return "false";
        }
        $code = $GemapayCode->find($codeId);
        if (empty($code)) {
            return "false";
        }

        $GemapayCode->setOnline($codeId);


        foreach ($data as $d) {
            if (empty($d)) {
                continue;
            }
            $item = explode(":", $d);

            $orderNo = $item[0];
            if (empty($orderNo)) {
                continue;
            }

            $price = sprintf('%.2f', $item[1]);

            $date = $item[2];

            $order_no = $item[0];

            if ($GemapayCollectOrder->checkAlreadyInsert($order_no, $codeId)) {
                continue;
            }

            $updateData = [];
            // Db::startTrans();
            //判断是否有正在支付的订单
            $order = $GemapayOrder->getPayingOrder($codeId, $price);
            if (empty($order)) {
                $updateData['status'] = $GemapayCollectOrder::PAYEERROR;
                $updateData['pay_order_no'] = "";
            } else {
                //完成订单
                $LGemaOrder = new \app\gemapay\logic\GemaOrder();
                $LGemaOrder->setOrderSucess($order->id);
                $_POST["out_trade_no"] = $order['out_trade_no'];
                $Notify = new \app\api\controller\Notify();
                $Notify->notify("GumaPay");

                $updateData['status'] = $GemapayCollectOrder::PAYED;
                $updateData['pay_order_no'] = trim($order->order_no);
            }
            $updateData['code_id'] = $codeId;
            $updateData['order_paytime'] = $date;
            $updateData['create_time'] = request()->time();
            $updateData['order_payprice'] = $price;
            $updateData['order_no'] = $order_no;
            $GemapayCollectOrder->insert($updateData);
        }
        return 1;
    }


}