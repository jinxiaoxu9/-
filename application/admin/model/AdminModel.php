<?php
namespace app\admin\model;

use think\Model;

class AdminModel extends Model
{
    protected $pk = 'id';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'ysk_admin';
}