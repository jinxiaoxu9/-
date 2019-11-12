<?php

namespace app\index\controller;

use app\common\library\enum\CodeEnum;
use app\index\logic\HomeLogic;

class HomeController extends CommonController
{
    public function index()
    {
        $HomeLogic = new HomeLogic();
        $info = $HomeLogic->getUserWorkInfo($this->user_id);
        $data['info'] = $info;

        ajaxReturn('操作成功',1,'', $data);
    }

    public function startWork()
    {
        $HomeLogic = new HomeLogic();
        $res = $HomeLogic->startWork($this->user_id);

        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');
    }

    public function stopWork()
    {
        $HomeLogic = new HomeLogic();
        $res = $HomeLogic->stopWork($this->user_id);
        if ($res['code'] == CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn('操作成功',1,'');

    }
}