<?php
namespace app\index\controller;

use app\index\logic\CodeLogic;
use app\index\logic\ShareLogic;
use Common\Library\enum\CodeEnum;
use Home\Logic\GemaCodeLogic;
use think\Request;
use think\db;


class ShareController extends CommonController
{
    /**
     * 获取分享链接列表
     */
    public function linkList()
    {
        $page = $this->request->post("page", 1);
        $ShareLogic =  new ShareLogic();
        $linkList = $ShareLogic->getLinkList($this->user_id, $page);
        $data['link_list'] = $linkList;
        ajaxReturn('成功',1,'', $data);
    }

    /**
     * 分享链接初始化信息
     */
    public function getInitShareInfo()
    {
        $ShareLogic =  new ShareLogic();
        $info = $ShareLogic->getInitShareInfo($this->user_id);
        $data['init_info'] = $info;
        ajaxReturn('成功',1,'', $data);
    }

    /**
     *　添加分享链接
     */
    public function addLink()
    {
        $ShareLogic =  new ShareLogic();
        $result = $ShareLogic->addLink($this->user_id, $this->request);
        if ($result['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            ajaxReturn("添加失败,".$result['msg']);
            exit;
        }
        else
        {
            ajaxReturn("添加成功", 1);
            exit;
        }
    }

    /**
     * 删除邀请码
     */
    public function delLink()
    {
        $linkId = $this->request->post('link_id');

        $ShareLogic =  new ShareLogic();
        $res = $ShareLogic->delLink($this->user_id, $linkId);
        if ($res['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn($res['msg'],1,'');
    }

}