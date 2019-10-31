<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Think\Page;


/**
 * 菜单权限
 *
 * Class MenuController
 * @package Admin\Controller
 */
class Menu extends Admin
{
    /**
     * 列表
     *
     */
    public function index()
    {
        $menuobj = M('menu');
        $condition = [
            'level'=>1
        ];
        // 左侧菜单一级分类
        $groups = $menuobj->field('id,name')->where($condition)->select();
        $map = array();
        $pid = I('get.pid/d');
        if(!empty($pid)){
            $map['m.pid'] = $pid;
            $this->assign('pid', $pid);
        }
        $name = I('get.name/s');
        if($name){
            $map['m.name'] = array('like',"%{$name}%");
        }
        $count = $menuobj->alias('m')->where($map)->count();
        $p = getpagee($count, 15);
        $list = $menuobj->alias('m')->field('m.*,m1.name as pname')->where($map)->join("left join __MENU__ as m1 on m.pid = m1.id")->limit($p->firstRow, $p->listRows)->select();
        $this->assign('groups', $groups);
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $p->show());
        $this->display();
    }


    /**
     * 新增
     *
     */
    public function add()
    {
        if (IS_POST) {
            $table = M('menu');
            $data = I('post.');
            $data['sort'] = intval($data['sort']);
            if (empty($data['name'])) {
                $this->error('菜单名称不能为空');
            }
            $info = D('Admin/menu')->getInfoByPid($data['pid']);
            $data['gid'] = $info['gid'];
            $data['level'] = $info['level'];
            if ($data) {
                $id = $table->add($data);
                if ($id) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($table->getError());
            }
        } else {
            $menu_list = D('Admin/menu')->getSelectMenu();
            $this->assign('menu_list',$menu_list);
            $this->display('edit');
        }
    }


    /**
     * 编辑
     *
     */
    public function edit($id)
    {
        $table = M('menu');
        if (IS_POST) {
            $data = I('post.');
            if (empty($data['name'])) {
                $this->error('菜单名称不能为空');
            }
            $data['sort'] = intval($data['sort']);
            $info = D('Admin/menu')->getInfoByPid($data['pid']);
            $data['gid'] = $info['gid'];
            $data['level'] = $info['level'];
            if ($data) {
                $result = $table
                    ->save($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败', $table->getError());
                }
            } else {
                $this->error($table->getError());
            }
        } else {
            $info = $table->find($id);
            $this->assign('info', $info);
            $menu_list = D('Admin/menu')->getSelectMenu();
            $this->assign('menu_list',$menu_list);
            $this->display('edit');
        }
    }


    /**
     * 删除
     */
    public function del()
    {
        $id = I('get.id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $table = M('menu');
        $count = $table->where(array('pid'=>$id))->count();
        if($count > 0){
            $this->error('当前分类下存在菜单不可删除');
        }
        $re = $table->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
