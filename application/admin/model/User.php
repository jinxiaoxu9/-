<?php
namespace app\admin\model;

use think\Model;
use think\Validate;
/**
 * 用户模型
 *
 */
class User extends Model
{
    protected $tableName = 'ysk_user';

     /**
     * [pwdMd5 用户密码加密]
     * 
     */
    public function pwdMd5($value,$salt){
       $user_pwd=md5(md5($value).$salt);
       return  $user_pwd;
    }
    

}
