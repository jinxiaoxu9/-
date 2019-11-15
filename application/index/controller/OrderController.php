<?php
namespace app\index\controller;

use app\common\library\enum\CodeEnum;

class OrderController extends CommonController
{
    public function oderList()
    {
        $OrderLogic = new \app\index\logic\OrderLogic();
        $status = $this->request->post('status', '-1');
        $orderList = $OrderLogic->getList($this->user_id, $status);
        $data['order'] = $orderList;
        ajaxReturn('成功',1,'', $orderList);
    }

    /**
     * 用户确认收款回调到支付平台
     */
    public function sureSk()
    {
        $orderId = $this->request->post('order_id');
        $security = $this->request->post('security');
        $GemaOrder = new \app\common\logic\GemapayOrderLogic();
        $res = $GemaOrder->setOrderSucessByUser($orderId,  $this->user_id, $security);

        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }


    /**
     * 上传转款凭证
     * @return mixed
     */
    public function uplaodCredentials()
    {
        $orderId = $this->request->post('order_id');
        $credentials = $this->request->post('credentials');

        $OrderLogic = new \app\index\logic\OrderLogic();
        $res = $OrderLogic->uplaodCredentials($this->user_id, $orderId, $credentials);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }

}