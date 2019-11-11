<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\logic\TreeLogic;
use think\Request;
use think\Db;
use Util;

/**
 * 部门控制器
 * @author jry <bbs.sasadown.cn>
 */
class GroupController extends AdminController
{
    /**
     * 部门列表
     * @author jry <bbs.sasadown.cn>
     */
    public function index(Request $request)
    {
        // 搜索
        $keyword         = $request->param('keyword', '', 'string');
        /*$condition       = array('like', '%' . $keyword . '%');
        $map['id|title'] = array(
            $condition,
            $condition,
            '_multi' => true,
        );*/
        $map['CONCAT(`id`, `title`)'] = array('like', '%' . $keyword . '%');
        // 获取所有角色
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list     = DB::name('Group')
            ->where($map)
            ->order('sort asc, id asc')
            ->select();
        $this->assign('list',$data_list);

        return $this->fetch();
    }

    /**
     * 新增部门
     * @author jry <bbs.sasadown.cn>
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $group_object       = D('Group');
            $_POST['menu_auth'] = implode(',', $request->param('post.menu_auth'));
            $_POST['hylb'] = implode(',', $request->param('post.hylb'));
            $data               = $group_object->create();
            if ($data) {
                $id = $group_object->add($data);
                if ($id) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($group_object->getError());
            }
        } else {
            $all_module_menu_list=$this->getMenuTree();
            $this->assign('all_module_menu_list', $all_module_menu_list);

            return $this->fetch('add');
        }
    }

    /**
     * 编辑部门
     * @author jry <bbs.sasadown.cn>
     */
    public function edit($id, Request $request)
    {
        if ($request->isPost()) {
            $group_object       = DB::name('Group');
            $_POST['menu_auth'] = implode(',', $request->param('post.menu_auth'));
            $_POST['hylb'] = implode(',', $request->param('post.hylb'));
            $data               = $group_object->create();
            if ($data) {
                if ($group_object->save($data) !== false) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败');
                }


            } else {
                $this->error($group_object->getError());
            }
        } else {
           //获取角色信息
            $where['id']=$id;
            $info=Db::name('Group')->find($id);

            // 获取功能模块的后台菜单列表
            $all_module_menu_list=$this->getMenuTree();
            $this->assign('all_module_menu_list', $all_module_menu_list);
            $info['menu_auth']=explode(',', trim($info['menu_auth'],','));
            $hylb=explode(',', trim($info['hylb'],','));
            $this->assign('info', $info);
            $this->assign('hylb', $hylb);
            
            return $this->fetch('edit');
        }
    }

     // 获取功能模块的后台菜单列表
    private function getMenuTree(){
        $tree                 = new Tree();
        $all_module_menu_list = array();

        $con['status']     = 1;
        $menu=Db::name('Menu')->where($con)->order('sort asc,id asc')->select();
        $temp                               = $menu;
        $menu_list_item                     = $tree->list2tree($temp);
        return $menu_list_item;
    }


    /**
     * 设置一条或者多条数据的状态
     * @author jry <bbs.sasadown.cn>
     */
    public function setStatus($model = '', $script = false)
    {
        $request = Request::instance();
        $ids = $request->param('request.ids');
        if (is_array($ids)) {
            if (in_array('1', $ids)) {
                $this->error('超级管理员组不允许操作');
            }
        } else {
            if ($ids === '1') {
                $this->error('超级管理员组不允许操作');
            }
        }
        parent::setStatus($model);
    }
}
