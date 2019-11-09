<?php


namespace Gemapay\Model;
use think\Model;
class GemapayCodeMoneyPayingModel extends Model
{
    const WAITEPAY = 0;
    const PAYING = 1;

    /**
     * 增加ｃｏｄｅ支付个数
     * @param $id
     * @return false|int
     */
    public function incCodePayingNum($codeId, $money)
    {
        $where = [
            'code_id' => $codeId,
            'money' => $money
        ];

        if(!$this->where($where)->find())
        {
            $data = [];
            $data['code_id'] = $codeId;
            $data['money'] = $money;
            $data['paying_num'] = 1;
            return $this->add($data);
        }
        else
        {
           return $this->where($where)->setInc("paying_num");
        }
    }

    /**
     * 减少ｃｏｄｅ支付个数
     * @param $id
     * @return false|int
     */
    public function decCodePayingNum($codeId, $money)
    {
        $where = [
            'code_id' => $codeId,
            'money' => $money,
        ];
        return $this->where($where)->setDec("paying_num");
    }

    //
    public function resetCodePayingNum($codeId, $money)
    {
        $where = [
            'code_id' => $codeId,
            'money' => $money
        ];
        return $this->where($where)->dec("paying_num")->update();
    }
}