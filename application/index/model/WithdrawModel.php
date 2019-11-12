<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 提现模型
 * Class BankcardModel
 * @package app\index\model
 */
class WithdrawModel extends BaseModel
{



    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_withdraw';



    /**
     * @param array $data
     * @param array $where
     * @return false|int|string
     */
    public function setWithdraw($data = [], $where = []){
        return $this->setInfo($data,$where);
    }




}
