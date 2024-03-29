<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use app\index\model\BankcardModel;
use think\Db;
//use app\index\model\ConfigModel;
use app\index\model\User;

class BankCardLogic extends BaseLogic
{

    /**
     *添加银行卡
     */
     public function  add($param=[]){

         if(!is_numeric($param['banknum']))
         {
             return ['code' =>CodeEnum::ERROR,'message'=>'银行卡格式错误'];
         }

         $count  = $this->modelBankcard->getBankCardCount(['banknum'=>$param['banknum']],'id');
         if($count)
         {
             return ['code' =>CodeEnum::ERROR,'message'=>'银行卡号已存在'];
         }

         $SecurityLogic = new SecurityLogic();

         $res = $SecurityLogic->checkSecurityByUserId($param['uid'], $param['security']);
         if($res['code'] == \app\common\library\enum\CodeEnum::ERROR)
         {
            return $res;
         }

         unset($param['security']);
         unset($param['token']);

         $param['addtime'] = request()->time();
         if($this->modelBankcard->setBankCard($param))
         {
             return ['code' =>CodeEnum::SUCCESS,'message'=>'银行卡添加成功'];
         }
         return ['code' =>CodeEnum::ERROR,'message'=>'银行卡添加失败'];
     }


    /**
     * 删除银行卡
     * @param $userId
     * @param $messageId
     * @return array
     */
    public function delBank($userId, $bankId)
    {
        $BankcardModel = new BankcardModel();
        if (empty($bankId))
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '参数错误'];
        }

        $ret = $BankcardModel->where(['id' => $bankId, 'uid' => $userId])->delete();

        //再校验一下
        if ($ret == false)
        {
            return ['code' => CodeEnum::ERROR, 'msg' => '操作失败'];
        }
        return ['code' => CodeEnum::SUCCESS, 'msg' => '操作成功'];
    }



}