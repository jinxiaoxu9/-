<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
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
             return ['status' =>CodeEnum::ERROR,'message'=>'银行卡格式错误'];
         }
         $count  = $this->modelBankcard->getBankCardCount(['banknum'=>$param['banknum']],'id');
         if($count)
         {
             return ['status' =>CodeEnum::ERROR,'message'=>'银行卡号已存在'];
         }
         unset($param['token']);
         $param['addtime'] =time();
         if($this->modelBankcard->setBankCard($param))
         {
             return ['status' =>CodeEnum::SUCCESS,'message'=>'银行卡添加成功'];
         }
         return ['status' =>CodeEnum::ERROR,'message'=>'银行卡添加失败'];
     }





}