<?php
namespace app\admin\logic;

use app\admin\model\UserModel;
use think\Db;

class UserLogic
{
    /**
     * @param $array
     * @param int $pid
     * @param int $level
     * @return array
     */
    function getCategory($array, $pid =0, $level = 1)
    {
        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];
        foreach ($array as $key => $value) {
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['parentid'] == $pid) {
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getCategory($array, $value['id'], $level + 1);
            }
        }
        return $list;
    }

    /**
     * 验证手机号是否正确
     * @author 陶
     * @param $mobile
     */
    function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^1[34578]\d{9}$#', $mobile) ? true : false;
    }

    /**密码加密
     * @param $value
     * @param $salt
     * @return string
     */
    function pwd_md5($value, $salt){
        $user_pwd = md5(md5($value) . $salt);
        return $user_pwd;
    }

    /**获取设备IP
     * @return array|false|mixed|string
     */
    function get_userip(){
        //判断服务器是否允许$_SERVER
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }

    /**
     * @param $uid  uwer_id
     * @param int $type   资金操作类型对应数据库中jl_class
     * @param int $add_subtract  添加或者减少
     * @param float $money    操作金额
     * @param string $tip_message    资金流水备注
     * @return bool
     */
    function accountLog($uid, $type=1,$add_subtract = 1, $money=0.00, $tip_message = '')
    {
        $UserModel = new UserModel();
        $user= $UserModel->where(['userid'=>$uid])->find();

        //转账身份检测
        if ($user) {  //当前用户状态正常
            $moneys = ($add_subtract == 1) ? $money : 0 - $money;
            $updateBalanceRes = $UserModel->where(['userid' => $uid])->setInc('money', $moneys);
            if ($updateBalanceRes) {
                //记录流水
                $insert['uid'] = $uid;
                $insert['jl_class'] = $type;
                $insert['info'] = $tip_message;
                $insert['addtime']= time();
                $insert['jc_class']= ($add_subtract)?"+":"-";
                $insert['num']= $money;
                $insert['pre_amount']= $user['money'];
                $insert['last_amount']= $user['money']+$moneys;
                if( Db::name('somebill')->insert($insert) ) {
                    return true;
                }
                return false;
            } else {
                return false;
            }
        }
        return false;
    }
}