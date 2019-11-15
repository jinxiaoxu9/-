<?php

namespace app\admin\controller;

use app\admin\model\GroupModel;
use app\admin\model\UserInviteSetting;
use app\admin\model\UserGroupModel;
use app\index\model\UserModel;
use app\common\model\GemapayCodeModel;
use app\common\model\GemapayCodeTypeModel;
use app\admin\model\GemapayOrderModel;
use app\admin\logic\UserLogic;
use app\admin\logic\AdminLogic;
use think\Request;
use think\Db;

/**
 * 团队统计报表
 * Class UserController
 * @package Admin\Controller
 */
class CalController extends AdminController
{
    /**
     * 团队统计报表列表
     */
    public function tz(Request $request)
    {
        //所有团长auth_
        $tzRole = Config('tz_id');
        $GemapayCodeModel = new GemaPayCodeModel();
        $adminTzs = Db::name('admin')->where(['auth_id' => $tzRole])->select();
        $this->assign('tzs', $adminTzs);


        //时间搜素
        //时间
        $startTime = $request->param('start_time',date("Y-m-d 00:00:00", time()));
        $endTime = $request->param('end_time');

        if ($startTime && empty($endTime)) {
            $map['add_time'] = ['egt', strtotime($startTime)];
        }
        if (empty($startTime) && $endTime) {
            $map['add_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $map['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }
        $adminId = $request->param('admin_id', 0, 'intval');
        $this->assign('adminId', $adminId);
        $adminId && $map['b.add_admin_id'] = $adminId;
        //重置团长
        $adminLogic = new AdminLogic();
        $int_is_admingroup = 0;
        if($adminLogic->checkIstz(session('user_auth.uid'))){
            $adminId = $map['b.add_admin_id'] = session('user_auth.uid');

            $int_is_admingroup = 1;
        }

        if ($adminId) {
            $adminTzs = Db::name('admin')->where(['id' => $adminId])->select();
        }

        //就全部写道控制器算了
        $data = [];
        if (is_array($adminTzs) && count($adminTzs) > 0) {
            foreach ($adminTzs as $k => $v) {
                //dump($v);exit();
                $map['b.add_admin_id'] = $v['id'];
                //dump($map);exit();
                $tzOrders = Db::name('gemapay_order')->alias('a')->field('a.order_price,a.status')
                    ->join('ysk_user b', 'a.gema_userid=b.userid', "LEFT")
                    ->where($map)
                    ->select();
                //echo Db::getLastSql();exit();
                $row['tc_nickname'] = $v['nickname'];
                $row['total_count'] = count($tzOrders);//总订单数

                $success_count = 0;
                $sueccesPrice = 0.00;
                foreach ($tzOrders as $k1 => $v1) {
                    if ($v1['status'] == GemapayOrderModel::PAYED) {
                        $success_count++;
                        $sueccesPrice += $v1['order_price'];
                    }
                }
                $row['online_num'] = $GemapayCodeModel->getOnlineCodes($v['id']);
                $row['zfb_online_num'] = $GemapayCodeModel->getOnlineCodes($v['id'], GemapayCodeTypeModel::ZHIFUBAO);
                $row['vx_online_num'] = $GemapayCodeModel->getOnlineCodes($v['id'], GemapayCodeTypeModel::WEIXIN);
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
        $this->assign('int_is_admingroup', $int_is_admingroup);

        return $this->fetch();
    }


    /**
     * 用户报表统计
     */
    public function userGroup(Request $request)
    {
        $adminLogic = new AdminLogic();
        //用户组别
        $adminId = session('user_auth.uid');
        $_map = array();
        if($adminLogic->checkIstz($adminId)) {
            $_map['admin_id'] = $adminId;
        }

        $userGroups = Db::name('user_group')->where($_map)->field('id, parentid,name')->select();
        //时间搜素
        $startTime = $request->param('start_time',date("Y-m-d 00:00:00", time()));
        $endTime =  $request->param('end_time');
        $map = array();
        if ($startTime && empty($endTime)) {
            $map['add_time'] = ['egt', strtotime($startTime)];
        }

        if (empty($startTime) && $endTime) {
            $map['add_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $map['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }

        $groupId = $request->param('group_id', -1, 'intval');
        $userGetGroupsObj = new UserController();
        $groups = $userGetGroupsObj::getGroups($groupId);
        if ($groups !== "") {
            $map['group_id'] = array("in", $groups . "");
        }
        $adminId = session('user_auth.uid');

        $a_uid = $adminLogic->tzUsers();
        $map    = array();
        if(is_array($a_uid) && $a_uid && isset($_map['admin_id'])) {
            $map['gema_userid'] = ['in', $a_uid];
        }
        //$adminLogic->checkIstz($adminId) &&  $map['gema_userid'] = ['in', $adminLogic->tzUsers()];
        $list = Db::name('gemapay_order')->alias('a')
            ->field('sum(a.order_price) as total_group_price,count(a.id) as total_group_count ,group_concat(a.id) as ids,b.group_id')
            ->join("ysk_user b", "a.gema_userid=b.userid", "left")
            ->where($map)
            ->order('id desc')
            ->group('b.group_id')
            ->select();

        if(is_array($list)){
            foreach($list as $k=>$v){
                  //获取成功条数以及成功金额
                $con['status'] = 1;
                $con['id'] =['in' ,$v['ids']];
                $row =Db::name('gemapay_order')->field('sum(order_price) as total_success_group_price,count(*) as success_num')->where($con)->find();
                $list[$k]['success_total_group_count'] =$row['success_num'];
                $list[$k]['success_total_group_money'] =$row['total_success_group_price'];
                $list[$k]['success_percent'] =sprintf("%.2f", $row['success_num']/$v['total_group_count']);
               //小组名称
                $list[$k]['group_name'] =Db::name('user_group')->where(['id'=>$v['group_id']])->value('name');

            }
        }
        $this->assign('data', $list);
        $this->assign('groupId', $groupId);
        $userLogic = new UserLogic();
        $this->assign('userGroups', $userLogic->getCategory($userGroups));

        return $this->fetch();
    }


}
