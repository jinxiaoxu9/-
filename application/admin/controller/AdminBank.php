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
 * 管理员银行卡
 *
 * Class MenuController
 * @package Admin\Controller
 */
class AdminBank extends Admin
{
    /**
     * 列表
     *
     */
    public function index()
    {
        $admin_id = D('Admin/Manage')->is_login();
        $admin_bank = M('admin_bank');
        $map = array();
        if($admin_id != 1){
            $map['admin_id'] = $admin_id;
        }
        $account_name = I('get.account_name/s');
        if($account_name){
            $map['account_name'] = array('like',"%{$account_name}%");
        }
        $account_num = I('get.account_num/s');
        if($account_num){
            $map['account_num'] = array('like',"%{$account_num}%");
        }
        $count = $admin_bank->where($map)->count();
        $p = getpagee($count, 15);
        $list = $admin_bank->where($map)->limit($p->firstRow, $p->listRows)->select();
        if($admin_id == 1 && $list){
            foreach ($list as $k=>$v){
                $list[$k]['nickname'] = M('admin')->getFieldById($v['admin_id'],'nickname');
            }
        }
        $this->assign('admin_id',$admin_id);
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
            $table = M('admin_bank');
            $data = I('post.');
            if (empty($data['account_name'])) {
                $this->error('账号名称不能为空');
            }else if (empty($data['account_num'])) {
                $this->error('账号不能为空');
            }else if (empty($data['bank_name'])) {
                $this->error('银行名称不能为空');
            }
            $data['admin_id'] = D('Admin/Manage')->is_login();
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
            $this->display('edit');
        }
    }


    /**
     * 编辑
     *
     */
    public function edit($id)
    {
        $table = M('admin_bank');
        if (IS_POST) {
            $data = I('post.');
            if (empty($data['account_name'])) {
                $this->error('账号名称不能为空');
            }else if (empty($data['account_num'])) {
                $this->error('账号不能为空');
            }else if (empty($data['bank_name'])) {
                $this->error('银行名称不能为空');
            }
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
        $table = M('admin_bank');
        $admin_id = D('Admin/Manage')->is_login();
        if($admin_id != 1){
            $info = $table->find($id);
            if(!$info || $info['admin_id'] != $admin_id){
                $this->error('这不是您的银行账号，不能删除');
            }
        }
        $re = $table->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
