<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 银行卡模型
 * Class BankCardModel
 * @package app\index\model
 */
class BankcardModel extends BaseModel
{



    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_bankcard';


    /**
     * @param array $where
     * @param string $field
     * @return int|string
     * @throws \think\Exception
     */
    public function getBankCardCount($where = [],$field ='*'){
        return $this->getCount($where,$field);
    }


    /**
     * 设置卡号数据
     * @param array $data
     * @param array $where
     * @return false|int|string
     */
    public function setBankCard($data = [], $where = []){
        return $this->setInfo($data,$where);
    }



    public function  getBankCards($where = [], $field = true, $order = '', $paginate = 0){
        return $this->getList($where ,$field,$order ,$paginate);
    }

}
