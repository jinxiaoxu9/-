<?php

namespace Admin\Controller;

use Think\Page;

/**
 * 个码收款二维码类型
 * Class CodeType
 * @package Admin\Controller
 */
class CodeType extends Admin
{


    /**
     * 列表
     *
     */
    public function index()
    {
        $table = M('gemapay_code_type');
        //分页
        $p = getpage($table, 1, 10);
        $page = $p->show();

        $data_list = $table
            ->order('sort asc,id desc')
            ->select();


        $this->assign('list', $data_list);
        $this->assign('table_data_page', $page);
        $this->display('code_type/index');
    }


    /**
     * 新增
     *
     */
    public function add()
    {
        if (IS_POST) {
            //原基础上修改  直接http 提交  todo 后面再修改为ajax
            $table = M('gemapay_code_type');
            $data = I('post.');
            $fileInfo = D('Upload')->upload();
            if ($fileInfo['error'] != 0) {
                $this->error($fileInfo['message']);
            }
            if (empty($data['type_name'])) {
                $this->error('二维码类型名称不能为空');
            }
            $data['create_time'] = time();
            $data['type_logo'] = $fileInfo['path'];
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
            $this->display('code_type/edit');
        }
    }


    /**
     * 编辑
     *
     */
    public function edit($id)
    {
        $table = M('gemapay_code_type');
        if (IS_POST) {
            $data = I('post.');
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
            $this->display('code_type/edit');
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
        //直接干掉
        $table = M('gemapay_code_type');
        $re = $table->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }


}
