<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;
use app\index\model\SomebillModel;

/**
 * 用户资产控制器
 * Class Belongs
 * @package app\index\controller
 */
class BelongsController extends CommonController
{


    /**
     * 个人中心用户资产控制器
     */
    public function index(){
        //用户金额
        $user  = $this->modelUser->getUser(['userid'=>$this->user_id],'money,u_yqm');
        $ret['money'] = formateFrice($user['money']);
        //获取某个用户的佣金费率
        $ret['codeTypeJjPercent'] = $this->logicBelongs->getYjconfig(['code'=>$this->user_id['u_yqm']]);
        //奖金金额
        $ret['jjMoneys'] =formateFrice($this->modelSomebill->getBelong(['uid' =>$this->user_id,'jl_class' => 1],'sum(num) as sum_jj'));;
        //代收款和已收款款
        $GemapayOrderModel = $this->modelGemapayOrder;
        $ret['skMoneys '] = formateFrice($GemapayOrderModel->getGemapayOrder(['gema_userid' => $this->user_id, 'status' => $GemapayOrderModel::PAYED],'sum(order_price) as skMoneys'));
        $ret['unskMoneys  '] = formateFrice($this->modelGemapayOrder->getGemapayOrder(['gema_userid' => $this->user_id, 'status' => $GemapayOrderModel::WAITEPAY],'sum(order_price) as skMoneys'));
        ajaxReturn('success',1,'',$ret);
    }

    /**
     * 用户资金账变记录
     */
    public function changeLog(){
           $where['uid'] = $this->user_id;
           $SomebillModel = new SomebillModel();
           $list = $SomebillModel->getBelongs($where,'*','addtime desc',10);
           $data['list'] = $list;
           ajaxReturn('success',CodeEnum::SUCCESS,'',$data);
    }



    /****************************************end******************************************/








}