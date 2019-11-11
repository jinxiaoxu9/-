<?php

namespace app\index\logic;

use Common\Library\enum\CodeEnum;
use app\index\model\ConfigModel;
use app\index\model\User;

class UserLogic
{
    public function getIndexInfo($userId)
    {
        $UserModel = new User();
        $ConfigModel = new ConfigModel();

        $userInfo = $UserModel->where(array('userid' => $userId))->find();
        $conf = $ConfigModel->field('value')->where(['name' => 'USER_NAV'])->find();

        $data['config'] = json_decode($conf['value'], true);
        $data['userinfo'] = $userInfo;
        return $data;
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
}