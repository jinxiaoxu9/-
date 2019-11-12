<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use think\Db;
//use app\index\model\ConfigModel;
use app\index\model\User;

/**
 * 提现逻辑处理类
 * Class WithdrawLogic
 * @package app\index\logic
 */
class WithdrawLogic extends BaseLogic
{

    public function add($param = [])
    {

        $count = $this->modelBankcard->getBankCardCount(['id' => $param['bankcard_id']], 'id');
        if ($count == 0) {
            return ['status' => CodeEnum::ERROR, 'message' => '银行卡号不存在'];
        }
        if (bccomp($param['price'], 0.00, 2) != 1) {
            return ['status' => CodeEnum::ERROR, 'message' => '请输入大于零的提现金额'];
        }

        $sysconfig = getSysconfig();
        if ($param['price'] < $sysconfig['mix_withdraw']) {
            return ['status' => CodeEnum::ERROR, 'message' => '最小提现额度' . $sysconfig['mix_withdraw'] . '元'];
        }

        if ($param['price'] > $sysconfig['max_withdraw']) {
            return ['status' => CodeEnum::ERROR, 'message' => '最大提现额度' . $sysconfig['max_withdraw'] . '元'];
        }

        $user = $this->modelUser->getUser(['userid' => $param['uid']], 'money');
        if ($param['price'] > $user['money']) {
            return ['status' => CodeEnum::ERROR, 'message' => '您最多可提现' . $user['money'] . '元'];
        }
        $param['addtime'] =time();
        $ret  = $this->modelWithdraw->setWithdraw($param);

        if($ret){
              accountLog($param['uid'],4,0,$param['price'],'申请提现扣除金额'.$param['price']);
              return ['status' => CodeEnum::SUCCESS, 'message' => '提现成功'];
        }
        return ['status' => CodeEnum::ERROR, 'message' => '提现失败'];
    }


}