<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 9/18/19
 * Time: 12:44 AM
 */


use Common\Library\enum\CodeEnum;
use Gemapay\Logic\GemapayOrderLogic;
use Gemapay\Model\GemapayCodeModel;
use Think\Db;
use Think\Log;
use Think\Controller;

class ApiController extends Controller
{

    protected  $gemaLogic=null;

    public function __construct()
    {
        parent::__construct();
        (is_null($this->gemaLogic)) && $this->gemaLogic = new GemapayOrderLogic();
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

    public function lists()
    {
        $GemapayCollectOrder = new \app\gemapay\model\GemapayCollectOrder();
        $datas = $GemapayCollectOrder->paginate(20);
        $this->assign('datas', $datas);
        return $this->fetch();
    }


    /**
     * 个码下单
     */
    public function addOrder()
    {

        /*try{*/
            if(!empty(I('get.money',"")))
            {
                $money = I('get.money',"");
            }else
            {
                $money = I('post.money',12);
            }

            //所有个码类型----所有可以用来收款的二维码
            $codeTypes= M('gemapay_code_type')->field('id')->select();

           /* $codeTypes= array_column($codeTypes,'id');
            //第三方接入如果不传想获取的二维码类型则随机取一个二维码类型
           // $codeType=I('code_type',$codeTypes[array_rand($codeTypes)]);
            */
            if(!empty(I('get.code_type',"")))
            {
                $codeType = I('get.code_type',"");
            }else
            {
                $codeType = I('post.code_type',1);
            }

            $tradeNo = I('post.trade_no',201910132454465784);
            exit(json_encode($this->gemaLogic->createOrder($money,$tradeNo,$codeType)));
       /* }catch(\Exception $e){
            echo  json_encode(['code' =>CodeEnum::ERROR, 'msg' =>$e->getMessage()]);
        }*/
    }

    /**
     * 关闭订单  crontab
     * linus   * * * * * curl http://www.paofen.net/gemapay-api-autoCancleOrder
     */
    public function  autoCancleOrder(){

        $where['order_no'] = I('post.order_no',"B20191025205944A25239");

        $field="id,order_no,status,order_pay_price,gema_userid";
        $cancleOrders = M('gemapay_order')->where($where)->field($field)->order(['add_time'=>'asc'])->select();
        if(empty($cancleOrders))
        {
            return false;
        }
        //暂时关闭事务吧 todo 后面修改存储引擎
        //M()->startTrans();
        try{
            if(is_array($cancleOrders) && count($cancleOrders)>0){
                foreach($cancleOrders as $order){
                    //再判断一下防止未处理到  todo 后面优化
                    if($order['status']==0){
                       $this->gemaLogic->cancleOrder($order);
                    }
                }
            }
        }catch(\Exception $e){
           // M()->rollback();
            file_put_contents('./error/cancle_order.log',$e->getMessage(),FILE_APPEND);
        }
    }

    public function clearYesterdayOrder()
    {
        $GemapayCode = new GemapayCodeModel();
        $GemapayCode->clearYesterdayOrder();
    }

}