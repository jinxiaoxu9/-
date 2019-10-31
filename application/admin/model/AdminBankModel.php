<?php
namespace Admin\Model;

use think\Model;

class AdminBankModel extends Model
{
    protected $tableName = 'admin_bank';
    /**
     * 自动验证规则
     *
     */
    protected $_validate = array(
        // self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
        // self::MUST_VALIDATE 或者1 必须验证
        // self::VALUE_VALIDATE或者2 值不为空的时候验证
        //验证账号
        array('account_name', 'require', '账号名称不能为空', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('account_num', 'require', '账号不能为空', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bank_name', 'require', '银行名称不能为空', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
    );



}
