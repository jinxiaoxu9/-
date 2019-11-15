<?php

namespace app\index\model;
use app\common\model\BaseModel;

/**
 * 用户模型
 *
 */
class UserModel extends BaseModel
{



    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'ysk_user';


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
    public function  getUser($where = [], $field = true){
        return $this->getInfo($where,$field);
    }


    //验证登录密码是否正确
    public function check_pwd_one($value, $userId)
    {
        $where['userid'] = $userId;
        $u_info = $this->where($where)->field('login_pwd,login_salt')->find();
        $salt = $u_info['login_salt'];
        $pwd = $u_info['login_pwd'];
        if ($pwd == pwdMd5($value, $salt))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


}
