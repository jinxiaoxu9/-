<?php

namespace app\admin\controller;

use think\Request;
use think\Db;

class UserMessageController extends AdminController
{
    /**
     * 首页  只提供编辑功能
     */
    public function index(Request $request)
    {
        $listData = Db::name('user_message')
            ->order('is_read asc,id desc')
            ->paginate(15);
        $list = $listData->items();
        //分页
        $page = $listData->render();

        $this->assign('list', $list);
        $this->assign('page', $page);

        return $this->fetch();
    }

    /**
     * 首页  只提供编辑功能
     */
    public function add(Request $request)
    {
        $this->assign('act', url('add'));
        if ($request->isPost()) {
            $data = $request->post();
            if ($data['mobile'] == '') {
                $this->error('手机号不能为空');
            }
            /*if (!is_numeric($data['mobile']) || !preg_match('#^1[34578]\d{9}$#', $data['mobile'])) {
                $this->error('手机号不对');
            }*/

            //通过手机号找user_id
            $map = array();
            $map['mobile|account'] = array('eq', $data['mobile'], 'or');
            $user_id = Db::name('user')->where($map)->value('userid');
            if(!$user_id) {
                $this->error('找不到用户，或是用户未注册');
            }
            if ($data['title'] == '') {
                $this->error('标题不能为空');
            }
            if ($data['content'] == '') {
                $this->error('内容不能为空');
            }
            $data['add_time'] = time();
            $data['read_time'] = $data['is_read'] = 0;
            unset($data['mobile']);
            Db::name('user_message')->insert($data);
            $this->success('新增成功', url('index'));
        }
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        $id = intval($request->param('id'));
        if(!$id) {
            $this->redirect(url('index'));
        }

        $info = Db::name('user_message')->where('id', $id)->find();
        $info['pid_account'] = Db::name('user')->where('userid', $info['user_id'])->value('account');
        $this->assign('info', $info);
        return $this->fetch();
    }
}
