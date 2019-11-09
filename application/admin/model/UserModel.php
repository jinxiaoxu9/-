<?php
namespace app\admin\model;

use think\Model;
/**
 * 用户模型
 *
 */
class UserModel extends Model
{
    protected $pk = 'userid';

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
