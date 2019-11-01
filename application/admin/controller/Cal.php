<?php

namespace Admin\Controller;

use Admin\Model\GroupModel;
use Admin\Model\UserInviteSetting;
use Admin\Model\UserGroupModel;
use Gemapay\Model\GemapayCodeModel;
use Gemapay\Model\GemapayCodeTypeModel;
use Gemapay\Model\GemapayOrderModel;
use Think\Page;


/**
 * 团队统计报表
 * Class UserController
 * @package Admin\Controller
 */
class Cal extends Admin
{
    /**
     * 团队统计报表列表
     */
    public function tz()
    {
        //所有团长auth_
        $tzRole = C('tz_id');
        $GemapayCodeModel = new \Gemapay\Model\GemapayCodeModel();
        $adminTzs = M('admin')->where(['auth_id' => $tzRole])->select();
        $this->assign('tzs', $adminTzs);

       // echo date("Y-m-d Hi:s", 1571833991);die(); 2019-10-23 20:33:11
        //时间搜素
        //时间
        $startTime = I('start_time/s',date("Y-m-d 00:00:00", time()));
        $endTime = I('end_time/s');

        if ($startTime && empty($endTime)) {
            $map['add_time'] = ['egt', strtotime($startTime)];
        }
        if (empty($startTime) && $endTime) {
            $map['add_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $map['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }
        $adminId = I('admin_id', 0, 'intval');
        $this->assign('adminId', $adminId);
        $adminId && $map['b.add_admin_id'] = $adminId;
        //重置团长
        if(checkIstz(session('user_auth.uid'))){
            $adminId = $map['b.add_admin_id'] = session('user_auth.uid');

        }

        if ($adminId) {
            $adminTzs = M('admin')->where(['id' => $adminId])->select();
        }

        //就全部写道控制器算了
        $data = [];
        if (is_array($adminTzs) && count($adminTzs) > 0) {
            foreach ($adminTzs as $k => $v) {
                $map['b.add_admin_id'] = $v['id'];
                $tzOrders = M('gemapay_order')->alias('a')->field('a.order_price,a.status')
                    ->join('ysk_user  b ON a.gema_userid=b.userid', "LEFT")
                    ->where($map)
                    ->select();
                $row['tc_nickname'] = $v['nickname'];
                $row['total_count'] = count($tzOrders);//总订单数

                $success_count = 0;
                $sueccesPrice = 0.00;
                foreach ($tzOrders as $k1 => $v1) {
                    if ($v1['status'] == \Gemapay\Model\GemapayOrderModel::PAYED) {
                        $success_count++;
                        $sueccesPrice += $v1['order_price'];
                    }
                }
                $row['online_num'] = $GemapayCodeModel->getOnlineCodes($v['id']);
                $row['zfb_online_num'] = $GemapayCodeModel->getOnlineCodes($v['id'], \Gemapay\Model\GemapayCodeTypeModel::ZHIFUBAO);
                $row['vx_online_num'] = $GemapayCodeModel->getOnlineCodes($v['id'], \Gemapay\Model\GemapayCodeTypeModel::WEIXIN);
                $row['success_count'] = $success_count;
                $row['success_percent'] = ($success_count != 0) ? $success_count / $row['total_count'] : 0;
                $row['success_percent'] = sprintf("%.2f", $row['success_percent']);
                $row['success_order_pay_price'] = sprintf("%.2f", $sueccesPrice);
                $data[] = $row;
                unset($row);
            }
        }
        unset($adminTzs);
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 用户报表统计
     */
    public function userGroup()
    {
        //用户组别
        $adminId = session('user_auth.uid');
        checkIstz($adminId) && $_map['admin_id'] = $adminId;
        $userGroups = M('user_group')->where($_map)->field('id,parentid,name')->select();
        //时间搜素
        $startTime = I('start_time/s',date("Y-m-d 00:00:00", time()));
        $endTime =  I('end_time/s');
        if ($startTime && empty($endTime)) {
            $map['add_time'] = ['egt', strtotime($startTime)];
        }

        if (empty($startTime) && $endTime) {
            $map['add_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $map['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }

        $groupId = I('group_id', -1, 'intval');
        $userGetGroupsObj = new UserController();
        $groups = $userGetGroupsObj::getGroups($groupId);
        if ($groups !== "") {
            $map['group_id'] = array("in", $groups . "");
        }
        $adminId = session('user_auth.uid');
        checkIstz($adminId) &&  $map['gema_userid'] = ['in', tzUsers()];
        $list = M('gemapay_order')->alias('a')
            ->field('sum(a.order_price) as total_group_price,count(a.id) as total_group_count ,group_concat(a.id) as ids,b.group_id')
            ->join("ysk_user b on a.gema_userid=b.userid", "left")
            ->where($map)
            ->order('id desc')
            ->group('b.group_id')
            ->select();
        if(is_array($list)){
            foreach($list as $k=>$v){
                  //获取成功条数以及成功金额
                $con['status'] = 1;
                $con['id'] =['in' ,$v['ids']];
                $row =M('gemapay_order')->field('sum(order_price) as total_success_group_price,count(*) as success_num')->where($con)->find();
                $list[$k]['success_total_group_count'] =$row['success_num'];
                $list[$k]['success_total_group_money'] =$row['total_success_group_price'];
                $list[$k]['success_percent'] =sprintf("%.2f", $row['success_num']/$v['total_group_count']);
               //小组名称
                $list[$k]['group_name'] =M('user_group')->where(['id'=>$v['group_id']])->getField('name');

            }
        }
       $this->assign('data', $list);
        $this->assign('groupId', $groupId);
        $this->assign('userGroups', getCategory($userGroups));
        $this->display();
    }


}
