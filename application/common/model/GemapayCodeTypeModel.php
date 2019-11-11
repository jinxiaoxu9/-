<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:38
 */
namespace app\common\model;

use think\Model;
class GemapayCodeTypeModel extends Model
{
    const ZHIFUBAO = 1;
    const WEIXIN = 2;
    const CAIFUTONG = 3;
    const BAIFUTONG =4;

    public function getAllType()
    {
        return $this->select();
    }
}