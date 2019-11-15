<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\logic\AdminLogic;
use think\Request;
use think\Db;


/**
 * 管理员银行卡
 *
 * Class MenuController
 * @package Admin\Controller
 */
class AdminBankController extends AdminController
{
    /**
     * 列表
     *
     */
    public function index(Request $request)
    {
        $adminLogic = new AdminLogic();
        $admin_id = $adminLogic->is_login();


        $map = array();
        if($admin_id != 1){
            $map['admin_id'] = $admin_id;
        }
        $account_name = $request->param('account_name');
        if($account_name){
            $map['account_name'] = array('like',"%{$account_name}%");
        }
        $account_num = $request->param('account_num/s');
        if($account_num){
            $map['account_num'] = array('like',"%{$account_num}%");
        }

        $listData = Db::name('admin_bank')->where($map)->paginate(15);

        $list = $listData->items();
        $count = $listData->count();
        $page = $listData->render();

        if($admin_id == 1 && $list){
            foreach ($list as $k=>$v){
                $list[$k]['nickname'] = Db::name('admin')->where('id', $v['admin_id'])->value('nickname');
            }
        }
        $this->assign('admin_id',$admin_id);
        $this->assign('list', $list);
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
        if ($request->isPost()) {
            $data = $request->post();
            if (empty($data['account_name'])) {
                $this->error('账号名称不能为空');
            }else if (empty($data['account_num'])) {
                $this->error('账号不能为空');
            }else if (empty($data['bank_name'])) {
                $this->error('银行名称不能为空');
            }
            $adminLogic = new AdminLogic();
            $data['admin_id'] = $adminLogic->is_login();
            if ($data) {
                $id = Db::name('admin_bank')->insert($data);
                if ($id) {
                    $this->success('新增成功', url('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error('新增失败了!');
            }
        } else {
           return $this->fetch('edit');
        }
    }


    /**
     * 编辑
     *
     */
    public function edit($id, Request $request)
    {

        if ($request->isPost()) {
            $data = $request->post();
            if (empty($data['account_name'])) {
                $this->error('账号名称不能为空');
            }else if (empty($data['account_num'])) {
                $this->error('账号不能为空');
            }else if (empty($data['bank_name'])) {
                $this->error('银行名称不能为空');
            }
            if ($data) {
                $i_admin_bank_id = $data['id'];
                unset($data['id']);
                $result = Db::name('admin_bank')->where('id', $i_admin_bank_id)->update($data);
                if ($result) {
                    $this->success('更新成功', url('index'));
                } else {
                    $this->error('更新失败,或数据没有更新');
                }
            } else {
                $this->error('更新失败!');
            }
        } else {
            $info = Db::name('admin_bank')->find($id);
            $this->assign('info', $info);

            return $this->fetch('edit');
        }
    }


    /**
     * 删除
     */
    public function del()
    {
        $id = input('id', 0, 'intval');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $adminLogic = new AdminLogic();
        $admin_id = $adminLogic->is_login();

        if($admin_id != 1){
            $info = Db::name('admin_bank')->find($id);
            if(!$info || $info['admin_id'] != $admin_id){
                $this->error('这不是您的银行账号，不能删除');
            }
        }
        $re = Db::name('admin_bank')->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
