<?php

namespace Admin\Controller;

use Admin\Model\InviteSettingModel;
use Admin\Model\UserGroupModel;
use Gemapay\Model\GemapayCodeTypeModel;
use Think\Db;

/**
 * 用户控制器
 *
 */
class UserGroup extends Admin
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
        }

        $UserGroupModel   = new UserGroupModel();

        //分页
        $data_list     = $UserGroupModel
            ->where($map)
            ->select();
        foreach ($data_list as $key=>$data)
        {
            if($data['level']==1)
            {
                $data_list[$key]["level_name"]="大组长";
            }

            if($data['level']==2)
            {
                $data_list[$key]["level_name"]="小组长";
            }

            if($data['work_status'] == UserGroupModel::STATUS_NOT_WORK)
            {
                $data_list[$key]["work_status_name"]="<a style='color: red'>停工中</a>";
            }
            if($data['work_status'] == UserGroupModel::STATUS_WORK)
            {
                $data_list[$key]["work_status_name"]="<a style='color: green'>工作中</a>";
            }
        }
        $data_list=getCategory($data_list);
        $this->assign('list',$data_list);
        $this->display();
    }


    public function setAllWorkStatus()
    {
        $status = I('get.work_status');
        $ids = I('get.ids');
        $UserGroupModel   = new UserGroupModel();

        $UserGroupModel->startTrans();

        //dazu
        $data["work_status"] = $status;

            //xiaozhu
        $re = $UserGroupModel->where(array('admin_id'=>session("user_auth.uid")))->save($data);
        if($re===false)
        {
                $UserGroupModel->rollback();
                $this->error('失败');
        }

        $re = M("user")->where(array('add_admin_id'=>session("user_auth.uid")))->save($data);
        if($re===false)
        {
            $UserGroupModel->rollback();
            $this->error('失败');
        }
        $UserGroupModel->commit();
        $this->success('成功', U('UserGroup/index'));


    }



    public function setWorkStatus()
    {
        $status = I('get.work_status');
        $ids = I('get.ids');
        $UserGroupModel   = new UserGroupModel();
        $userGroup = $UserGroupModel->find($ids);

        $UserGroupModel->startTrans();
        //dazu
        $data["work_status"] = $status;
        if($userGroup['parentid'] ==0)
        {
            //dazu
            //dazugengxin
            $re = $UserGroupModel->where(array('id'=>$ids))->save($data);
            if(!$re){
                $UserGroupModel->rollback();
                $this->error('失败');
            }
            //xiaozugenx
            $re = $UserGroupModel->where(array('parentid'=>$ids))->save($data);
            if($re===false){
                $UserGroupModel->rollback();
                $this->error('失败');
            }

            $list = $UserGroupModel->where(array('parentid'=>$ids))->select();

            $t = [];
            foreach ($list as $l)
            {
                $t[]= $l['id'];
            }

            if(!empty($t))
            {
                $where2['group_id'] = array('in',$t);
                $re = M("user")->where($where2)->save($data);

                if($re===false)
                {
                    $UserGroupModel->rollback();
                    $this->error('失败');
                }
            }

            $UserGroupModel->commit();
            $this->success('成功', U('UserGroup/index'));
        }
        else
        {
            //xiaozhu
            $re = $UserGroupModel->where(array('id'=>$ids))->save($data);
            if($re===false){
                $UserGroupModel->rollback();
                $this->error('失败');
            }

            $re = M("user")->where(array('group_id'=>$ids))->save($data);
            if($re===false)
            {
                $UserGroupModel->rollback();
                $this->error('失败');
            }
            $UserGroupModel->commit();
            $this->success('成功', U('UserGroup/index'));

        }


    }

    /**
     * 修改邀请设置
     */
	public function addGroup()
    {
        $UserGroupModel   = new UserGroupModel();
        $levelOneList = $UserGroupModel->getLevelList(session("user_auth.uid"),1);
        if (IS_POST) {
            if(empty(I('post.name')))
            {
                $this->error('小组名字不能为空,发布失败,');die();
            }
            if(empty(I('post.bouns_points')))
            {
                $this->error('小组费率不能为空,发布失败');die();
            }
            if(I('post.bouns_points')>1000)
            {
                $this->error('小组费率error,发布失败');die();
            }
            if(empty(I('post.note')))
            {
                $this->error('小组备注不能为空,发布失败');die();
            }

            $data["bouns_points"] = I('post.bouns_points');
            $data["note"]        = I('post.note');
            $data["name"]      = I('post.name');
            if(empty(I('post.parentid')))
            {
                $parentid = 0;
                $level = 1;
            }
            else
            {
                $parentid = I('post.parentid');
                $info =$UserGroupModel->find($parentid);
                $level= $info['level'] + 1;
            }
            $data["work_status"]    = UserGroupModel::STATUS_NOT_WORK;
            $data["level"]    = $level;
            $data["parentid"]    = $parentid;
            $data["admin_id"] = session("user_auth.uid");
            $re = $UserGroupModel->add($data);
            if($re){
                $this->success('发布成功', U('UserGroup/index'));
            }else{
                $this->error('发布失败');
            }
        } else {
            $this->assign('data_lists',$levelOneList);
			$this->display('addGroup');
        }
    }
	
	
	public function editGroup()
    {
        $UserGroupModel   = new UserGroupModel();
        $levelOneList = $UserGroupModel->getLevelList(session("user_auth.uid"),1);
        if (IS_POST) {
            if(empty(I('post.name')))
            {
                $this->error('小组名字不能为空,发布失败,');die();
            }
            if(empty(I('post.bouns_points')))
            {
                $this->error('小组费率不能为空,发布失败');die();
            }
            if(I('post.bouns_points')>1000)
            {
                $this->error('小组费率error,发布失败');die();
            }
            if(empty(I('post.note')))
            {
                $this->error('小组备注不能为空,发布失败');die();
            }

            $data["bouns_points"] = I('post.bouns_points');
            $data["note"]        = I('post.note');
            $data["name"]      = I('post.name');

            $data["parentid"]    = I('post.parentid');
            $where['id'] = I('post.id');
            $re = $UserGroupModel->where($where)->save($data);
            if($re){
                $this->success('发布成功', U('UserGroup/index'));
            }else{
                $this->error('发布失败');
            }
        } else {
            $id = I('get.id');
            $info =$UserGroupModel->find($id);
            $this->assign('info',$info);
            if($info["parentid"] == 0)
            {
                $levelOneList = [];
            }
            $this->assign('data_lists',$levelOneList);
            $this->display('editGroup');
        }
    }


    /**
     * 删除分组
     */
    public function delGroup()
    {
        $id=I('id/d');
        $nextCount = M('user_group')->where(['parentid'=>$id])->count();
        if($nextCount){
            $this->error('当前用户组别存在小组,请先删除小组');
        }
        $groupUsersCount = M('user')->where(['group_id'=>$id])->count();
        if($groupUsersCount){
            $this->error('当前用户组别存在成员,不可删除');
        }
        if(empty($id))
        {
            $this->error('不可删除');
        }
        $re = M('user_group')->where(['id'=>$id])->delete();
        if($re){
            $this->success('删除成功', U('UserGroup/index'));
        }else{
            $this->error('删除失败');
        }
    }

    public function joinGroup()
    {
        if (IS_POST) {
            $data['group_id'] = I('post.group_id');
            $where["userid"]= I('post.userid');

            $re = M("user")->where($where)->save($data);
            if($re){
                $this->success('成功', U('User/index'));
            }else{
                $this->error('失败');
            }
        }
        $info = M("user")->find(I('get.userid'));
        $UserGroupModel   = new UserGroupModel();
        $levelList = $UserGroupModel->getLevelList(session("user_auth.uid"),2);
        $this->assign('data_lists',$levelList);
        $this->assign('info',$info);
        $this->assign('userid',I('get.userid'));
        $this->display('joinGroup');
    }
}
