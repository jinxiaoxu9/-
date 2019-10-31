<?php
namespace app\index\controller;
use Gemapay\Logic\GemapayOrderLogic;
class Order extends Common
{
    /**
     * 用户确认收款回调到支付平台
     */
    public function sureSk()
    {
        $orderId = I('id/d');
        $security = I('security/d');

        $GemaOrder = new GemapayOrderLogic();
        $res = $GemaOrder->setOrderSucessByUser($orderId,  $this->user_id, $security);
        exit(json_encode($res));
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
        $this->display();
    }


    /**
     * 当前用户个码订单列表
     */
    public function shoudan()
    {
        $GemapayOrderModel = new GemapayOrderModel();
        if (isAjax()) {
            $ret = $GemapayOrderModel->getUserCodeOrder($this->user_id, null, 10);
            if (!empty($ret)) {
                $statusMs = ['未支付', '已支付', '已关闭'];
                foreach ($ret as $k => $V) {
                    $ret[$k]['status'] = $statusMs[$V['status']];
                    $ret[$k]['order_no'] = '****' . mb_substr($V['order_no'], -4);
                }
            }
            // dd($ret);
            ajaxReturn($ret);
        }
        $this->display();
    }


    /**
     * 我的二维码
     */
    public function mycode()
    {
        $GemapayCodeModel = new GemapayCodeModel();
        $ret = $GemapayCodeModel->getUserCode($this->user_id);
        $this->assign('gemaCode', $ret);
        $this->display();
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
            $this->display();

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
        $this->display();
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

        $user = M("user")->where("userid=" . $userId)->find();
        if ($user["money"] < 50) {
            exit(json_encode(['code' => -1, 'msg' => '你的余额不足,请先充值']));
        }

        $qd_status = I('post.qd_status/d');
        //更新用户code码的最后在线时间
        $lastOnlineT = $qd_status ? 0 : time();
        $qd_status = $qd_status ? 0 : 1;

        //更改为抢单状态
        Cache::getInstance()->set('qdStatus_' . $userId, $qd_status);
        //本次选择的二维码入内存
        $qr_ids_arr = I('qr_ids_arr/a');

        (!empty($qr_ids_arr)) && Cache::getInstance()->set('qdqrcodesIds_' . $userId, $qr_ids_arr);

        //停止抢单的话那么所有处于缓存中的qr_id都要清空
        ($qd_status == 0) && Cache::getInstance()->set('qdqrcodesIds_' . $userId, null);

        $map['user_id'] = $this->user_id;
        $map['id'] = ['in', $qr_ids_arr];
        $update['last_online_time'] = $lastOnlineT;
        $ret = M("gemapay_code")->where($map)->save($update);

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
        $qrTypesArr = I('post.qr_types_arr');
        $exists = [];

        $countNum = array_count_values($qrTypesArr);
        foreach($countNum as $k=>$v){
            $allowNum =C('qdQrcodeTypesNum');
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
        $ret = M("gemapay_code")->where($map)->save($update);
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
        $ret = M("gemapay_code")->where($map)->save($update);

        if ($ret !== false) {
            $this->success('已停止');
        }
        $this->error('操作失败');
    }


    /**
     * 上传转款凭证
     * @return mixed
     */
    public function uplaodCredentials()
    {
        $orderId = I('order_id');
        $GemapayOrderModel = new GemapayOrderModel();
        if (empty($orderId)) {
            $this->error('参数错误');
        }
        $orderInfo = $GemapayOrderModel->where(['id' => $orderId])->find();
        if ($_POST) {
            $data = I('post.');
            //再校验一下
            if ($orderInfo['status'] == 1 && $orderInfo['credentials'] == 0) {
                $update['credentials'] = $data['icon'];
                $update['is_upload_credentials'] = 1;
                $ret = M('gemapay_order')->where(['id' => $orderId])->save($update);
                if ($ret !== false) {
                    $this->success('操作成功');
                }
                $this->error('操作失败');
            }
            $this->error('参数错误');
        }
        $this->assign('order', $orderInfo);
        $this->display();
    }
}