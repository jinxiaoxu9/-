<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 用户资产模型
 *
 */
class SomebillModel extends BaseModel
{



    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_somebill';


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


    /**
     * 获取多条记录
     * @param $where
     * @param $field
     * @param $order
     * @param $paginate
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  getBelongs($where = [], $field = true, $order = '', $paginate = 0){
          return $this->getList($where ,$field,$order ,$paginate);
    }

}
