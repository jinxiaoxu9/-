<?php

namespace app\admin\Controller;

use app\admin\logic\InviteSettingLogic;
use app\admin\logic\AdminLogic;
use app\admin\logic\UserLogic;
use app\admin\model\UserModel;
use app\admin\model\UserInviteSettingModel as UserInviteSetting;
use app\admin\model\UserGroupModel;
use app\admin\model\GemapayCodeTypeModel;
use think\Request;
use think\Db;

/**
 * 用户控制器
 *
 */
class UserController extends AdminController
{
    /**
     * 编辑用户
     *
     */
    public function edit(Request $request)
    {
        $userid = trim($request->param('userid'));

        if(!$userid) {
            $this->error('参数错误');
        }
        $ulist = Db::name('user')->where(array('userid' => $userid))->find();

        $InviteSettingLogic = new InviteSettingLogic();
        $where["admin_id"] = session("user_auth.uid");
        $data_list = Db::name('user_invite_setting')->where($where)->select();

        $codeTypeLists = Db::name('gemapay_code_type')->select();
        //添加描述
        foreach ($data_list as $key => $dataval) {
            $data_list[$key]["desc"] = $InviteSettingLogic->getInviteDesc($dataval["invite_setting"], $codeTypeLists);
        }

        if ($request->isPost()) {
            $userLogic = new UserLogic();
            $code = trim($request->param('code'));
            $data['username'] = trim($request->param('username'));
            $data['mobile'] = trim($request->param('mobile'));
            $data['truename'] = trim($request->param('truename'));
            /*$data['nsc_money'] = trim($request->param('nsc_money'));
            $data['eth_money'] = trim($request->param('eth_money'));
            $data['eos_money'] = trim($request->param('eos_money'));
            $data['btc_money'] = trim($request->param('btc_money'));*/
            $data['money'] = trim($request->param('money'));
            $data['u_yqm'] = $code;
            $login_pwd = trim($request->param('login_pwd'));
            if (empty($code)) {
                $this->error('邀请费率不能为空,请先添加邀请配置');
            }
            if ($login_pwd != '') {
                $data['login_pwd'] = $userLogic->pwd_md5($login_pwd, $ulist['login_salt']);
            }

            $safety_pwd = trim($request->param('safety_pwd'));
            //安全密码
            if ($safety_pwd != '') {
                $data['security_pwd'] = $userLogic->pwd_md5($safety_pwd, $ulist['security_salt']); //safety_salt
            }
            unset($data['id'], $data['code']);  //id不更新

            $re = Db::name('user')->where(array('userid' => $userid))->update($data);
            if ($re) {
                $this->success('资料修改成功');
            } else {
                $this->error('资料修改失败');
            }
        } else {
            if(isset($ulist['pid'])) {
                $ulist['pid_account'] = Db::name('user')->where('userid', $ulist['pid'])->value('account');
            } else {
                $ulist['pid_account'] = '';
            }
            $this->assign("data_list", $data_list);
            $this->assign('info', $ulist);
            $this->assign('act', url('edit'));
            return $this->fetch();
        }

    }


    public function add(Request $request)
    {
        $userId = intval($request->param('user_id'));
        $userInfo = null;
        $InviteSettingModel = new UserInviteSetting();
        $where["admin_id"] = session("user_auth.uid");
        //$data_list = $InviteSettingModel->where($where)->select();
        $data_list = Db::name('user_invite_setting')->where($where)->select();
        $InviteSettingLogic = new InviteSettingLogic();
        $codeTypeLists = Db::name('gemapay_code_type')->select();
        //添加描述
        foreach ($data_list as $key => $dataval) {
            $data_list[$key]["desc"] = $InviteSettingLogic->getInviteDesc($dataval["invite_setting"], $codeTypeLists);
        }

        $userId && $userInfo = Db::name('user')->where(['id' => $userId])->find();
        if ($request->isPost()) {
            $userLogic = new UserLogic();
            $username = trim($request->param('username'));
            $mobile = trim($request->param('mobile'));

            $login_pwd = trim($request->param('login_pwd'));
            $relogin_pwd = trim($request->param('relogin_pwd'));
            $code = trim($request->param('code'));

            if (empty($code)) {
                $this->error('邀请费率不能为空,请先添加邀请配置');
            }

            if (strlen($login_pwd) < 7) {
                $this->error('密码必须大于6位');
            }
            if ($login_pwd != $relogin_pwd) {
                $this->error('两次密码不一致');
            }
            if ($userLogic->isMobile($mobile) == false) {
                $this->error('手机号码格式不正确');
            }
            $salt = strrand(4);
            $cuser = Db::name('user')->where(array('account' => $mobile))->find();
            $muser = Db::name('user')->where(array('mobile' => $mobile))->find();
            if (!empty($cuser) || !empty($muser)) {
                $re_data['status'] = 1;
                $re_data['message'] = "手机号已经被注册";
                ajaxReturn($re_data);
                exit;
            }
            $data['pid'] = 0;
            $data['gid'] = 0;
            $data['ggid'] = 0;
            //以下值要有默认值
            $data['email'] = $data['security_pwd'] = $data['usercard'] = $data['security_salt'] = $data['rz_st'] = '';
            $data['tx_status'] = $data['userqq'] = $data['u_ztnum'] = $data['group_id'] = 0;
            $data['zsy'] = 0.00;
            $data['account'] = $mobile;
            $data['mobile'] = $mobile;
            $data['u_yqm'] = $code;
            $data['username'] = $username;
            $data['login_pwd'] = $userLogic->pwd_md5($login_pwd, $salt);
            $data['login_salt'] = $salt;
            $data['reg_date'] = time();
            $data['reg_ip'] = $userLogic->get_userip();
            $data['status'] = 1;
            $adminId = session('user_auth.uid');
            $data['add_admin_id'] = ($adminId == 1) ? 0 : $adminId;
            $ure_re = Db::name('user')->insert($data);
            if ($ure_re !== false) {
                $this->success('添加成功', url('index'));
            }
            $this->error('添加失败');
        }
        $this->assign("data_list", $data_list);
        return $this->fetch();
    }


    /**
     * 平台手动调整用户余额
     */
    public function changeUserBalance(Request $request)
    {
        $userId = $request->param('userid');
        $curretuserMoney = Db::name('user')->where(['userid' => $userId])->value('money');
        if ($request->isPost()) {
            //看了存储引擎不支持事务算了 M()->startTrans();
            $data = $request->post();
            if (bccomp(0.00, $data['money']) != -1) {
                $this->error('操作资金不可小于或等于0.00');
            }
            if ($data['op_type'] == 0 && bccomp($data['money'], $curretuserMoney) == 1) { //减少
                $this->error('减少资金不可小于用户本金');
            }
            $userLogic = new UserLogic();
            $ret = $userLogic->accountLog($userId, 6, $data['op_type'], $data['money'], $data['opInfo']);
            //修改余额同时写入流水
            if ($ret) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        }
        $this->assign('curretuserMoney', $curretuserMoney);
        return $this->fetch('change_user_balance');

    }

    /**
     * 用户列表
     */
    public function index(Request $request)
    {
        $userobj = Db::table('ysk_user');
        $adminLogic = new AdminLogic();

        $groupId = $request->param('group_id', -1, 'intval');
        $this->assign('groupId', $groupId);
        $groups = $this->getGroups($groupId);

        if ($groups !== "") {
            $map['group_id'] = array("in", $groups . "");
        }
        $mobile = $request->param('mobile');
        (!empty($mobile)) && $map['mobile'] = ['like', "%" . $mobile . "%"];
        $map['userid'] = ['in', $adminLogic->tzUsers()];

        $listUser = $userobj->where($map)->order(' reg_date desc')
            ->paginate(20);
        $list = $listUser->items();

        $count = $listUser->total();
        // 获取分页显示
        $page = $listUser->render();

        if (is_array($list) && count($list) > 0) {
            foreach ($list as $k => $v) {
                $addAdminId = $v['add_admin_id'];
                $where['a.id'] = $addAdminId;
                $info = DB::name('admin')->alias('a')->field('a.id,b.title')
                    ->join('ysk_group b' ,'a.auth_id=b.id', "LEFT")
                    ->where($where)
                    ->find();
                $list[$k]['role_name'] = $info['id'] ? $info['title'] : "超级管理员";
                $groupInfo = Db::name('user_group')->field('name')->where(['id' => $v['group_id']])->find();
                $list[$k]['group_name'] = (!empty($groupInfo)) ? $groupInfo['name'] : '暂无分组';
                if ($v['work_status'] == UserGroupModel::STATUS_NOT_WORK) {
                    $list[$k]["work_status_name"] = "<a style='color: red'>停工中</a>";
                }
                if ($v['work_status'] == UserGroupModel::STATUS_WORK) {
                    $list[$k]["work_status_name"] = "<a style='color: green'>工作中</a>";
                }
                if(isset($v['pid']) && $v['pid']) {
                    $list[$k]['pid_account'] = Db::name('user')->where('userid', $v['pid'])->value('account');
                    $list[$k]['pid_account_count'] = Db::name('user')->where('userid', $v['pid'])->count();
                } else {
                    $list[$k]['pid_account'] = '无上级';
                    $list[$k]['pid_account_count'] = '';
                }
            }
        }
        //用户组别如果是团长就查询自己的设置组别
        $_map = [];
        $adminId = session('user_auth.uid');
        $adminLogic->checkIstz($adminId) && $_map['admin_id'] = $adminId;
        $groups = Db::name('user_group')->where($_map)->field('id,parentid,name')->select();
        $userLogic = new UserLogic();
        $this->assign('groups', $userLogic->getCategory($groups));
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch();
    }

    public static function getGroups($groupId)
    {
        if ($groupId == -1) {
            return "";
        }

        if ($groupId == 0) {
            return 0;
        }

        $GroupModel = new UserGroupModel();
        $groupModel = $GroupModel->find($groupId);
        $groups = [];
        if ($groupModel['parentid'] != 0) {
            return $groupId;
        }

        $groupLists = $GroupModel->where("parentid=" . $groupId)->select();
        foreach ($groupLists as $group) {
            $groups[] = $group["id"];
        }
        return empty($groups) ? "" : implode(",", $groups);
    }

    /**资金流水
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function bill(Request $request)
    {
        $someBill = Db::name('somebill');
        $adminLogic = new AdminLogic();
        $a_uid = $adminLogic->tzUsers();
        $map    = array();
        if(is_array($a_uid) && $a_uid) {
            $map['a.uid'] = ['in', $a_uid];
        }
        //增加的查询条件
        //时间
        $startTime = $request->param('start_time','', 'strtotime');
        $endTime =$request->param('end_time','','strtotime');
        if ($startTime && empty($endTime)) {
            $map['addtime'] = ['egt', $startTime];
        }
        if (empty($startTime) && $endTime) {
            $map['addtime'] = ['elt', $endTime];
        }
        if ($startTime && $endTime) {
            $map['addtime'] = ['between', [$startTime, $endTime]];
        }
        $billType =$request->param('bill_type', 0,'intval');
        $billType && $map['jl_class'] =$billType;
        $username = $request->param('username','','trim');
        $username  && $map ['b.username']=$username;

        $listData = $someBill->alias('a')
            ->join("ysk_user b", "a.uid=b.userid", "left")
            ->where($map)
            ->order('addtime desc')
            ->paginate(20);
            //->limit($p->firstRow, $p->listRows)->select();
        $list = $listData->items();
        $count = $listData->count();
        $page = $listData->render();
        //[1=>'佣金','团队奖励','充值','提现','订单匹配','平台操作','关闭订单'];//
        $bill_types = config('bill_types');
        /*foreach ($list as $key => $value) {

        }*/

        $this->assign('billTypes', $bill_types);
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch();
    }

    public function delbill(Request $request)
    {
        $id = trim($request->param('id'));
        $re = Db::name('somebill')->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
            exit;
        } else {
            $this->error('删除失败');
            exit;
        }
    }

    public function setWorkStatus(Request $request)
    {
        $status = $request->param('work_status');
        $ids = $request->param('ids');
        $data["work_status"] = $status;
        if (empty($ids)) {
            $this->error('失败');
        }

        $re = Db::name("user")->where(array('userid' => $ids))->update($data);
        if ($re === false) {
            $this->error('失败');
        }
        $this->success('成功', url('User/index'));
    }

    //提现列表
    public function recharge(Request $request)
    {
        $querytype = trim($request->param('querytype'));
        $account = trim($request->param('keyword'));
        $coinpx = trim($request->param('coinpx'));

        if ($querytype != '') {
            if ($querytype == 'mobile') {
                $map['account'] = $account;
            } elseif ($querytype == 'userid') {
                $map['uid'] = $account;
            }
        } else {
            $map = '';
        }

        $adminLogic = new AdminLogic();
        $userobj = Db::name('recharge');
        $map['uid'] = ['in', $adminLogic->tzUsers()];

        if ($coinpx) {
            if ($coinpx == 1) {
                $listData = $userobj->where($map)->order('price desc')->paginate(50);
            } else {
                $listData = $userobj->where($map)->order('id desc')->paginate(50);
            }
        } else {
            $listData = $userobj->where($map)->order('id desc')->paginate(50);
        }

        $count = $listData->count();
        $list = $listData->items();
        $page = $listData->render();

        $conf = Db::name('system')->where(array('id' => 1))->find();

        $this->assign('conf', $conf);
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集

        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch();

    }

    /** 充值管理，退回，完成 删除
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function reedit(Request $request)
    {
        $id = trim($request->param('id'));
        $st = trim($request->param('st'));
        $relist = Db::name('recharge')->where(array('id' => $id))->find();
        $ulist = Db::name('user')->where(array('userid' => $relist['uid']))->find();

        if ($st == 1) {
            if ($relist['status'] == 1) {
                $re = Db::name('recharge')->where(array('id' => $id))->update(array('status' => 3));
                $ure = Db::name('user')->where(array('userid' => $relist['uid']))->setField('money', $relist['price']);
            } else {
                $re = 0;
                $ure = 0;
            }


        } elseif ($st == 2) {
            if ($relist['status'] == 1) {
                $re = Db::name('recharge')->where(array('id' => $id))->update(array('status' => 2));
                $ure = 1;
            } else {
                $re = 0;
                $ure = 0;
            }


        } elseif ($st == 3) {
            if ($relist['status'] == 3) {
                $re = Db::name('recharge')->where(array('id' => $id))->delete();
                $ure = 1;
            } else {
                $re = 0;
                $ure = 0;
            }
        }

        if ($re && $ure) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }


    }

    /** 充值处理
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function save_czset(Request $request)
    {
        if ($_GET) {
            $data['cz_yh'] = trim($request->param('cz_yh'));
            $data['cz_xm'] = trim($request->param('cz_xm'));
            $data['cz_kh'] = trim($request->param('cz_kh'));

            $re = DB::name('system')->where(array('id' => 1))->update($data);

            if ($re) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败或数据没有变化');
            }
        }

    }

    //充值处理

    public function withdraw(Request $request)
    {
        $querytype = trim($request->param('querytype'));
        $account = trim($request->param('keyword'));
        $coinpx = trim($request->param('coinpx'));

        if ($querytype != '') {
            if ($querytype == 'mobile') {
                $map['account'] = $account;
            } elseif ($querytype == 'userid') {
                $map['uid'] = $account;
            }
        } else {
            $map = '';
        }

        $adminLogic = new AdminLogic();
        $userobj = Db::name('withdraw');
        $map['uid'] = ['in', $adminLogic->tzUsers()];
        /*$count = $userobj->where($map)->count();
        $p = getpagee($count, 50);*/

        if ($coinpx) {
            if ($coinpx == 1) {
                $listData = $userobj->where($map)->order('price desc')->paginate(50);
            } else {
                $listData = $userobj->where($map)->order('id desc')->paginate(50);
            }
        } else {
            $listData = $userobj->where($map)->order('id desc')->paginate(50);
        }
        $list = $listData->items();
        $count = $listData->total();
        // 获取分页显示
        $page = $listData->render();

        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch();

    }


    //提现列表

    public function wiedit(Request $request)
    {
        $id = trim($request->param('id'));
        $st = trim($request->param('st'));
        $relist = Db::name('withdraw')->where(array('id' => $id))->find();

        if ($st == 1) {
            $re = Db::name('withdraw')->where(array('id' => $id))->update(array('status' => 3));


        } elseif ($st == 2) {
            $re = Db::name('withdraw')->where(array('id' => $id))->update(array('status' => 2));


        } elseif ($st == 3) {
            $re = Db::name('withdraw')->where(array('id' => $id))->update(array('status' => 3));

        }

        if ($re) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }


    }

    /**提现处理
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function ewm(Request $request)
    {
        $adminLogic = new AdminLogic();
        $type = $request->param('type',0,'intval');
        $type &&  $map['type'] =$type;
        $map['a.user_id'] = ['in', $adminLogic->tzUsers()];

        $gemapayCode = Db::name('gemapay_code');
        $listGemaPayCode = $gemapayCode->alias('a')->field('a.*,b.account,b.username,c.type_name')
            ->join('ysk_user b' ,'a.user_id=b.userid', "LEFT")
            //->join('left join ' . C("DB_PREFIX") . 'user as b on a.user_id = b.userid')
            //->join('left join ' . C("DB_PREFIX") . 'gemapay_code_type as c on a.type = c.id')
            ->join('gemapay_code_type c' , 'a.type=c.id', "LEFT")
            ->where($map)->order('id desc')->paginate(50);

        $list = $listGemaPayCode->items();
        $count = $listGemaPayCode->total();
        // 获取分页显示
        $page = $listGemaPayCode->render();

        $this->assign('count', $count);
        $this->getcodeType();
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch();
    }


    //提现列表

    protected function getcodeType()
    {
        $codeTypes = Db::name('gemapay_code_type')->field('id,type_name')->order('sort asc,id desc')->select();
        $this->assign('codeTypes', $codeTypes);
    }


    //二维码详情

    public function ewminfo(Request $request)
    {
        if ($request->isPost()) {
            $id = trim($request->param('id'));
            $data["account_name"] = trim($request->param('account_name'));
            $data["account_number"] = trim($request->param('account_number'));
            $data["bonus_points"] = trim($request->param('bonus_points'));
            $data["limit_money"] = trim($request->param('limit_money'));
            $re = Db::name('gemapay_code')->where(array('id' => $id))->update($data);
            if ($re) {
                $this->success('操作成功', url('ewm'));
            } else {
                $this->error('操作失败');
            }
        }
        $this->getcodeType();
        $id = trim($request->param('id'));
        $ewminfo = Db::name('gemapay_code')->where(array('id' => $id))->find();
        //二微码类型
        if (isset($ewminfo['type']) && $ewminfo['type']) {
            $ewminfo['gemapay_code_type_name'] = Db::name('gemapay_code_type')->where('id', $ewminfo['type'])->value('type_name');
        } else {
            $ewminfo['gemapay_code_type_name'] = '';
        }
        $this->assign('info', $ewminfo);
        $this->assign('act', url('ewminfo'));

        return $this->fetch();
    }


    //删除二维码
    public function delewm(Request $request)
    {
        $id = trim($request->param('id'));
        $re = M('ewm')->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }


    /** 银行卡列表
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bankcard(Request $request)
    {
        $querytype = trim($request->param('querytype'));
        $account = trim($request->param('keyword'));
        $coinpx = trim($request->param('coinpx'));

        if ($querytype != '') {
            if ($querytype == 'mobile') {
                $map['name'] = $account;
            } elseif ($querytype == 'userid') {
                $map['uid'] = $account;
            }
        } else {
            $map = '';
        }
        $adminLogic = new AdminLogic();
        $userobj = Db::name('bankcard');
        $map['uid'] = ['in', $adminLogic->tzUsers()];

        if ($coinpx) {
            if ($coinpx == 1) {
                $listData = $userobj->where($map)->order('addtime desc')->paginate(50);
            } else {
                $listData = $userobj->where($map)->order('id desc')->paginate(50);
            }
        } else {
            $listData = $userobj->where($map)->order('id desc')->paginate(50);
        }
        $list = $listData->items();
        $count = $listData->count();
        $page = $listData->render();

        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出

        return $this->fetch();

    }


    //冻结会员
    public function set_status(Request $request)
    {
        if ($request->isGet()) {
            $userid = trim($request->param('userid'));
            $st = trim($request->param('st'));
            $list = Db::name('user')->where(array('userid' => $userid))->find();
            if (empty($list)) {
                $this->error('该会员不存在');
            }
            if ($st == 1) {
                $re = Db::name('user')->where(array('userid' => $userid))->update(array('status' => 0));
                if ($re) {
                    $this->success('该会员已被冻结', url('index'));
                } else {
                    $this->error('网络错误！');
                }

            } elseif ($st == 2) {
                $re = Db::name('user')->where(array('userid' => $userid))->update(array('status' => 1));
                if ($re) {
                    $this->success('该会员已被解冻',url('index'));
                } else {
                    $this->error('网络错误！');
                }

            } else {
                $this->error('网络错误！');
            }


        } else {
            $this->error('网络错误！');
        }


    }


    /**
     * 编辑用户
     *
     */
    public function del(Request $request)
    {
        $userid = trim($request->param('userid'));
        M('user')->where(array('userid' => $userid))->delete();
        $this->success('会员删除成功');
    }


    //限制出售币和提币
    public function restrict(Request $request)
    {
        $userid = trim($request->param('userid'));

        $ulist = Db::name('user')->where(array('userid' => $userid))->find();
        if ($request->isPost()) {
            //$sell_status = trim($request->param('sell_status'));
            $tx_status = trim($request->param('tx_status'));
            //$zz_status = trim($request->param('zz_status'));

            /*if ($ulist['sell_status'] == 1) {
                if ($sell_status != '') {
                    $data['sell_status'] = 0;
                }
            } else {
                if ($sell_status != '') {
                    $data['sell_status'] = 1;
                }
            }*/
            if ($ulist['tx_status'] == 1) {
                if ($tx_status != '') {
                    $data['tx_status'] = 0;
                }
            } else {
                if ($tx_status != '') {
                    $data['tx_status'] = 1;
                }
            }
            /*if ($ulist['zz_status'] == 1) {
                if ($zz_status != '') {
                    $data['zz_status'] = 0;
                }
            } else {
                if ($zz_status != '') {
                    $data['zz_status'] = 1;
                }
            }*/
            if(!isset($data['tx_status'])) {
                $this->error('参数错误，或没有做任何变更');
            }
            $re = Db::name('user')->where(array('userid' => $userid))->update($data);
            if ($re) {
                $this->success('修改成功', url('index'));
            } else {
                $this->error('修改失败', url('index'));
            }
        } else {

            $this->assign('info', $ulist);
            $this->assign('act', url('restrict'));
            return $this->fetch();
        }
    }


    /**
     * 设置一条或者多条数据的状态
     *
     */
    public function setStatus($model ='', $script = false)
    {

    }


    /**
     * 设置会员隐蔽的状态
     *
     */
    public function setStatus1($model, $script = false, Request $request)
    {
        $id = (int)$request->param('request.id');
        $userid = (int)$request->param('request.userid');

        $user_object = DB::name('user');
        $result = DB::name('User')->where(array('userid' => $userid))->setField('yinbi', $id);
        if ($result) {
            $this->success('更新成功', url('index'));
        } else {
            $this->error('更新失败', $user_object->getError());
        }
    }


    //用户登录
    public function userlogin(Request $request)
    {
        $userid = $request->param('userid', 0, 'intval');
        $user = D('Home/User');
        $info = $user->find($userid);
        if (empty($info)) {
            return false;
        }

        $login_id = $user->auto_login($info);
        if ($login_id) {
            session('in_time', time());
            session('login_from_admin', 'admin', 10800);
            $this->redirect('Home/Index/index');
        }
    }

    /**
     * 锁定二维码
     * @param mixed|string $model
     */
    public function updateLock($model = '', Request $request)
    {
        $id = $request->param('request.id');
        $status = $request->param('status');
        $map = array('id' => $id);

        switch ($status) {
            case 'lock': // 锁定
                $data = array('is_lock' => 1);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success' => '锁定成功', 'error' => '锁定失败')
                );
                break;
            case 'unlock': // 解锁
                $data = array('is_lock' => 0);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success' => '解锁成功', 'error' => '解锁失败')
                );
                break;
        }
    }
}
