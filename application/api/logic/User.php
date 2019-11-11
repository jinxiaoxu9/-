<?php
/**
 *
 */

namespace app\gemapay\logic;



use  app\common\library\enum;
use app\common\logic\BaseLogic;
use app\common\model\GemapayCode;
use app\common\model\GemapayUser;


class User extends BaseLogic
{

    public function register($telphone, $password)
    {
        $GemapayUser = new GemapayUser();

        if($GemapayUser->checkUser($telphone))
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '用户名已经存在'];
        }

        if(!$GemapayUser->addUser($telphone, $password))
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '注册失败'];
        }

        return ['code' => enum\CodeEnum::SUCCESS, 'msg' => '注册成功'];
    }

    public function login($telphone, $password)
    {
        $GemapayUser = new GemapayUser();

        if(!$GemapayUser->checkLogin($telphone, $password))
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '登陆失败,帐户或者密码错误'];
        }

        $token = $this->createToken();

        if(!$GemapayUser->setToken($telphone, $token))
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '登陆失败'];
        }

        return ['code' => enum\CodeEnum::SUCCESS, 'msg' => '注册成功'];
    }

    public function loginByToken($telphone, $token)
    {
        $GemapayUser = new GemapayUser();
        $res = $GemapayUser->checkLoginByToken($telphone, $token);
        if(!$res)
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '登陆失败,帐户或者密码错误'];
        }
        return $res;
    }

    public function addCode($userId, $type, $Qrimage)
    {
        $GemapayCode = new GemapayCode();
        $res = $GemapayCode->checkCode($userId, $type);
        if($res)
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '添加失败,你已经有该类型二维码了'];
        }

        $GemapayUser = new GemapayUser();
        $userInfo = $GemapayUser->getInfo($userId);


        $res = $GemapayCode->addCode($userId, $type, $Qrimage, $userInfo->telphone);
        if(!$res)
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '添加失败'];
        }

        return ['code' => enum\CodeEnum::SUCCESS, 'msg' => '添加成功'];
    }

    public function updateCode($userid, $codeId, $Qrimage)
    {
        $GemapayCode = new GemapayCode();

        $res = $GemapayCode->updateCode($userid, $codeId, $Qrimage);
        if(!$res)
        {
            return ['code' => enum\CodeEnum::ERROR, 'msg' => '添加失败'];
        }

        return ['code' => enum\CodeEnum::SUCCESS, 'msg' => '添加成功'];
    }

    public function getCodeList($userId)
    {
        $GemapayCode = new GemapayCode();
        return $GemapayCode->getList($userId);
    }

    protected function createToken()
    {
        return  md5(time()+date("Y-m-d"));
    }
}