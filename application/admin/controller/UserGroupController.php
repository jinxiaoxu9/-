<?php

namespace app\admin\Controller;

use app\admin\model\UserInviteSetting;
use app\admin\model\UserGroupModel;
use app\admin\model\GemapayCodeTypeModel;
use app\admin\logic\UserGroupLogic;
use think\Db;
use think\Request;

/**
 * 用户控制器
 *
 */
class UserGroupController extends AdminController
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
        $userGroupLogic = new UserGroupLogic();
        $data_list = $userGroupLogic->getCategory($data_list);
        $this->assign('list',$data_list);
        return $this->fetch();
    }


    public function setAllWorkStatus(Request $request)
    {
        $status = $request->param('work_status');
        $ids = $request->param('ids');
        $UserGroupModel   = new UserGroupModel();

        $UserGroupModel->startTrans();

        //dazu
        $data["work_status"] = $status;

            //xiaozhu
        $re = $UserGroupModel->where(array('admin_id'=>session("user_auth.uid")))->update($data);
        if($re===false)
        {
                $UserGroupModel->rollback();
                $this->error('失败');
        }

        $re = Db::name("user")->where(array('add_admin_id'=>session("user_auth.uid")))->update($data);
        if($re===false)
        {
            $UserGroupModel->rollback();
            $this->error('失败');
        }
        $UserGroupModel->commit();

        $this->success('成功', url('UserGroup/index'));


    }



    public function setWorkStatus(Request $request)
    {
        $status = $request->param('work_status');
        $ids = $request->param('ids');
        $UserGroupModel   = new UserGroupModel();
        $userGroup = $UserGroupModel->find($ids);

        $UserGroupModel->startTrans();
        //dazu
        $data["work_status"] = $status;
        if($userGroup['parentid'] ==0)
        {
            //dazu
            //dazugengxin
            $re = $UserGroupModel->where(array('id'=>$ids))->update($data);
            if(!$re){
                $UserGroupModel->rollback();
                $this->error('失败');
            }
            //xiaozugenx
            $re = $UserGroupModel->where(array('parentid'=>$ids))->update($data);
            if($re===false){
                $UserGroupModel->rollback();
                $this->error('失败');
            }

            $list = $UserGroupModel->where(array('parentid'=>$ids))->select()->toArray();

            $t = [];
            foreach ($list as $l)
            {
                $t[]= $l['id'];
            }

            if(!empty($t))
            {
                $where2['group_id'] = array('in',$t);
                $re = Db::name("user")->where($where2)->update($data);

                if($re===false)
                {
                    $UserGroupModel->rollback();
                    $this->error('失败');
                }
            }

            $UserGroupModel->commit();
            $this->success('成功', url('UserGroup/index'));
        } else {
            //xiaozhu
            $re = $UserGroupModel->where(array('id'=>$ids))->update($data);
            if($re===false){
                $UserGroupModel->rollback();
                $this->error('失败');
            }

            $re = Db::name("user")->where(array('group_id'=>$ids))->update($data);
            if($re===false)
            {
                $UserGroupModel->rollback();
                $this->error('失败!');
            }
            $UserGroupModel->commit();
            $this->success('成功', url('UserGroup/index'));
        }
    }

    /**
     * 修改邀请设置
     */
	public function addGroup(Request $request)
    {
        $UserGroupModel   = new UserGroupModel();
        $levelOneList = $UserGroupModel->getLevelList(session("user_auth.uid"),1);
        if ($request->isPost()) {
            $err = $this->checkValid($request, false);
            if($err) {
                return $err;
            }
            $data["bouns_points"] = $request->param('bouns_points');
            $data["note"]        = $request->param('note');
            $data["name"]      = $request->param('name');
            if($request->param('parentid') == '')
            {
                $parentid = 0;
                $level = 1;
            }
            else
            {
                $parentid = $request->param('parentid');
                $info =$UserGroupModel->find($parentid);
                $level= $info['level'] + 1;
            }
            $data["work_status"]    = UserGroupModel::STATUS_NOT_WORK;
            $data["level"]    = $level;
            $data["parentid"]    = $parentid;
            $data["admin_id"] = session("user_auth.uid");
            $re = $UserGroupModel->save($data);
            if($re){
                $this->success('发布成功', url('UserGroup/index'));
            }else{
                $this->error('发布失败');
            }
        } else {
            $this->assign('data_lists',$levelOneList);
			return $this->fetch('addGroup');
        }
    }

    /**
     * @param $request
     * @param bool $methon
     * @return string
     */
    protected function checkValid($request, $methon= false)
    {
        if(intval($request->param('id')) == 0 && $methon){

            $this->error('小组名字不能为空,发布失败,');die();
        }
        if($request->param('name') == ''){
            $this->error('小组名字不能为空,发布失败,');die();
        }
        if($request->param('bouns_points') == '') {

            $this->error('小组费率不能为空,发布失败');die();
        }
        if($request->param('bouns_points')>1000){

            $this->error('小组费率error,发布失败');die();
        }
        if($request->param('note') == '') {
            $this->error('小组备注不能为空,发布失败');die();
        }
        return '';
    }
	
	public function editGroup(Request $request)
    {
        $UserGroupModel   = new UserGroupModel();
        $levelOneList = $UserGroupModel->getLevelList(session("user_auth.uid"),1);
        if ($request->isPost()) {
            $err = $this->checkValid($request, true);
            if($err) {
                return $err;
            }
            $data["bouns_points"] = $request->param('bouns_points');
            $data["note"]        = $request->param('note');
            $data["name"]      = $request->param('name');
            $data["parentid"]    = $request->param('parentid');
            $where['id'] = intval($request->param('id'));
            //dump($request->post());exit();
            $re = $UserGroupModel->where($where)->update($data);
            if($re){
                $this->success('发布成功', url('UserGroup/index'));
            }else{
                $this->error('发布失败');
            }
        } else {
            $id = $request->param('id');
            $info =$UserGroupModel->find($id);
            $this->assign('info',$info);
            if($info["parentid"] == 0) {
                $levelOneList = [];
            }
            $this->assign('data_lists',$levelOneList);
            $this->assign('act', url('editGroup'));

            return $this->fetch('editGroup');
        }
    }


    /**
     * 删除分组
     */
    public function delGroup(Request $request)
    {
        $id = intval($request->param('id'));
        $nextCount = Db::name('user_group')->where(['parentid'=>$id])->count();
        if($nextCount){
            $this->error('当前用户组别存在小组,请先删除小组');
        }
        $groupUsersCount = Db::name('user')->where(['group_id'=>$id])->count();
        if($groupUsersCount){
            $this->error('当前用户组别存在成员,不可删除');
        }
        if(empty($id))
        {
            $this->error('不可删除');
        }
        $re = Db::name('user_group')->where(['id'=>$id])->delete();
        if($re){
            return '删除成功';
            //$this->success('删除成功', url('UserGroup/index'));
        }else{
            $this->error('删除失败');
        }
    }

    /** 加入分组
     * @param Request $request
     * @return mixed
     */
    public function joinGroup(Request $request)
    {
        if ($request->isPost()) {
            $data['group_id'] = $request->param('group_id');
            $where["userid"]= $request->param('userid');

            $re = Db::name("user")->where($where)->update($data);
            if($re){
                $this->success('成功', url('User/index'));
            }else{
                $this->error('失败');
            }
        }
        $info = Db::name("user")->find($request->param('userid'));
        $UserGroupModel   = new UserGroupModel();
        $levelList = $UserGroupModel->getLevelList(session("user_auth.uid"),2);
        $this->assign('data_lists',$levelList);
        $this->assign('info',$info);
        $this->assign('act', url('joinGroup'));
        $this->assign('userid',$request->param('userid'));

        return $this->fetch('joinGroup');
    }
}
