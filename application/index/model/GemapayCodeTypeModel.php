<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 个码类型模型
 * Class SomebillModel
 * @package app\index\model
 */
class GemapayCodeTypeModel extends BaseModel
{



    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_gemapay_code_type';


    /**
     * 万物皆资源
     * @param array $where
     * @param bool $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  getCodeTypes($where = [], $field = true,$order='sort asc',$paginate=false){
        return $this->getList($where,$field,$order,$paginate);
    }

    public function getAllType()
    {
        return $this->select();
    }
}
