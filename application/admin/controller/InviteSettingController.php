<?php

namespace app\admin\controller;

use app\admin\logic\InviteSettingLogic;
use app\admin\model\UserInviteSettingModel;
use app\admin\model\InviteSettingModel;
use app\admin\model\GemaPayOrderModel;
use app\admin\model\GemaPayCodeTypeModel;
use think\Request;
use think\Db;

/**
 * 用户控制器
 *
 */
class InviteSettingController extends AdminController
{
    /**
     * 总后台邀请设置
     */
    public function index()
    {
        $map = [];
        // 获取所有用户
        if($this->admin_id!=1)
        {
            $map['admin_id'] = $this->admin_id; //超级管理员　admin 添加
            $map['user_id'] = 0;
        }

        $InviteSettingModel = new UserInviteSettingModel();
        $fields=[
            "i.*",
            "a.nickname"
        ];
        $inviteSetting   = DB::table('ysk_user_invite_setting');
        $data_list     = $inviteSetting->alias('i')
            ->where($map)
            ->field($fields)
            ->order('i.id desc')
            ->join('ysk_admin a ', 'a.id=i.admin_id', "LEFT")
            ->paginate(20);

        $count = $data_list->total();
        // 获取分页显示
        $page = $data_list->render();

        $InviteSettingLogic = new InviteSettingLogic();
        $codeTypeLists = Db::name('gemapay_code_type')->select();
        $arr_lists =  filterDataMap($codeTypeLists, "id");

        $data_list = $data_list->items();
         //添加描述
         foreach ($data_list as $key => $data) {
             $data_list[$key]["desc"] = $InviteSettingLogic->getInviteDesc($data["invite_setting"], $arr_lists);
             $data_list[$key]["invite_url"] = $InviteSettingLogic->getInviteLink($data["code"]);
         }

        $this->assign('list',$data_list);
        $this->assign('page',$page);
        $this->assign('count', $count);

        return $this->fetch();
    }

    /**
     * 修改邀请设置
     */
	public function addInviteSetting(Request $request)
    {
        $CodeTypeList = new GemapayCodeTypeModel();
        $codeTypeLists = Db::name('gemapay_code_type')->select();
        if ($request->isPost()) {
            $setting = [];
            foreach ($codeTypeLists as $type)
            {
                if($request->param('type_'.$type["id"]))
                {
                    $points = $request->param('type_'.$type["id"]);
                    if($points>500)
                    {
                        $this->error('发布失败，费率过大');
                    }
                } else {
                    $points = 0;
                }
                $setting[$type["id"]] = $points;
            }

            $Setting = DB::name('user_invite_setting');
            $data["invite_setting"] = json_encode($setting);
            $data["user_id"]        = 0;
            $data["code"]           = strrand(9);
            $data["admin_id"]       = $this->admin_id;
            $data["create_time"]    = time();
            $re = $Setting->insert($data);
            if($re){
                $this->success('发布成功', url('InviteSetting/index'));
            }else{
                $this->error('发布失败');
            }
        } else {
            $this->assign('type_lists',$codeTypeLists);
            $this->assign('act',url('InviteSetting/addInviteSetting'));
			return $this->fetch('addSetting');
        }
    }
	
	
	public function editInviteSetting(Request $request)
    {
        //$codeTypeLists = $CodeTypeList->getAllType();
        $codeTypeLists = Db::name('gemapay_code_type')->select();
        $Setting = DB::name('user_invite_setting');
        $setting = $Setting->find($request->param('get.id'));

        if ($request->isPost()) {

            $setting = [];
            foreach ($codeTypeLists as $type)
            {
                if($request->param('type_'.$type["id"]))
                {
                    $points = $request->param('type_'.$type["id"]);
                    if($points>500)
                    {
                        $this->error('发布失败，费率过大');
                    }
                }  else {
                    $points = 0;
                }
                $setting[$type["id"]] = $points;
            }
            $userInviteSetting = DB::name('user_invite_setting');
            $where["id"] = $request->param('id');
            $data["invite_setting"] = json_encode($setting);
            $re = $userInviteSetting->where($where)->update($data);
            if($re){
                $this->success('发布成功', url('InviteSetting/index'));
            }else{
                $this->error('发布失败');
            }

        } else {
            $this->assign('type_lists',$codeTypeLists);
            $this->assign('info',$setting);
            $data = json_decode($setting["invite_setting"]);
            $setting = [];
            foreach ($data as $key=>$d)
            {
                $setting[$key] = $d;
            }
            $this->assign('setting',$setting);
            $this->assign('act',url('InviteSetting/editInviteSetting'));
			return $this->fetch('editSetting');
        }
    }


}
