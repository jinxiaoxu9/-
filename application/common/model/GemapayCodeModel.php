<?php


namespace app\common\model;


use think\Model;

class GemapayCodeModel extends Model
{

    //打开中
    const STATUS_ON = 0;

    //关闭中
    const STATUS_OFF = 1;

    //支付中
    const STATUS_PAYING = 1;

    //空闲中　
    const STATUS_NOPAYING = 0;

    //关闭中
    const STATUS_CLOSE = 2;

    //二维码生成订单最大个数
    const LIMIT_NUM = 200;

    //每个金额最大个数
    const MONEY_LIMIT_NUM = 10;

    //每个码最大收款额
    const CODE_MONEY_LIMIT = 10000;

    /**
     * 获取一个最优使用的二维码
     * @param $money
     * @param null $type
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getAviableCode($money, $type)
    {
        $GemapayOrderModel = new GemapayOrderModel();
        //查询该金额正在支付的订单的code
        $where["order_pay_price"] = $money;
        $where["status"] = $GemapayOrderModel::WAITEPAY;
        $fileds = [
            "code_id",
        ];
        $codes = $GemapayOrderModel->field($fileds)->where($where)->select();
        unset($where);

        if(!empty($codes))
        {

            $ids = [];
            foreach ($codes as $code)
            {
                if(!empty($code['code_id']))
                {
                    $ids[] = $code['code_id'];
                }
            }

            $ids = array_unique($ids);
            if(!empty($ids))
            {
                $where["code.id"] = array("not in", $ids);
            }
        }

        //20分钟在线
        $bt=time()-config('online_time_toqiangdan');
        //最近20分钟在线
//        $where["code.last_online_time"] = array('gt',$bt);
        //二维码类型
        $where["code.type"] = $type;

        //二维码状态正常
        $where["code.status"] = self::STATUS_ON;

        //二维码没有被锁定
        $where["code.is_lock"] = self::STATUS_NO;

        //余额足够
        $where["u.money"] = array('gt',$money);

        //用户正常，没被冻结
        $where["u.status"] = self::STATUS_YES;

        //用户工作状态
        $where["u.work_status"] = self::STATUS_YES;

        //用户所属管理员工作状态
        //$where["adm.work_status"] = self::STATUS_YES;

        //根据最近更新时间去寻找
        //$order = "order_today_all desc, last_online_time desc";
        if(time()%100>10)
        {
            $order = "order_today_all ASC, last_online_time desc";
        }else
        {
            $order = "last_online_time desc";
        }

        //选内容
        $fileds = [
            "code.*",
        ];

        $this->join('ysk_user u', "u.userid=code.user_id", "LEFT");
        $this->join('ysk_admin adm', "adm.id=u.add_admin_id", "LEFT");
        $data =  $this->alias('code')->field($fileds)->where($where)->order($order)->select();
        return $data;
    }

    public function getOnlineCodes($adminId, $type = false)
    {
        if($adminId !=1)
        {
            $where["adm.id"] = $adminId;
        }
        //20分钟在线
        $bt=time()-C('online_time_toqiangdan');
        //最近20分钟在线
        $where["a.last_online_time"] = array('gt',$bt);

        //二维码类型
        if(!empty($type))
        {
            $where["a.type"] = $type;
        }

        //二维码状态正常
        $where["a.status"] = self::STATUS_ON;

        //二维码没有被锁定
        $where["a.is_lock"] = self::STATUS_NO;

        //余额足够
        $where["u.money"] = array('gt',0);

        //用户正常，没被冻结
        $where["u.status"] = self::STATUS_YES;

        //用户工作状态
        $where["u.work_status"] = self::STATUS_YES;

        //用户所属管理员工作状态
        //$where["adm.work_status"] = self::STATUS_YES;

        //选内容
        $fileds = [
            "a.*",
        ];

        $this->join(C('DB_PREFIX').'user u on u.userid=a.user_id', "left");
        $this->join(C('DB_PREFIX').'admin adm on adm.id=u.add_admin_id', "left");
        return $this->alias('a')->field($fileds)->where($where)->count();
    }
    /**
     * 增加ｃｏｄｅ支付个数
     * @param $id
     * @return false|int
     */
    public function incCodePayingNum($id)
    {
        $data = [
            'pay_status' => self::STATUS_PAYING,
            'last_used_time' => time(),
            'paying_num'=>['exp','paying_num'+1]
        ];
        $where = [
            'id' => $id
        ];
        $ret = $this->where($where)->save($data);
        return $ret;
    }

    /**
     * 增加ｃｏｄｅ支付个数
     * @param $id
     * @return false|int
     */
    public function incTodayOrder($id)
    {
        $where = [
            'id' => $id
        ];
        $ret = $this->where($where)->setInc("order_today_all");
        return $ret;
    }

    /**
     * 减少ｃｏｄｅ支付个数
     * @param $id
     * @return false|int
     */
    public function decCodePayingNum($id)
    {
        $data = [
            'last_used_time' => time(),
            'paying_num'=>['exp','paying_num'-1]
        ];

        $where = [
            'id' => $id
        ];

        return $this->where($where)->save($data);
    }


    /**
     * 添加二维码
     * @param $userId
     * @param $type
     * @param $image
     * @return int|string
     */
    public function addCode($userId, $type, $image, $username)
    {
        $data = [
            'last_used_time' => request()->time(),
            'user_id' => $userId,
            'type' => $type,
            'qr_image' => $image,
            'paying_num' => 0,
            'user_name' => $username,
            'limit_money' => self::CODE_MONEY_LIMIT,
            'pay_status' => self::STATUS_NOPAYING,
            'pay_status' => self::STATUS_ON,
        ];

        return $this->insert($data);
    }

    /**
     * 更新二维码
     * @param $userId
     * @param $type
     * @param $image
     * @return int|string
     */
    public function updateCode($userId, $codeId, $image)
    {
        $where = [
            'user_id' => $userId,
            'code_id' => $codeId
        ];

        $data = [
            'qr_image' => $image
        ];

        return $this->where($where)->update($data);
    }

    /**
     * 检查二维码是否存在
     * @param $userId
     * @param $type
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkCode($userId, $type)
    {
        $where = [
            'user_id' => $userId,
            'type' => $type
        ];
        return $this->where($where)->find();
    }

    public function getLists($userId)
    {
        $where['user_id'] = $userId;
        return $this->where($where)->select();
    }

    public function clearYesterdayOrder()
    {
        return $this->where("1=1")->setField("order_today_all", 0);
    }

    public function setOnline($codeId)
    {
        $where = [
            'id' => $codeId
        ];

        $data = [
            'last_online_time' => request()->time()
        ];

        return $this->where($where)->update($data);
    }

}
