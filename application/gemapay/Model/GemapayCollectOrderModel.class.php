<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:38
 */
namespace Gemapay\Model;

use think\Model;
class GemapayCollectOrder extends Model
{

    const PAYED = 1;
    const PAYEERROR = 2;

    public function checkAlreadyInsert($orderNo, $codeId)
    {
        $where['order_no'] = $orderNo;
        $where['code_id'] = $codeId;
        $res = $this->where($where)->find();
        if(empty($res))
        {
            return false;
        }
        return true;
    }
}