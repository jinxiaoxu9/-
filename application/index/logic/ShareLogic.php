<?php

namespace app\index\logic;
use Admin\Model\UserInviteSetting;
use app\index\model\UserInviteSettingModel;
use app\index\model\UserModel;
use Common\Library\enum\CodeEnum;
use Gemapay\Model\GemapayCodeModel;
use app\index\model\User;
use Gemapay\Model\GemapayCodeTypeModel;

class ShareLogic
{
    /**
     * 获取分享链接列表
     * @param $userId
     * @param $page
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLinkList($userId)
    {
        $UserInviteSettingModel = new \app\index\model\UserInviteSettingModel();
        $GemapayCodeTypeModel = new \app\index\model\GemapayCodeTypeModel();
        $where['is_delete'] = $UserInviteSettingModel::STATUS_NO;
        $where['user_id'] = $userId;
        $lists =  $UserInviteSettingModel->getList($where ,'code,invite_setting,create_time,id','create_time desc' ,10);

        $codeTypeLists = $GemapayCodeTypeModel->getAllType();
        foreach ($lists as $key=>$link)
        {
            $lists[$key]["desc"] = $UserInviteSettingModel->getInviteDesc($link["invite_setting"], $codeTypeLists);
            $lists[$key]["invite_url"] = $UserInviteSettingModel->getInviteLink($link["code"]);
        }
        return $lists;
    }

    /**
     * 删除分享链接
     * @param $userId
     * @param $codeId
     * @return array
     */
    public function delLink($userId, $linkId)
    {
        $UserInviteSettingModel = new \app\index\model\UserInviteSettingModel();

        $where['id'] = $linkId;
        $where['user_id'] = $userId;

        $data['is_delete'] = $UserInviteSettingModel::STATUS_YES;

        $ret = $UserInviteSettingModel->isUpdate(true, $where)->save($data);
        if($ret)
        {
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '删除成功！'];
        }
        else
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }

    /**
     * 获取该用户初始分享信息
     */
    public function getInitShareInfo($userId)
    {
        $UserModel = new UserModel();
        $UserInviteSettingModel = new \app\index\model\UserInviteSettingModel();

        $userinfo = $UserModel->where(array("userid" => $userId))->find();
        $code = $userinfo["u_yqm"];

        $setting = $UserInviteSettingModel->where(array("code" => $code))->find();
        if(empty($setting))
        {
            return [];
        }

        $setting = json_decode($setting["invite_setting"]);

        $GemapayCodeTypeModel = new GemapayCodeTypeModel();
        $codeTypeLists = $GemapayCodeTypeModel->getAllType("id, type_name, type_logo");
        $codeTypeLists = filterDataMap($codeTypeLists, "id");
        $noemptySetting = [];
        foreach ($setting as $key => $s) {
            if (!empty($s)) {
                $codeTypeLists[$key]["max"] = $s;
                $noemptySetting[] = $codeTypeLists[$key];
            }
        }
        return $noemptySetting;
    }

    /*
    *添加分享链接
    */
    public function addLink($userId, $request)
    {
        $UserInviteSettingModel = new \app\index\model\UserInviteSettingModel();

        $UserModel = new UserModel();
        $userinfo = $UserModel->where(array("userid" => $userId))->find();
        $code = $userinfo["u_yqm"];
        if (empty($code))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '用户分成配置为空'];
        }

        $inviteSetting = $UserInviteSettingModel->where(array('code' => $code))->find();

        if (empty($inviteSetting["invite_setting"]))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '未找到分成配置'];
        }

        $setting = json_decode($inviteSetting["invite_setting"]);
        $settingArray = [];
        foreach ($setting as $key=>$s)
        {
            $settingArray[$key] = $s;
        }

        $GemapayCodeTypeModel = new \app\index\model\GemapayCodeTypeModel();
        $codeTypeLists = $GemapayCodeTypeModel->getAllType();
        $psetting = [];
        foreach ($codeTypeLists as $type) {
            if ($request->post('type_' . $type["id"])) {

                $points = $request->post('type_' . $type["id"]);
                if ($points > $settingArray[$type["id"]]) {
                    return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '费率超过范围'];
                }
            } else {
                $points = 0;
            }
            $psetting[$type["id"]] = $points;
        }
        $data["invite_setting"] = json_encode($psetting);
        $data["user_id"] = $userId;
        $data["code"] = strrand(9);
        $data["admin_id"] = 0;
        $data["create_time"] = time();
        $result = $UserInviteSettingModel->insert($data);
        if(!$result)
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '请一会儿再试'];
        }

        return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '添加成功'];
    }
}