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
    /**
     * 获取改用户可以添加的二维码类型列表
     * @param $userId
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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
            $codeTypes = $GemapayCodeTypeModel->where($where)->field('id,type_name')->order('sort asc,id desc')->select();
        }
        foreach ($codeTypes as $key=>$codeType)
        {
            $codeTypes[$key]['rate'] = $setting[$key];
        }
        return $codeTypes;
    }

    /**
     * 获取二维码列表
     * @param $userId
     * @param $page
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCodeList($userId, $page, $pageSize=30)
    {
        $GemapayCodeModel = new \app\index\model\GemapayCodeModel();
        $fileds = [
            'id',
            'status',
            'type',
            'account_name',
            "failed_order_num",
            "success_order_num",
            "qr_image",
            "order_today_all",
            "bonus_points",
            "create_time",
            "account_number"
        ];

        $list = $GemapayCodeModel->field($fileds)->where(array('user_id' => $userId))->page($page, $pageSize)->select();
        foreach ($list as $key=>$l)
        {
            $list[$key]['type_icon'] = "/static/icon/t_".$l['type'].".png";
        }
        return $list;
    }

    /**
     * 删除二维码
     * @param $userId
     * @param $codeId
     * @return array
     */
    public function delCode($userId, $codeId)
    {
        $GemapayCodeModel = new \app\index\model\GemapayCodeModel();
        $where['id'] = $codeId;
        $where['user_id'] = $userId;
        $ret = $GemapayCodeModel->where($where)->delete();
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
     * 冻结二维码
     * @param $userId
     * @param $codeId
     * @return array
     */
    public function disactiveCode($userId, $codeId)
    {
        $GemapayCodeModel = new \app\index\model\GemapayCodeModel();
        $where['id'] = $codeId;
        $where['user_id'] = $userId;

        $data['status'] = $GemapayCodeModel::STATUS_NO;
        $ret = $GemapayCodeModel->isUpdate(true, $where)->save($data);
        if($ret)
        {
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '冻结成功！'];
        }
        else
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }

    /**
     * 激活二维码
     * @param $userId
     * @param $codeId
     * @return array
     */
    public function activeCode($userId, $codeId)
    {
        $GemapayCodeModel = new \app\index\model\GemapayCodeModel();
        $where['id'] = $codeId;
        $where['user_id'] = $userId;

        $data['status'] = $GemapayCodeModel::STATUS_YES;
        $ret = $GemapayCodeModel->isUpdate(true, $where)->save($data);
        if($ret)
        {
            return ['code' => \app\common\library\enum\CodeEnum::SUCCESS, 'msg' => '激活成功！'];
        }
        else
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '网络错误！'];
        }
    }

    /*
    *添加二维码
    */
    public function addQRcode($userId, $codeType, $imgs, $accountName, $accountNumber, $security)
    {
        $UserModel = new UserModel();
        $InviteSettingModel = new UserInviteSettingModel();
        $GemapayCodeModel = new \app\index\model\GemapayCodeModel();
        $SecurityLogic = new SecurityLogic();
        $userInfo = $UserModel->find($userId);

        $res = $SecurityLogic->checkSecurityByUserId($userId, $security);

        if($res['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            return $res;
        }

        if (empty($userInfo["u_yqm"]))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '用户分成配置为空'];
        }

        $inviteSetting = $InviteSettingModel->where(array('code' => $userInfo["u_yqm"]))->find();

        if (empty($inviteSetting["invite_setting"]))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '未找到分成配置'];
        }

        $setting = json_decode($inviteSetting["invite_setting"]);

        if (empty($setting->$codeType))
        {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '未找到对应的分成配置'];
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