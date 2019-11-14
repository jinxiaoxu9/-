<?php

namespace app\admin\controller;

use think\Request;
use think\Db;

class ShopOrderController extends AdminController
{
    //类型
    public $shop_type =  [
        1 => 'PDD',
        2 => '淘宝',
        3 => '转转',
        4 => '京东',
        5 => '国美',
    ];
    //状态
    public $status_type = [ 0 => '初始化', 1 => '正在支付中', 2 => '支付完成'];
    /**
     * 首页  只提供编辑功能
     */
    public function index(Request $request)
    {
        $listData = Db::name('shop_order')
            ->order('shop_type asc,id desc')
            ->paginate(10);
        $list = $listData->items();
        foreach ($list as $k => $v) {
            if(isset($v['user_id']) && $v['user_id']) {
                $list[$k]['pid_account'] = Db::name('user')->where('userid', $v['user_id'])->value('account');
            } else {
                $list[$k]['pid_account'] = '';
            }
        }
        //分页
        $page = $listData->render();

        $this->assign('list', $list);
        $this->assign('page', $page);
        //商店订单类型

        $this->assign('shop_type', $this->shop_type);
        $this->assign('status_type', $this->status_type);
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

        $info = Db::name('shop_order')->where('id', $id)->find();
        $info['pid_account'] = Db::name('user')->where('userid', $info['user_id'])->value('account');
        $this->assign('info', $info);
        $this->assign('act', url('edit'));
        $this->assign('shop_type', $this->shop_type);
        $this->assign('status_type', $this->status_type);
        return $this->fetch();
    }
}
