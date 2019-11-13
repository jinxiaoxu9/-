<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 9/18/19
 * Time: 12:44 AM
 */
namespace app\api\controller;


use app\admin\logic\GemaPayOrderLogic;
use app\common\model\GemapayCodeModel;
use app\common\model\GemapayOrderModel;
use think\Controller;

class ApiController extends Controller
{

    protected  $gemaLogic=null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 个码下单
     */
    public function addOrder()
    {
        $GemaPayOrderLogic = new \app\common\logic\GemapayOrderLogic();
        if(!empty(request()->get("money","")))
        {
            $money = request()->get("money","");
        }else
        {
            $money = request()->post("money","");
        }

        if(!empty(request()->get("code_type","")))
        {
            $codeType = request()->get("code_type","");
        }else
        {
            $codeType = request()->post("code_type","");
        }
        $tradeNo = request()->post("trade_no","");
        exit(json_encode($GemaPayOrderLogic->createOrder($money,$tradeNo,$codeType)));
    }

    /**
     * 关闭订单  crontab
     * linus   * * * * * curl http://www.paofen.net/gemapay-api-autoCancleOrder
     */
    public function  autoCancleOrder(){

        $orderNo = request()->post("order_no","10001");
        $GemaPayOrderLogic = new \app\common\logic\GemapayOrderLogic();
        echo $GemaPayOrderLogic->cancleOrder($orderNo);
    }

    public function clearYesterdayOrder()
    {
        $GemapayCode = new GemapayCodeModel();
        $GemapayCode->clearYesterdayOrder();
    }

}