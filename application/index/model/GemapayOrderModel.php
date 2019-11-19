<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:38
 */
namespace app\index\model;
use app\common\model\BaseModel;

class GemapayOrderModel extends BaseModel
{
    const WAITEPAY = 0;
    const PAYED = 1;
    const CLOSED = 2;


    public function  getGemapayOrder($where = [], $field = true){
        return $this->getInfo($where,$field);
    }



    public function  getLastGemapayOrder($where = [], $field = '*',$order='id desc'){
        return $this->where($where)->field($field)->order($order)->find();
    }


    public function getOrderList($where = [], $field = true, $order = '', $paginate = 0){
       return  $this->getList($where,$field,$order,$paginate);
    }



}