<?php
namespace app\index\controller;
use Common\Library\enum\CodeEnum;
use Home\Logic\GemaCodeLogic;

class GemaCode extends Common
{

    //添加二维码页面
    public function adderweima()
    {
        //获取个码收款二维码类型
        $UserGemaCodeLogic =  new GemaCodeLogic();
        $codeTypes = $UserGemaCodeLogic->getcodeTypes($this->user_id);

        $this->assign('codeTypes', $codeTypes);
        $this->display();
    }

    //二维码管理
    public function erweima()
    {
        //获取个码收款二维码类型
        $UserGemaCodeLogic =  new GemaCodeLogic();
        $codeTypes = $UserGemaCodeLogic->getcodeTypes($this->user_id);

        $this->assign('codeTypes', $codeTypes);
        $this->display();
    }
    
    /**
     * 用户个码二维码列表
     */
    public function gemaCodelist()
    {
        $uid = $this->user_id;
        $codeTypeId = I('code_type_id', 1, 'intval');
        $ret = M('gemapay_code')->where(array('user_id' => $uid, 'type' => $codeTypeId))->select();
        $this->assign('codeLists', $ret);
        $this->display('gema_code_list');
    }

    /**
     *
     */
    public function ewmup()
    {
        if (IS_POST)
        {
            $codeTypeId = trim(I('post.ewmclass'));
            $imgs = trim(I('post.icon'));
            $accountName = trim(I('post.account_name'));
            $accountNumber = trim(I('post.account_number'));
            $security = trim(I('post.security'));
            $GemaCodeLogic = new GemaCodeLogic();
            $result = $GemaCodeLogic->addQRcode($this->user_id, $codeTypeId, $imgs, $accountName, $accountNumber, $security);
            if ($result['code'] == CodeEnum::ERROR)
            {
                ajaxReturn("上传失败,".$result['msg']);
                exit;
            }
            else
            {
                ajaxReturn("ok", 1, U('User/index'));
                exit;
            }

        }
        else
        {
            ajaxReturn("上传失败");
            exit;
        }
        $this->display();
    }

}