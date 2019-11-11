<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace app\admin\Controller;

use app\admin\model\MenuModel;
use app\admin\model\MenuModel as Menu;
use app\admin\logic\MenuLogic;
use think\Request;
use think\Db;


/**
 * 菜单权限
 *
 * Class MenuController
 * @package Admin\Controller
 */
class MenuController extends AdminController
{
    /**
     * 列表
     *
     */
    public function index(Request $request)
    {
        $menuobj = new Menu();
        $condition = [
            'level'=>1
        ];
        // 左侧菜单一级分类
        $groups = Menu::where($condition)->field('id,name')->select();

        $map = array();
        $pid = $request->param('pid');
        if(!empty($pid)){
            $map['m.pid'] = $pid;
            $this->assign('pid', $pid);
        }
        $name = $request->param('name');
        if($name){
            $map['m.name'] = array('like',"%{$name}%");
        }
        $list = Db::table('ysk_menu')->alias('m')->field('m.*,m1.name as pname')->where($map)
            ->join("__MENU__ m1", 'm.pid = m1.id')
            ->paginate(15);

        $count = $list->total();
        // 获取分页显示
        $page = $list->render();

        $this->assign('groups', $groups);
        $this->assign('list', $list->items());
        $this->assign('count', $count);
        $this->assign('page', $page);
        return $this->fetch();
    }


    /**
     * 新增
     *
     */
    public function add(Request $request)
    {
        //return $this->success('新增成功', url('index'));
        $menuLogic = new MenuLogic();
        if ($request->isPost()) {
            $table = new Menu();
            $data = $request->post();
            $data['sort'] = intval($data['sort']);
            if (empty($data['name'])) {
                $this->error('菜单名称不能为空');
            }
            $info = $menuLogic->getInfoByPid($data['pid']);
            $data['gid'] = $info['gid'];
            $data['level'] = $info['level'];
            if ($data) {
                $id = $table->save($data);
                if ($id) {
                    $this->success('新增成功', url('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($table->getError());
            }
        } else {
            $menu_list = $menuLogic->getSelectMenu();
            $this->assign('menu_list',$menu_list);

            return $this->fetch('edit');
        }
    }


    /**
     * 编辑
     *
     */
    public function edit(Request $request)
    {
        $id = intval($request->param('id'));
        $table = Db::name('menu')->where('id', intval($id));
        $menuLogic = new MenuLogic();
        if ($request->isPost()) {
            $data = $request->post();

            if (empty($data['name'])) {
                $this->error('菜单名称不能为空');
            }
            $data['sort'] = intval($data['sort']);

            $info = $menuLogic->getInfoByPid($data['pid']);
            $data['gid'] = $info['gid'];
            $data['level'] = $info['level'];
            if ($data) {
               // dump($id);exit();
                unset($data['id']);
                $result = $table->update($data);
                if ($result) {
                    $this->success('更新成功', url('index'));
                } else {
                    $this->success('数据没有变更', url('index'));
                }
            } else {
                $this->error($table->getLastSql());
            }
        } else {
            $info = $table->find($id);
            $this->assign('info', $info);
            $menu_list = $menuLogic->getSelectMenu();
            $this->assign('menu_list',$menu_list);
            return $this->fetch('edit');
        }
    }


    /**
     * 删除
     */
    public function del(Request $request)
    {
        $id = intval($request->param('id'));
        if (empty($id)) {
            $this->error('参数错误');
        }
        $table = Db::name('menu');
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
