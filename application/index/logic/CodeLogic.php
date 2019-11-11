<?php

namespace app\index\logic;
use Admin\Model\UserInviteSetting;
use app\common\model\GemapayCodeTypeModel;
use app\index\model\UserInviteSettingModel;
use app\index\model\UserModel;
use Common\Library\enum\CodeEnum;
use Gemapay\Model\GemapayCodeModel;
use app\index\model\User;

class CodeLogic
{
    public function getcodeTypes($userId)
    {
        $UserModel = new UserModel();
        $InviteSettingModel = new UserInviteSettingModel();
        $GemapayCodeTypeModel = new GemapayCodeTypeModel();
        $userinfo = $UserModel->find($userId);
        $inviteSetting = $InviteSettingModel->where(array('code' => $userinfo["u_yqm"]))->find();
        $setting = json_decode($inviteSetting["invite_setting"]);

        $typeIds = [];
        if(empty($setting))
        {
            return [];
        }
        foreach ($setting as $key => $s) {
            if (!empty($s)) {
                $typeIds[] = $key;
            }
        }

        if (empty($typeIds)) {
            $codeTypes = [];
        } else {
            $where["id"] = array("in", $typeIds);
            $codeTypes = $GemapayCodeTypeModel->where($where)->field('id,type_name,limit_money')->order('sort asc,id desc')->select();
        }

        return $codeTypes;
    }

    /*
    *添加二维码
    */
    public function addQRcode($userId, $codeType, $imgs, $accountName, $accountNumber, $security)
    {
        $UserModel = new User();
        $InviteSettingModel = new UserInviteSettingModel();
        $GemapayCodeModel = new \app\index\model\GemapayCodeModel();
        $SecurityLogic = new SecurityLogic();
        $userInfo = $UserModel->find($userId);

        $res = $SecurityLogic->checkSecurityByUserId($userId, $security);

        if($res['code'] == CodeEnum::ERROR)
        {
            return $res;
        }

        if (empty($userInfo["u_yqm"]))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '用户分成配置为空'];
        }

        $inviteSetting = $InviteSettingModel->where(array('code' => $userInfo["u_yqm"]))->find();

        if (empty($inviteSetting["invite_setting"]))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '未找到分成配置'];
        }

        $setting = json_decode($inviteSetting["invite_setting"]);

        if (empty($setting->$codeType))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '未找到对应的分成配置'];
        }

        $imgs = explode("?", $imgs);
        $data['user_id'] = $userId;
        $data['type'] = $codeType;
        $data['qr_image'] = $imgs[0];
        $data['raw_qr_image'] = $imgs[1];
        $data['account_name'] = $accountName;
        $data['account_number'] = $accountNumber;
        $data['bonus_points'] = $setting->$codeType;
        $data['user_name'] = $userInfo['username'];
        $data['limit_money'] = 10000;
        $data['create_time'] = time();

        $result = $GemapayCodeModel->insert($data);
        if(!$result)
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '保存失败,请一会儿再试'];
        }

        return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '修改成功'];
    }
}