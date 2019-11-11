<?php

namespace app\admin\Controller;

use app\admin\model\ConfigModel;
use think\Request;
use think\Db;

class UserNavController extends AdminController
{
    /**
     * 首页  只提供编辑功能
     */
    public function index()
    {
        $ConfigModel = new ConfigModel();
        if ($_POST) {
            $data = I('post.');
            $ret = $ConfigModel->where(['name' => $data['config_item']])->setField('value', json_encode($data));
            if ($ret !== false) {
                $this->success('配置成功');
            }
            $this->error('配置失败');
        }


        //查询配置
        $conf = M('config')->field('value')->where(['name' => 'USER_NAV'])->find();
        $conf =  json_decode($conf['value'],true);

        $this->assign('userNavConfig',$conf );
        $this->display();
    }
}
