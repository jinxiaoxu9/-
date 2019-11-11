<?php
namespace app\index\controller;

use app\index\logic\CodeLogic;
use Common\Library\enum\CodeEnum;
use Home\Logic\GemaCodeLogic;
use think\Request;
use think\db;


class CodeController extends CommonController
{

    //获取个码收款二维码类型
    public function codeTypes()
    {
        //获取个码收款二维码类型
        $UserGemaCodeLogic =  new CodeLogic();
        $codeTypes = $UserGemaCodeLogic->getcodeTypes($this->user_id);
        $data['codeTypes'] = $codeTypes;

        ajaxReturn('成功',1,'', $data);
    }

    
    /**
     * 用户个码二维码列表
     */
    public function codeList()
    {
        $uid = $this->user_id;
        $codeTypeId = input('code_type_id', 1, 'intval');
        $ret = Db::name('gemapay_code')->where(array('user_id' => $uid, 'type' => $codeTypeId))->select();
        $this->assign('codeLists', $ret);
        return $this->fetch('gema_code_list');
    }

    /**
     *
     */
    public function addCode(Request $request)
    {
        if ($request->isPost())
        {
            $codeTypeId = trim($request->param('ewmclass'));
            $imgs = trim($request->param('icon'));
            $accountName = trim($request->param('account_name'));
            $accountNumber = trim($request->param('account_number'));
            $security = trim($request->param('security'));
            $GemaCodeLogic = new GemaCodeLogic();
            $result = $GemaCodeLogic->addQRcode($this->user_id, $codeTypeId, $imgs, $accountName, $accountNumber, $security);
            if ($result['code'] == CodeEnum::ERROR)
            {
                ajaxReturn("上传失败,".$result['msg']);
                exit;
            }
            else
            {
                ajaxReturn("ok", 1, url('User/index'));
                exit;
            }

        }
        else
        {
            ajaxReturn("上传失败");
            exit;
        }
        return $this->fetch();
    }

    public function delCode()
    {

    }

    public function disactiveCode()
    {

    }

    public function activeCode()
    {

    }
}