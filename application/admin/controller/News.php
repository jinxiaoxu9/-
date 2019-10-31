<?php

namespace app\admin;

use Think\Page;

/**
 * 用户控制器
 *
 */
class News extends AdminController
{


    /**
     * 用户列表
     *
     */
    public function index()
    {
        // 获取所有用户
        $map['status'] = array('egt', '0'); // 禁用和正常状态
        $user_object   = M('news');
        //分页
        $p=getpage($user_object,$map,10);
        $page=$p->show();  

        $data_list     = $user_object
            ->where($map)
            ->order('id desc')
            ->select();
       

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    /**
     * 新增用户
     *
     */
    public function add()
    {
        if (IS_POST) {

            $user_object = D('news');
            $data        = I('post.');
            if(empty($data['title'])){
              $this->error('标题不能为空');  
            }
            $data['uid_str']        = '0,';
            $data['create_time']        = time();
            $data['status']         =1;
            if ($data) {
                $id = $user_object->add($data);
                if ($id) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
                $this->display('edit');
        }
    }
	
	//九星快讯
	public function flash(){
		$map =''; // 禁用和正常状态
        $user_object   = M('flash');
        //分页
        $p=getpage($user_object,$map,10);
        $page=$p->show();  

        $data_list     = $user_object
            ->where($map)
            ->order('id desc')
            ->select();
       

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
		
		$this->display();
	}
	

	
	public function addflash()
    {
        if (IS_POST) {
			
            $flash = M('flash');
            $content = trim(I('post.content'));
            $id = trim(I('post.id'));
			if( $content == ''){
				 $this->error('快讯内容不能为空');
			}
			
			if($id != ''){
				$flist = $flash->where(array('id'=>$id))->find();
				if(empty($flist)){
					$this->error('快讯不存在');exit;
				}
				
				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->where(array('id'=>$id))->save($f_data);
				if($re){
					$this->success('更新成功', U('flash'));
				}else{
					$this->error('更新失败');
				}
				
			}else{
				
				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->add($f_data);
				if($re){
					$this->success('发布成功', U('flash'));
				}else{
					$this->error('发布失败');
				}
			}

        } else {
			$this->display('editflash');
        }
    }
	
	
	public function editflash()
    {
        if (IS_POST) {
			
            $flash = M('flash');
            $content = trim(I('post.content'));
            $id = trim(I('post.id'));
			if( $content == ''){
				 $this->error('快讯内容不能为空');
			}
			
			if($id != ''){
				$flist = $flash->where(array('id'=>$id))->find();
				if(empty($flist)){
					$this->error('快讯不存在');exit;
				}
				
				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->where(array('id'=>$id))->save($f_data);
				if($re){
					$this->success('更新成功', U('flash'));
				}else{
					$this->error('更新失败');
				}
				
			}else{
				
				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->add($f_data);
				if($re){
					$this->success('发布成功', U('flash'));
				}else{
					$this->error('发布失败');
				}
			}

        } else {

			$this->display('editflash');
        }
    }
	
	public function delflash(){
		if($_GET){
			
			$id = trim(I('get.id'));
			$flist = M('flash')->where(array('id'=>$id))->find();
			if(!empty($flist)){
				$re = M('flash')->where(array('id'=>$id))->delete();
				if($re){
					$this->success('删除成功', U('flash'));
				}else{
					$this->error('删除失败');
				}
			}else{
				$this->error('该快讯不存在');
			}
			
			
		}else{
			$this->error('网络错误');
		}
	}

    /**
     * 编辑用户
     *
     */
    public function edit($id)
    {
        if (IS_POST) {
            // 提交数据
            $user_object = D('news');
            $data        = I('post.');
            $data['create_time'] = time();
            if(empty($data['title'])){
              $this->error('标题不能为空');  
            }
          //  var_dump($data);exit;
            if ($data) {
                $result = $user_object
                    ->save($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 获取账号信息
            $info = D('news')->find($id);
            $this->assign('info',$info);
            $this->display();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     *
     */
    public function setStatus($model = CONTROLLER_NAME)
    {
        $ids = I('request.ids');
        parent::setStatus($model);
    }
}
