<?php

namespace app\admin\Controller;

use app\admin\model\NewsModel as News;
use think\Request;
use think\Db;


/**
 * 用户控制器
 *
 */
class NewsController extends AdminController
{
    /**
     * 用户列表
     *
     */
    public function index()
    {
        // 获取所有用户
        //$map['status'] = array('egt', '0'); // 禁用和正常状态
        $user_object   = Db::name('news');

        $data_list     = $user_object
            //->where($map)
            ->order('id desc')
            ->paginate(15);

        $count = $data_list->total();
        // 获取分页显示
        $page = $data_list->render();

        $this->assign('list',$data_list);
        $this->assign('page',$page);
        $this->assign('count', $count);
        return $this->fetch();
    }

    /**
     * 新增用户
     *
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $user_object = new News();

            $data        = $request->post();
            if(empty($data['title'])){
              $this->error('标题不能为空');  
            }
            $data['create_time']        = time();
            if ($data) {
                $id = $user_object->save($data);
                if ($id) {
                    $this->success('新增成功', url('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            $this->assign('act', url('add'));
            return $this->fetch('edit');
        }
    }
	
	//九星快讯
	public function flash(){
		$map =''; // 禁用和正常状态

        $data_list     = Db::name('flash')
            //->where($map)
            ->order('id desc')
            ->paginate(15);
        $count = $data_list->total();
        // 获取分页显示
        $page = $data_list->render();

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->assign('count', $count);

		return $this->fetch();
	}
	

	
	public function addflash(Request $request)
    {
        if ($request->isPost()) {
			
            $flash = Db::name('flash');
            $content = trim($request->param('content'));
            $id = trim($request->param('post.id'));
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
				$re = $flash->where(array('id'=>$id))->update($f_data);
				if($re){
					$this->success('更新成功', url('flash'));
				}else{
					$this->error('更新失败');
				}
			}else{
				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->save($f_data);
				if($re){
					$this->success('发布成功', url('flash'));
				}else{
					$this->error('发布失败');
				}
			}

        } else {
			return $this->fetch('editflash');
        }
    }
	
	
	public function editflash(Request $request)
    {
        if ($request->isPost()) {
			
            $flash = Db::name('flash');
            $content = trim($request->param('content'));
            $id = trim($request->param('id'));
			if( $content == ''){
				 $this->error('快讯内容不能为空');
			}
			
			if($id != ''){
				$flist = $flash->where(array('id'=>$id))->find();
				if(empty($flist)){
					$this->error('快讯不存在');
				}

				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->where(array('id'=>$id))->update($f_data);
				if($re){
					$this->success('更新成功', url('flash'));
				}else{
					$this->error('更新失败');
				}
				
			}else{
				
				$f_data['content'] = $content;
				$f_data['addtime'] = date('Y-m-d H:i:s',time());
				$f_data['auther'] = '徐文娟';
				$re = $flash->add($f_data);
				if($re){
					$this->success('发布成功', url('flash'));
				}else{
					$this->error('发布失败');
				}
			}
        } else {
			return $this->fetch('editflash');
        }
    }
	
	public function delflash(Request $request){
		if($request->isGet()){
			$id = trim($request->param('id'));
			$flist = Db::name('flash')->where(array('id'=>$id))->find();
			if(!empty($flist)){
				$re = Db::name('flash')->where(array('id'=>$id))->delete();
				if($re){
					$this->success('删除成功', url('flash'));
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
    public function edit($id, Request $request)
    {
        if ($request->isPost()) {
            // 提交数据
            $user_object = Db::name('news');
            $data        = $request->post();
            $data['create_time'] = time();
            if(empty($data['title'])){
              $this->error('标题不能为空');  
            }
          //  var_dump($data);exit;
            if ($data) {
                $result = $user_object
                    ->update($data);
                if ($result) {
                    //return '更新成功';
                    $this->success('更新成功', url('index'));
                } else {
                    return '更新失败';
                    //$this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {
            // 获取账号信息
            $info = Db::name('news')->find($id);
            $this->assign('info',$info);
            $this->assign('act', url('edit', ['id' => $id]));
            return $this->fetch();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     *
     */
    public function setStatus($model = '', $script = false)
    {
        $model = 'NewsModel';
        parent::setStatus($model, $script);
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
        $table = Db::name('news');
        $re = $table->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功', url('index'));
        } else {
            $this->error('删除失败');
        }
    }
}
