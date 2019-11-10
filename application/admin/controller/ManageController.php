<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace app\admin\Controller;

use think\Request;
use think\Db;

/**
 * 用户控制器
 * @author jry <bbs.sasadown.cn>
 */
class ManageController extends AdminController
{

    /**
     * 用户列表
     * @author jry <bbs.sasadown.cn>
     */
    public function index(Request $request)
    {
        // 搜索
        $keyword                                  = $request->param('keyword', '', 'string');
        /*$condition                                = array('like', '%' . $keyword . '%');
        $map['a.id|a.username|a.nickname'] = array(
            $condition,
            $condition,
            $condition,
            '_multi' => true,
        );*/
        //$map = 'CONCAT(`a.id`,`a.username`,`a.nickname`) LIKE "%' . $keyword . '%"';
        $map['CONCAT(a.`id`, a.`username`, a.`nickname`)'] = array('like', '%' . $keyword . '%');
        // 获取所有用户
        $map['a.status'] = array('egt', '0'); // 禁用和正常状态
        $user_object   = Db::name('admin a')->join('ysk_group b', 'a.auth_id=b.id','LEFT');

        $listData     = $user_object
            ->field('a.*,b.title')
            ->where($map)
            ->order('a.id asc')
            ->paginate(10);

        $list = $listData->items();
        $page = $listData->render();

        $this->assign('list',$list);
        $this->assign('page',$page);

        return $this->fetch();
    }

    /**
     * 新增用户
     * @author jry <bbs.sasadown.cn>
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {

            $data        = $request->post();
            if ($data) {
                $id = Db::name('Manage')->insert($data);
                if ($id) {
                    $this->success('新增成功', url('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error('新增失败');
            }
        } else {
            $role=Db::name('Group')->where(array('status'=>array('neq',-1)))->field('id,title')->order('id')->select();
            $this->assign('role',$role);
            return $this->fetch('add');
        }
    }


    public function setWorkStatus(Request $request)
    {
        $status = $request->param('work_status');
        $id = $request->param('id');

        //dazu
        $where["id"] = $id;
        $data["work_status"] = $status;
        $result = Db::name("admin")->where($where)->update($data);

        if ($result) {
            $this->success('更新成功', url('index'));
        } else {
            $this->error('更新失败');
        }

    }

    /**
     * 编辑用户
     * @author jry <bbs.sasadown.cn>
     */
    public function edit($id, Request $request)
    {
        if ($request->isPost()) {

            // 提交数据
            $user_object = DB::name('Manage');
            $data        = $request->post();
            // 密码为空表示不修改密码
            if(!$_POST['password'])
                unset($data['password']);
            if ($data) {
                $result = $user_object
                    //->field('id,nickname,username,password,mobile,auth_id,update_time')
                    ->update($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            //角色信息
            $role= DB::name('Group')->field('id,title')->order('id')->select();
            $this->assign('role',$role);

            // 获取账号信息
            $info = DB::name('Manage')->find($id);
            unset($info['password']);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <bbs.sasadown.cn>
     */
    public function setStatus($model = '', $script = false)
    {
        $request = Request::instance();
        $ids = $request->param('ids');
        if (is_array($ids)) {
            if (in_array('1', $ids)) {
                $this->error('超级管理员不允许操作');
            }
        } else {
            if ($ids === '1') {
                $this->error('超级管理员不允许操作');
            }
        }
        parent::setStatus($model, $script);
    }
}
