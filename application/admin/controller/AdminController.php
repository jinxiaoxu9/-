<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * 后台公共控制器
 * 为什么要继承AdminController？
 * 因为AdminController的初始化函数中读取了顶部导航栏和左侧的菜单，
 * 如果不继承的话，只能复制AdminController中的代码来读取导航栏和左侧的菜单。
 * @author jry <bbs.sasadown.cn>
 */
class AdminController extends Controller
{
    public $s_name_action ='';
    public $s_name_controller ='';
    public $s_name_module ='';

    /**
     * 初始化方法
     * @author jry <bbs.sasadown.cn>
     */
    public function _initialize()
    {
        $adminLogic = new \app\admin\logic\AdminLogic();
        // 登录检测
        if (!$adminLogic->is_login()) {
            //还没登录跳转到登录页面
            $this->redirect('admin/Pubss/login');
        }
        $request = Request::instance();
        $this->s_name_module = strtolower($request->module());
        $this->s_name_controller = strtolower($request->controller());
        $this->s_name_action = strtolower($request->action());

        $this->assign('s_name_module', $this->s_name_module);
        $this->assign('s_name_controller', $this->s_name_controller);
        $this->assign('s_name_action', $this->s_name_action);
        $this->assign('request', $request);

        // 权限检测
        $current_url = $this->s_name_module . '/' . $this->s_name_controller . '/' . $this->s_name_action;
        if ('admin/Index/index' !== $current_url &&  $current_url!='admin/Index/editPassword') {
            $groupLogic = new \app\admin\logic\GroupLogic();
            if (!$groupLogic->checkMenuAuth( $this->s_name_controller)) {
                $this->error('权限不足！', url('admin/Index/index'));
            }
        }

        // 获取左侧导航
        $this->getMenu();
		
    }

    // 后台主菜单
    public function getMenu()
    {
        $module_object = new \app\admin\logic\MenuLogic();
        //选种的顶部菜单ID
        $_menu_tab = $module_object->getParentMenu($this->s_name_controller);
        isset($_menu_tab['gid']) && $_menu_tab['gid'] ? $_menu_tab['gid'] : $_menu_tab['gid']=1;
        // 获取所有导航
        $menu_list=$module_object->getAllMenu($_menu_tab['gid']);
        $menu_top=$module_object->getTopMenu();
        $select_url=$module_object->SelectMenu($this->s_name_action, $this->s_name_controller);
        $_menu_list_c = $menu_list['c_menu'];

        $this->assign(array(
                '_menu_list_g'  =>  $menu_top['g_menu'],//爷爷级
                '_menu_list_p'  =>  $menu_list['p_menu'],//父级
                '_menu_list_c'  =>  $menu_list['c_menu'],//子级
                '_menu_tab'     =>  $_menu_tab,
                '_select_url'    =>  $select_url,
            ));
    }


    /**
     * 设置一条或者多条数据的状态
     * @param $script 严格模式要求处理的纪录的uid等于当前登陆用户UID
     * @author jry <bbs.sasadown.cn>
     */
    public function setStatus($model = '', $script = false)
    {
        $request = Request::instance();
        $ids    = $request->param('ids');
        $status = $request->param('status');

        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        //$modelOjb = model("app\admin\model\{$model}");
        $s_model = 'app\admin\model\\' . $model . 'Model';
        $modelOjb = new $s_model();

        $model_primary_key       = $modelOjb->getPk();
        $map[$model_primary_key] = array('in', $ids);
        $adminLogic = new \app\admin\logic\AdminLogic();
        if ($script) {
            $map['uid'] = array('eq', $adminLogic->is_login());
        }
        switch ($status) {
            case 'forbid': // 禁用条目
                $data = array('status' => 0);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success' => '禁用成功', 'error' => '禁用失败', 'url' => url('index'))
                );
                break;
            case 'resume': // 启用条目
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 0), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success' => '启用成功', 'error' => '启用失败', 'url' => url('index'))
                );
                break;
            case 'recycle': // 移动至回收站
                $data['status'] = -1;
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success' => '成功移至回收站', 'error' => '删除失败')
                );
                break;
            case 'restore': // 从回收站还原
                $data = array('status' => 1);
                $map  = array_merge(array('status' => -1), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success' => '恢复成功', 'error' => '恢复失败')
                );
                break;
            case 'delete': // 删除条目
                $result = $modelOjb->where($map)->delete();
                if ($result) {
                    $this->success('删除成功，不可恢复！');
                } else {
                    $this->error('删除失败');
                }
                break;
            default:
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $map   查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息
     *                       array(
     *                           'success' => '',
     *                           'error'   => '',
     *                           'url'     => '',   // url为跳转页面
     *                           'ajax'    => false //是否ajax(数字则为倒数计时)
     *                       )
     * @author jry <bbs.sasadown.cn>
     */
    final protected function editRow($model, $data, $map, $msg)
    {

        $model = 'app\admin\model\\' . $model . 'Model';
        $modelOjb = new $model();

        $request = Request::instance();
        $id = $request->param('id');

        $id = is_array($id) ? implode(',', $id) : $id;
        //如存在id字段，则加入该条件
        /*$options['table'] = $model->table;
        $fields = Db::name($model->table)->getTableFields($options);
        dump($fields);exit();
        if (in_array('id', $fields) && !empty($id)) {*/
            $where = array_merge(
                array('id' => array('in', $id)),
                (array) $map
            );
        //}
        $msg = array_merge(
            array(
                'success' => '操作成功！',
                'error'   => '操作失败！'
            ),
            (array) $msg
        );
        $result = $modelOjb->where($where)->update($data);
        if ($result != false) {
            $this->success($msg['success'], $msg['url']);
        } else {
            $this->error($msg['error'], $msg['url']);
        }
    }

    /**
     * 模块配置方法
     * @author jry <bbs.sasadown.cn>
     */
    public function module_config()
    {
        $module = new Module();
        $request = Request::instance()->param();
        $id     = (int) $request->param('id');
        if ($request->isPost()) {
            $config = $request['config'];
            $flag   = $module->where("id={$id}")
                ->setField('config', json_encode($config));
            if ($flag !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        } else {
            $name        = $this->s_name_module;
            $config_file = realpath(APP_PATH . $name) . '/' . D('Admin/Module')->install_file();
            if (!$config_file) {
                $this->error('配置文件不存在');
            }
            $module_config = include $config_file;

            $module_info = $module->where(array('name' => $name))->find($id);
            $db_config   = $module_info['config'];

            // 构造配置
            if ($db_config) {
                $db_config = json_decode($db_config, true);
                foreach ($module_config['config'] as $key => $value) {
                    if ($value['type'] != 'group') {
                        $module_config['config'][$key]['value'] = $db_config[$key];
                    } else {
                        foreach ($value['options'] as $gourp => $options) {
                            foreach ($options['options'] as $gkey => $value) {
                                $module_config['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                            }
                        }
                    }
                }
            }

            // 构造表单名
            foreach ($module_config['config'] as $key => $val) {
                if ($val['type'] == 'group') {
                    foreach ($val['options'] as $key2 => $val2) {
                        foreach ($val2['options'] as $key3 => $val3) {
                            $module_config['config'][$key]['options'][$key2]['options'][$key3]['name'] = 'config[' . $key3 . ']';
                        }
                    }
                } else {
                    $module_config['config'][$key]['name'] = 'config[' . $key . ']';
                }
            }

            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('设置') //设置页面标题
                ->setPostUrl(U('')) //设置表单提交地址
                ->addFormItem('id', 'hidden', 'ID', 'ID')
                ->setExtraItems($module_config['config']) //直接设置表单数据
                ->setFormData($module_info)
                ->display();
        }
    }

    /**
     * 扩展日期搜索map
     * @param $map array 引用型
     * @param string $field 搜索的时间范围字段
     * @param string $type datetime  类型 或 timestamp 时间戳
     * @param boolean $not_empty 是否允许空值搜索到
     */
    public function extendDates(&$map, $field = 'update_time', $type = 'datetime', $not_empty = false)
    {
        $dates = I('dates', '', 'trim');
        if ($dates) {
            $start_date = substr($dates, 0, 10);
            $end_date   = substr($dates, 11, 10);
            if ($type == 'datetime') {
                $map[$field] = [
                    ['egt', $start_date . ' 00:00:00'],
                    ['lt', $end_date . ' 23:59:59'],
                ];
                if ($not_empty) {
                    $map[$field][] = ['exp', 'IS NOT NUll'];
                }
            } else {
                $map[$field] = [
                    ['egt', strtotime($start_date . ' 00:00:00')],
                    ['lt', strtotime($end_date . ' 23:59:59')],
                ];
            }
        }
        // else {
        //     $start_date = datetime("-365 days", 'Y-m-d');
        //     $end_date   = datetime('now', 'Y-m-d');
        // }
    }
}
