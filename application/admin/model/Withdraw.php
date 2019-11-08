<?php


namespace app\admin\Model;

use think\Model;

class Withdraw extends Model
{
    protected $pk = 'id';

    // 设置当前模型对应的完整数据表名称
    protected $table = 'ysk_withdraw';
}