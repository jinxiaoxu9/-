<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;

use think\Model;

/**
 * 部门模型
 * @author jry <bbs.sasadown.cn>
 */
class UserGroupModel extends Model
{
    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    const STATUS_WORK = 1;

    const STATUS_NOT_WORK = 0;

    protected $tableName = 'user_group';

    public function getLevelList($adminId, $level=1)
    {
        $where = [];
        if($adminId != 1)
        {
            $where["admin_id"] = $adminId;
        }
        $where["level"] = $level;

        return $this->where($where)->select();
    }
}
