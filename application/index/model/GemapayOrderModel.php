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

}