<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-6-5
 * Time: 下午2:24
 */


namespace app\gemapay\controller;

use app\common\model\GemapayUser;
use think\Controller;
use  app\common\library\enum;
use think\Request;

class User extends Controller
{
    protected $userId = 0;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $action = $request->action();
        $authList = [
            'uploadqrcode',
            'orderlist',
            'updateorderstatus',
            'userinfo',
            'paynotify'
        ];

        $this->userId = 1;

        if(in_array($action, $authList))
        {
            $token = $request->post("token", "286347099317cf9eb6c8790e093985c2");
            if(!empty($token))
            {
                $telphone = $request->post("telphone", '18100222363');
                $User = new \app\gemapay\logic\User();
                $r = $User->loginByToken($telphone, $token);
                if(!$r)
                {
                    $data = ['code' => enum\CodeEnum::ERROR, 'msg' => '请先登录'];
                    echo json_encode($data);die();
                }
                $this->userId = $r->id;
            }
            else
            {
                $data = ['code' => enum\CodeEnum::ERROR, 'msg' => '请先登录'];
                echo json_encode($data);die();
            }
        }
    }

    public function login()
    {
        $password = "123456";
        $telphone = "1810022222363";
        $User = new \app\gemapay\logic\User();
        $r = $User->login($telphone, $password);
        return json_encode($r);
    }

    public function register()
    {
        $password = "123456";
        $telphone = "18100222363";
        $User = new \app\gemapay\logic\User();
        $r = $User->register($telphone, $password);
        return json_encode($r);
    }

    public function addCode()
    {
        $type = $this->request->post("type", 1);
        $userId = $this->userId = 1;
        $image = $this->request->post("image", "a1.jpg");
        $User = new \app\gemapay\logic\User();
        $r = $User->addCode($userId, $type, $image);
        return json_encode($r);
    }

    public function updateCode()
    {
        $codeId = $this->request->post("code_id", 1);
        $image = $this->request->post("image", "a1.jpg");
        $User = new \app\gemapay\logic\User();
        $r = $User->updateCode($this->userId, $codeId, $image);
        return json_encode($r);
    }

    public function orderList()
    {
        $GemaPayOrder = new \app\gemapay\model\GemapayOrder();
        $datas  = $GemaPayOrder->getList($this->userId);
        return json_encode($datas);die();
    }

    public function updateOrderStatusSuccess()
    {
        $orderId = $this->request->post("order_id", '212');
        $GemaOrder = new \app\gemapay\logic\GemaOrder();
        $res = $GemaOrder->setOrderSucess($orderId, "用户手动调单");
        return json_encode($res);
    }

    public function userInfo()
    {

    }

    public function userCodeList()
    {
        $User = new \app\gemapay\logic\User();
        $r = $User->getCodeList($this->userId );
        return json_encode($r);
    }

    public function payNotify()
    {
        if($this->request->post() || 1)
        {
            $money = $this->request->post("money", 999.95);
            $type = $this->request->post("type", 0);
            $GemaOrder = new \app\gemapay\logic\GemaOrder();
            $r =  $GemaOrder->setOrderSucessByAuto($this->userId, $type, $money);
            return json_encode($r);
        }
    }

    //获取改用户支付宝有没有新的订单 查询订单生成时间　大于当前时间加５，小于当前时间＋100秒的未支付订单
    public function getNewOrder()
    {
        $string = '{"accountDetailForm":{"billUserId":"2088012420452136","bizTypeList":[],"endAmount":"","endDateInput":"2019-06-07 23:59:59","forceAync":"","goodsTitle":"","pageNum":"1","pageSize":"20","precisionQueryKey":"tradeNo","precisionQueryValue":"","queryEntrance":"1","reqUserId":"","searchType":"","searchableCardListJson":"","securityBizType":"","securityId":"","shopId":"","showType":"","sortTarget":"","sortType":"","startAmount":"","startDateInput":"2019-06-07 00:00:00","targetMainAccount":"","type":""},"queryForm":{"billUserId":"2088012420452136","bizTypeList":[],"endAmount":"","endDateInput":"2019-06-07 23:59:59","forceAync":"","goodsTitle":"","pageNum":"1","pageSize":"20","precisionQueryKey":"tradeNo","precisionQueryValue":"","queryEntrance":"1","reqUserId":"","searchType":"","searchableCardListJson":"","securityBizType":"","securityId":"","shopId":"","showType":"","sortTarget":"","sortType":"","startAmount":"","startDateInput":"2019-06-07 00:00:00","targetMainAccount":"","type":""},"status":"succeed","success":"true","result":{"summary":{"expendSum":{"amount":"0.00","count":0},"incomeSum":{"amount":"12.00","count":1}},"paging":{"totalItems":1,"current":1,"sizePerPage":20},"showBillInfo":true,"detail":[{"orderNo":"","bizDesc":"","bizNos":"","tradeNo":"20190607210500200010130086194251","billSource":"","accountType":"","otherAccountFullname":"*登伟","accountLogId":"313568263371131","transMemo":"转出到余额","tradeTime":"2019-06-07 17:31:34","chargeRate":"","tradeAmount":"12.00","otherAccount":"dummy","actualChargeAmount":"0.00","balance":"12.00","transDate":"2019-06-07","bizOrigNo":"","action":{"needDetail":false},"cashierChannels":"","goodsTitle":"","depositBankNo":"","otherAccountEmail":"182******63","signProduct":""}]},"isEntOperator":false}';
        $data = json_decode($string, true);
        var_dump($data['result']['detail']);die();
        $type = "alipay";
        $data['status'] = 1;
        return  json_encode($data, true);
    }
}