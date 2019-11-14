<?php

namespace app\admin\controller;

use think\Request;
use think\Db;


/**
 * 个码收款二维码类型
 * Class CodeType
 * @package Admin\Controller
 */
class CodeTypeController extends AdminController
{


    /**
     * 列表
     *
     */
    public function index()
    {

        $listData = Db::name('gemapay_code_type')
            ->order('sort asc,id desc')
            ->paginate(10);
        $list = $listData->items();
        //分页
        $page = $listData->render();

        $this->assign('list', $list);
        $this->assign('page', $page);

        return $this->fetch('code_type/index');
    }


    /**
     * 新增
     *
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            //原基础上修改  直接http 提交  todo 后面再修改为ajax
            $table = Db::name('gemapay_code_type');
            $data = $request->post();
            $fileInfo = Db::name('Upload')->upload();
            if ($fileInfo['error'] != 0) {
                $this->error($fileInfo['message']);
            }
            if (empty($data['type_name'])) {
                $this->error('二维码类型名称不能为空');
            }
            $data['create_time'] = time();
            $data['type_logo'] = $fileInfo['path'];
            if ($data) {
                $id = $table->insert($data);
                if ($id) {
                    $this->success('新增成功', url('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error('新增失败!');
            }
        } else {
            $this->assign('act', url('add'));
            return $this->fetch('code_type/edit');
        }
    }


    /**
     * 编辑
     *
     */
    public function edit($id, Request $request)
    {
        $table = Db::name('gemapay_code_type');
        if ($request->isPost()) {
            $data = $request->post();
            if (!empty($_FILES['type_logo']['tmp_name'])) {
                unset($data['original_type_logo']);
                $fileInfo = D('Upload')->upload();
                if ($fileInfo['error'] != 0) {
                    $this->error($fileInfo['message']);
                }
                $data['type_logo'] = $fileInfo['path'];
            } else {
                $data['type_logo'] = $data['original_type_logo'];
            }
            if (empty($data['type_name'])) {
                $this->error('二维码类型名称不能为空');
            }

            if ($data) {
                unset($data['original_type_logo']);
                $result = $table
                    ->update($data);
                if ($result) {
                    $this->success('更新成功', url('index'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error('更新失败!');
            }
        } else {
            $info = $table->find($id);
            $this->assign('info', $info);
            $this->assign('act', url('edit'));
            return $this->fetch('code_type/edit');
        }
    }


    /**
     * 删除
     */
    public function del(Request $request)
    {
        $id = $request->param('id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        //直接干掉
        $table = Db::name('gemapay_code_type');
        $re = $table->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }


}
