<?php

namespace Admin\Controller;

use Admin\Model\UserInviteSetting;
use Gemapay\Model\GemapayCodeTypeModel;
/**
 * 用户控制器
 *
 */
class InviteSetting extends Admin
{
    /**
     * 总后台邀请设置
     */
    public function index()
    {
        $map = [];
        // 获取所有用户
        if(session("user_auth.uid")!=1)
        {
            $map['admin_id'] = session("user_auth.uid"); //超级管理员　admin 添加
            $map['user_id'] = 0;
        }

        $inviteSetting   = M('user_invite_setting');

        $InviteSettingModel = new UserInviteSetting();
        //分页
        $p=getpage($inviteSetting,$map,10);
        $page=$p->show();

        $fields=[
            "i.*",
            "a.nickname"
        ];
        $data_list     = $inviteSetting->alias('i')
            ->where($map)
            ->field($fields)
            ->order('i.id desc')
            ->join('ysk_admin a ON a.id=i.admin_id',"LEFT")
            ->select();

        $CodeTypeList = new GemapayCodeTypeModel();
        $codeTypeLists = $CodeTypeList->getAllType();

        //添加描述
        foreach ($data_list as $key => $data)
        {
            $data_list[$key]["desc"] = $InviteSettingModel->getInviteDesc($data["invite_setting"],$codeTypeLists);
            $data_list[$key]["invite_url"] = $InviteSettingModel->getInviteLink($data["code"]);
        }

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    /**
     * 修改邀请设置
     */
	public function addInviteSetting()
    {
        $CodeTypeList = new GemapayCodeTypeModel();
        $codeTypeLists = $CodeTypeList->getAllType();
        if (IS_POST) {
            $setting = [];
            foreach ($codeTypeLists as $type)
            {
                if(I('post.type_'.$type["id"]))
                {
                    $points = I('post.type_'.$type["id"]);
                    if($points>500)
                    {
                        $this->error('发布失败，费率过大');
                    }
                }
                else
                {
                    $points = 0;
                }
                $setting[$type["id"]] = $points;
            }

            $Setting = M('user_invite_setting');
            $data["invite_setting"] = json_encode($setting);
            $data["user_id"]        = 0;
            $data["code"]        = strrand(9);
            $data["admin_id"]       = session("user_auth.uid");
            $data["create_time"]    = time();
            $re = $Setting->add($data);
            if($re){
                $this->success('发布成功', U('InviteSetting/index'));
            }else{
                $this->error('发布失败');
            }
        } else {
            $this->assign('type_lists',$codeTypeLists);
			$this->display('addSetting');
        }
    }
	
	
	public function editInviteSetting()
    {
        $CodeTypeList = new GemapayCodeTypeModel();
        $codeTypeLists = $CodeTypeList->getAllType();
        $Setting = M('user_invite_setting');
        $setting = $Setting->find(I('get.id'));

        if (IS_POST) {

            $setting = [];
            foreach ($codeTypeLists as $type)
            {
                if(I('post.type_'.$type["id"]))
                {
                    $points = I('post.type_'.$type["id"]);
                    if($points>500)
                    {
                        $this->error('发布失败，费率过大');
                    }
                }
                else
                {
                    $points = 0;
                }
                $setting[$type["id"]] = $points;
            }
            $Setting = M('user_invite_setting');
            $where["id"] = I('post.id');
            $data["invite_setting"] = json_encode($setting);
            $re = $Setting->where($where)->save($data);
            if($re){
                $this->success('发布成功', U('InviteSetting/index'));
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
			$this->display('editSetting');
        }
    }


}
