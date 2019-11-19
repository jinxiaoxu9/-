<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 用户资产模型
 *
 */
class UserMessageModel extends BaseModel
{

    const READED = 1;
    const UNREAD = 0;

    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_user_message';


    /**
     * @param array $where
     * @param string $field
     * @return int|string
     * @throws \think\Exception
     */
    public function getUserMessageCount($where = [],$field ='*'){
        return $this->getCount($where,$field);
    }


}
