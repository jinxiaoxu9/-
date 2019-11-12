<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 个码订单模型
 * Class GemaOrderModel
 *
 * @package app\index\model
 */
class GemaOrderModel extends BaseModel
{



    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_gemapay_order';


    /**
     * 万物皆资源
     * 获取一条用户信息
     * @param array $where
     * @param bool $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  getBelong($where = [], $field = true){
        return $this->getInfo($where,$field);
    }

}
