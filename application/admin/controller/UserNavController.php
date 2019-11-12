<?php

namespace app\admin\controller;

use app\admin\model\ConfigModel;
use think\Request;
use think\Db;

class UserNavController extends AdminController
{
    /**
     * 首页  只提供编辑功能
     */
    public function index(Request $request)
    {
        $ConfigModel = new ConfigModel();
        if ($request->isPost()) {
            $data = $request->post();
            $ret = $ConfigModel->where(['name' => $data['config_item']])->setField('value', json_encode($data));
            if ($ret !== false) {
                $this->success('配置成功');
            }
            $this->error('配置失败');
        }


        //查询配置
        $conf = DB::name('config')->field('value')->where(['name' => 'USER_NAV'])->find();
        $conf =  json_decode($conf['value'],true);

        $this->assign('userNavConfig',$conf );
        return $this->fetch();
    }
}
