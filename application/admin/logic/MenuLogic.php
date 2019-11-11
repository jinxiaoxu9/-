<?php
namespace app\admin\logic;

use app\admin\model\MenuModel;
use app\admin\model\AdminModel;
use app\admin\model\GroupModel;
use app\admin\logic\AdminLogic;

class MenuLogic
{
    /**
     * [getCol 获取用户可操作控制器]
     * @return [type] [description]
     */
    public function getCol()
    {
        $adminLogic = new AdminLogic();
        $uid        = $adminLogic->is_login();
        $admin = new AdminModel();
        $auth_id    = $admin->where(array('id'=>$uid))->value('auth_id');
        if($auth_id==1){
            return $auth_id;
        }

        //根据用户ID获用户角色
        $group = new GroupModel();
        $group_info = $group->find($auth_id);
        $group_auth = $group_info['menu_auth']; // 获得当前登录用户的权限列表
        $con['status']      = 1;
        $con['_string']     = 'id IN ('.trim($group_auth,',').')';
        //顶级菜单
        $menu = new MenuModel();
        $col = $menu->where($con)->order(['sort','id'=>'asc'])->column('col');
        return $col;
    }


    /**
     * 获取最顶级菜单
     */
    public function getTopMenu()
    {
        $adminLogic = new \app\admin\logic\AdminLogic();
        $uid        = $adminLogic->is_login();

        //根据用户ID获用户角色
        $admin = new AdminModel();
        $auth_id    = $admin->where(array('id'=>$uid))->value('auth_id');
        $menu = new MenuModel();
        //超级管理员
        if($auth_id==1){

            //顶级菜单
            $con['status']      = 1;
            $con['level']      = 0;
            $system_module_list_g = $menu->where($con)->order(['sort','id'=>'asc'])->select();
            foreach ($system_module_list_g as $key => $val) {
                $where['level']=2;
                $where['gid']=$val['id'];
                $info=$menu->where($where)->order(['sort','id'=>'asc'])->field('col,act')->find();
                if($info['col'] && $info['act']){
                    $system_module_list_g[$key]['col']=$info['col'];
                    $system_module_list_g[$key]['act']=$info['act'];
                }
            }
            $menu_list['g_menu']=$system_module_list_g;


            return $menu_list;
        }
        $group = new GroupModel();
        $group_info = $group->find($auth_id);
        $group_auth = $group_info['menu_auth']; // 获得当前登录用户所属部门的权限列表
        // 获取所有菜单
        //顶级菜单
        $con['level']      = 0;
        $con['status']      = 1;
        $con['_string']     = 'id IN ('.trim($group_auth,',').')';
        $system_module_list_g = $menu->where($con)->order(['sort','id'=>'asc'])->select();
        foreach ($system_module_list_g as $key => $val) {
            $where['level']=2;
            $where['gid']=$val['id'];
            $where['_string']     = 'id IN ('.trim($group_auth,',').')';
            $info = $menu->where($where)->order(['sort','id'=>'asc'])->field('col,act')->find();
            if($info['col'] && $info['act']){
                $system_module_list_g[$key]['col']=$info['col'];
                $system_module_list_g[$key]['act']=$info['act'];
            }
        }
        $menu_list['g_menu']=$system_module_list_g;
        return $menu_list;
    }

    /**
     * 获取二三级菜单
     * @param string $addon_dir
     */
    public function getAllMenu($gid=1)
    {

        $adminLogic = new AdminLogic();
        $uid        = $adminLogic->is_login();
        $admin = new AdminModel();
        $menu = new MenuModel();
        $group = new GroupModel();
        $auth_id    = $admin->where(array('id'=>$uid))->value('auth_id');
        //超级管理员
        if($auth_id==1){

            //父级菜单
            $con['status']      = 1;
            $con['level']      = 1;
            $con['gid']      = $gid;
            $system_module_list_p = MenuModel::where($con)->order(['sort','id'=>'asc'])->select();
            $menu_list['p_menu']=$system_module_list_p;
            //子级菜单
            $con['level']      = 2;
            $system_module_list_c = MenuModel::where($con)->order(['sort','id' => 'asc'])->select();
            $menu_list['c_menu'] = $system_module_list_c;


            return $menu_list;
        }

        //根据用户ID获用户角色
        $group_info = $group->find($auth_id);
        $group_auth = $group_info['menu_auth']; // 获得当前登录用户所属部门的权限列表
        // 获取所有菜单
        $con['status']      = 1;
        $con['_string']     = 'id IN ('.trim($group_auth,',').')';
        $con['gid']      = $gid;
        //父级菜单
        $con['level']      = 1;
        $system_module_list_p = MenuModel::where($con)->order(['sort','id' => 'asc'])->select();
        $menu_list['p_menu']=$system_module_list_p;
        //子级菜单
        $con['level']      = 2;
        $system_module_list_c = MenuModel::where($con)->order(['sort','id' => 'asc'])->select();
        $menu_list['c_menu']=$system_module_list_c;
        return $menu_list;
    }

    /**
     * 根据菜单ID的获取其所有父级菜单
     * @return array 父级菜单集合
     */
    public function getParentMenu($controller_name ='')
    {
        $col=$controller_name;
        $where['col']       = $col;
        $where['status']    = 1;
        $where['level']     = 2;
        $menu = new MenuModel();
        //取当前菜单的顶级
        $m_info = $menu->where($where)->field('pid,gid,name')->find();
        //取父级名称
        $p_where['id']=$m_info['pid'];
        $p_name = $menu->where($p_where)->value('name');
        //取顶级名称
        $g_where['id']=$m_info['gid'];
        $g_name = $menu->where($g_where)->value('name');
        // 面包屑导航
        $m_info['name']=array($g_name,$p_name,$m_info['name']);

        return $m_info;
    }

    /*
        判断菜单选中
     */
    public function SelectMenu($action_name = '', $controller_name = ''){
        $act=$action_name;
        $col=$controller_name;
        $menu = new MenuModel();
        $select_url=strtolower($controller_name.'-'.$action_name);
        $where['col']=$col;
        $where['act']=$act;
        $count=$menu->where($where)->count(1);
        if($count>0){
            return $select_url;
        }

    }

    /**
     * 下拉菜单
     *
     * @param int $pid
     * @param array $menu
     * @param string $str
     * @return array|mixed
     */
    public function getSelectMenu($pid = 0,&$menu = array(),$str = '|--')
    {
        $menuModel = new MenuModel();
        $data = $menuModel->field('id,name')->where(array('pid'=>$pid))->order('sort asc')->select();
        if($data){
            foreach ($data as $k=>$v){
                $v['name'] = $str . $v['name'];
                $menu[] = $v;
                $this->getSelectMenu($v['id'],$menu,$str.'--');
            }
        }
        return $menu;
    }

    /**
     * 获取设置菜单的级别和爷爷id
     *
     * @param int $pid
     * @param int $gid
     * @param int $level
     * @return array
     */
    public function getInfoByPid($pid = 0,&$gid = 0,&$level = 0)
    {
        if(!empty($pid)){
            $menu = new MenuModel();
            $data = $menu->find($pid);
            if($data){
                $level++;
                $gid = $data['id'];
                if($data['pid'] != 0){
                    $this->getInfoByPid($data['pid'],$gid,$level);
                }
            }
        }
        return array(
            'gid'=>$gid,
            'level'=>$level
        );
    }

}