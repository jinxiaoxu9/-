<?php
namespace app\index\controller;

use app\common\library\enum\CodeEnum;
use app\common\model\GemapayOrderModel;

class OrderController extends CommonController
{
    public function oderList()
    {
        $OrderLogic = new \app\index\logic\OrderLogic();
        $orderList = $OrderLogic->getList($this->user_id);

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

    /*
     *抢单页面
     */
    public function index()
    {
        $GemapayOrderModel = new GemapayOrderModel();
        //当前用户个码在今日的订单总和按照订单总金额排序
        $ret = $GemapayOrderModel->getUserCodeOrderMoneyToday($this->user_id);
        $this->assign('todayCodeOrder', $ret);
        return $this->fetch();
    }

    /**
     * 我的二维码
     */
    public function mycode()
    {
        $GemapayCodeModel = new GemapayCodeModel();
        $ret = $GemapayCodeModel->getUserCode($this->user_id);
        $this->assign('gemaCode', $ret);
        return $this->fetch();
    }


    //会员抢单详请
    public function qiangdanxq()
    {
        $GemapayOrderModel = new GemapayOrderModel();
        if ($_GET) {
            $id = trim(I('get.id'));
            $olist = $GemapayOrderModel->where(array('id' => $id))->find();
            $ewmlist = M('gemapay_code')->where(array('uid' => $this->user_id, 'id' => $olist['code_id']))->find();
            $this->assign('olist', $olist);
            $this->assign('ewmlist', $ewmlist);
            return $this->fetch();

        } else {
            $this->error('未知错误', U('Index/shoudan'));
        }

    }


    /**
     *抢单页面---
     */
    public function qiangdan()
    {
        $userId = $this->user_id;
        $qiangdanStatus = Cache::getInstance()->get('qdStatus_' . $userId);

        //在线时间多少时间可以参与抢单默认20分钟
        $onLineTime = C('online_time_toqiangdan');
        $this->assign('onLineTime', $onLineTime);
        $this->assign('qiangdanStatus', $qiangdanStatus);
        //获取用户所有可以参与抢单的二维码

        $where['user_id'] = $this->user_id;

        if ($qiangdanStatus) { //处在抢单状态
            $qrcodesIdsArr = Cache::getInstance()->get('qdqrcodesIds_' . $userId);
            if (!empty($qrcodesIdsArr)) {
                $where['id'] = ['in', $qrcodesIdsArr];
            }
        }
        $userinfo = M("user")->find($userId);
        //更新用户当前code码的所有最后在线时间
        // $qiangdanStatus && M("gemapay_code")->where($where)->setField('last_online_time',time());

        $qrcodes = M("gemapay_code")->field('id,qr_image,bonus_points')->where($where)->select();
        //允许多少个二维码参与抢单
        $configQdQrcodeNum = C('qdQrcodeNum');

        $this->assign('qrcodes', $qrcodes);
        $this->assign('configQdQrcodeNum', $configQdQrcodeNum);
        $this->assign('info', $userinfo);
        return $this->fetch();
    }

    /**
     * 修改抢单状态
     */
    public function changeQdStatus()
    {
        $userId = $this->user_id;
        $GemapayCodeModel = new GemapayCodeModel();
        $isexistCode = $GemapayCodeModel->where(['user_id' => $userId, 'status' => 0])->find();
        if (empty($isexistCode)) {
            exit(json_encode(['code' => -1, 'msg' => '您还未上传收款二维码或二维码不支持抢单']));
        }

        $user = Db::name("user")->where("userid=" . $userId)->find();
        if ($user["money"] < 50) {
            exit(json_encode(['code' => -1, 'msg' => '你的余额不足,请先充值']));
        }

        $qd_status = input('qd_status');
        //更新用户code码的最后在线时间
        $lastOnlineT = $qd_status ? 0 : time();
        $qd_status = $qd_status ? 0 : 1;

        //更改为抢单状态
        Cache::getInstance()->set('qdStatus_' . $userId, $qd_status);
        //本次选择的二维码入内存
        $qr_ids_arr = input('qr_ids_arr');

        (!empty($qr_ids_arr)) && Cache::getInstance()->set('qdqrcodesIds_' . $userId, $qr_ids_arr);

        //停止抢单的话那么所有处于缓存中的qr_id都要清空
        ($qd_status == 0) && Cache::getInstance()->set('qdqrcodesIds_' . $userId, null);

        $map['user_id'] = $this->user_id;
        $map['id'] = ['in', $qr_ids_arr];
        $update['last_online_time'] = $lastOnlineT;
        $ret = Db::name("gemapay_code")->where($map)->update($update);

        if ($ret !== false) {
            exit(json_encode(['code' => 1, 'msg' => '操作成功']));
        }
        return json_encode(['code' => -1, 'msg' => '操作失败']);
    }


    /**
     * 选择二维码开始抢单
     */
    public function startQd()
    {
        $qrTypesArr = input('qr_types_arr');
        $exists = [];

        $countNum = array_count_values($qrTypesArr);
        foreach($countNum as $k=>$v){
            $allowNum = config('qdQrcodeTypesNum');
            if($v>$allowNum){
                $this->error('同一种类型的二维码只允许使用'.$allowNum.'个参与抢单');
            }
        }

        $userId = $this->user_id;
        $user = M("user")->where("userid=" . $userId)->find();
        if ($user["money"] < 50) {
            $this->error('你的余额不足,请先充值');
        }

        $qrCodeIds = I('qr_ids_arr/a');
        if (empty($qrCodeIds)) {
            $this->error('请选择二维码');
        }

        if (is_array($qrCodeIds)) {
            $gemaModel = new  GemapayCodeModel();
            foreach ($qrCodeIds as $qr_id) {
                $qr = $gemaModel->getInfo(['id' => $qr_id]);
                if ($qr == false) {
                    $this->error($gemaModel->errormsg);
                }
            }
        }
        //本次选中的参与跑分的二维码入内存
        Cache::getInstance()->set('qdqrcodesIds_' . $userId, $qrCodeIds);
        //更改为抢单状态
        Cache::getInstance()->set('qdStatus_' . $userId, 1);

        $map['user_id'] = $this->user_id;
        $map['id'] = ['in', $qrCodeIds];
        $update['last_online_time'] = time();
        $ret = Db::name("gemapay_code")->where($map)->update($update);
        if ($ret !== false) {
            $this->success('操作成功');
        }
        $this->error('系统错误');
    }


    public function stopQd()
    {
        $userId = $this->user_id;
        Cache::getInstance()->set('qdqrcodesIds_' . $userId, null);
        Cache::getInstance()->set('qdStatus_' . $userId, 0);
        $map['user_id'] = $this->user_id;
        $update['last_online_time'] = 0;
        $ret = Db::name("gemapay_code")->where($map)->update($update);

        if ($ret !== false) {
            $this->success('已停止');
        }
        $this->error('操作失败');
    }


}