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
        $UserGemaCodeLogic =  new CodeLogic();
        $codeLists = $UserGemaCodeLogic->getCodeList($this->user_id);
        $data['code_list'] = $codeLists;
        ajaxReturn('成功',1,'', $data);
    }

    /**
     *　添加二维码
     */
    public function addCode()
    {
        $accountName = $this->request->post('account_name');
        $type = $this->request->post('type');
        $imageLink = $this->request->post('image_link');
        $rawImageLink = $this->request->post('raw_image_link');

        $bankerName = $this->request->post('banker_name');
        $bankAccountName = $this->request->post('bank_account_name');
        $bankAccountNumber = $this->request->post('bank_account_number');
        $security = $this->request->post('security');

        $accountNumber = "";
        $imgs = "";

        if($type == 3)
        {
            $accountNumber = $bankerName.",".$bankAccountName.','.$bankAccountNumber;
        }
        else
        {
            $imgs = $imageLink."?".$rawImageLink;
        }

        $CodeLogic =  new CodeLogic();
        $result = $CodeLogic->addQRcode($this->user_id, $type, $imgs, $accountName, $accountNumber, $security);
        if ($result['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            ajaxReturn("上传失败,".$result['msg']);
            exit;
        }
        else
        {
            ajaxReturn($result['msg'], 1);
            exit;
        }
    }

    /**
     * 删除二维码
     */
    public function delCode()
    {
        $codeTypeId = $this->request->post('code_id');

        $CodeLogic =  new CodeLogic();
        $res = $CodeLogic->delCode($this->user_id, $codeTypeId);
        if ($res['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn($res['msg'],1,'');
    }

    /**
     * 冻结二维码
     */
    public function disactiveCode()
    {
        $codeTypeId = $this->request->post('code_id');

        $CodeLogic =  new CodeLogic();
        $res = $CodeLogic->disactiveCode($this->user_id, $codeTypeId);
        if ($res['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn($res['msg'],1,'');
    }

    /**
     *　激活二维码
     */
    public function activeCode()
    {
        $codeTypeId = $this->request->post('code_id');

        $CodeLogic =  new CodeLogic();
        $res = $CodeLogic->activeCode($this->user_id, $codeTypeId);
        if ($res['code'] == \app\common\library\enum\CodeEnum::ERROR)
        {
            ajaxReturn($res['msg'],0);
        }
        ajaxReturn($res['msg'],1,'');
    }
}