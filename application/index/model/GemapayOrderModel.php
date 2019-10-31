<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:38
 */
namespace app\index\model;

use Think\Model;
class GemapayOrderModel extends Model
{
    const WAITEPAY = 0;
    const PAYED = 1;
    const CLOSED = 2;

    const EXPREIDTIME = 30*60;



    /**
     * 获取用户个码在今日的订单总和按照订单总金额排序
     * @param $userId
     */
    public function getUserCodeOrderMoneyToday($userId=0){
        $userId && $where['a.gema_userid']=$userId;
        $where['a.status'] =0;
        $where['add_time']=['between',[strtotime(date('Y-m-d')),strtotime(date('Y-m-d 23:59:59'))]];

        $todayOrder = $this->alias('a')
            ->field('sum(a.order_price) as totayCodeOrderMoney,code_id,code_type,gema_username,b.account_name,c.type_name,c.type_logo')
            ->join(C('DB_PREFIX').'gemapay_code b on a.code_id=b.id')
            ->join(C('DB_PREFIX').'gemapay_code_type c on a.code_type=c.id')
            ->where($where)
            ->group('a.code_id')
            ->order("totayCodeOrderMoney desc")
            ->select();
        return $todayOrder;
    }


    /**
     * 获取用户gema订单列表
     * @param int $userId
     * @parasm s $status
     * @return mixed
     */
    public function getUserCodeOrder($userId=0,$status=null,$page=10){
        $userId && $where['a.gema_userid']=$userId;
        (!is_null($status)) && $where['a.status']=$status;
        //获取两个小时内的书
        $starttime=time()-7200;
        //$where['add_time'] = array('between', array($starttime,time()));
        $userOrder = $this->alias('a')
            ->field('a.*,code_id,b.account_name,c.type_name,c.type_logo')
            ->join(C('DB_PREFIX').'gemapay_code b on a.code_id=b.id', "left")
            ->join(C('DB_PREFIX').'gemapay_code_type c on a.code_type=c.id', "left")
            ->where($where)
            ->order("add_time desc")
            ->page($_GET['p'],"{$page}")
            ->select();
        return $userOrder;
    }






}