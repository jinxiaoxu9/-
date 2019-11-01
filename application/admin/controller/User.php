<?php

namespace Admin\Controller;

use Admin\Model\UserInviteSetting;
use Admin\Model\UserGroupModel;
use Gemapay\Model\GemapayCodeTypeModel;


/**
 * 用户控制器
 *
 */
class User extends Admin
{


    /**
     * 编辑用户
     *
     */
    public function edit()
    {
        $userid = trim(I('get.userid'));
        $ulist = M('user')->where(array('userid' => $userid))->find();

        $InviteSettingModel = new UserInviteSetting();
        $where["admin_id"] = session("user_auth.uid");
        $data_list = $InviteSettingModel->where($where)->select();
        $CodeTypeList = new GemapayCodeTypeModel();
        $codeTypeLists = $CodeTypeList->getAllType();
        //添加描述
        foreach ($data_list as $key => $data) {
            $data_list[$key]["desc"] = $InviteSettingModel->getInviteDesc($data["invite_setting"], $codeTypeLists);
        }

        if ($_POST) {
            $code = trim(I('post.code'));
            $data['username'] = trim(I('post.username'));
            $data['mobile'] = trim(I('post.mobile'));
            $data['truename'] = trim(I('post.truename'));
            $data['nsc_money'] = trim(I('post.nsc_money'));
            $data['eth_money'] = trim(I('post.eth_money'));
            $data['eos_money'] = trim(I('post.eos_money'));
            $data['btc_money'] = trim(I('post.btc_money'));
            $data['money'] = trim(I('post.money'));
            $data['u_yqm'] = $code;
            $login_pwd = trim(I('post.login_pwd'));
            if (empty($code)) {
                $this->error('邀请费率不能为空,请先添加邀请配置');
            }
            if ($login_pwd != '') {
                $data['login_pwd'] = pwd_md5($login_pwd, $ulist['login_salt']);
            }

            $safety_pwd = trim(I('post.safety_pwd'));

            if ($login_pwd != '') {
                $data['safety_pwd'] = pwd_md5($safety_pwd, $ulist['safety_salt']);
            }
            $re = M('user')->where(array('userid' => $userid))->save($data);
            if ($re) {

                $this->success('资料修改成功');
            } else {
                $this->error('资料修改失败');

            }


        } else {
            $this->assign("data_list", $data_list);
            $this->assign('info', $ulist);
            $this->display();
        }

    }


    public function add()
    {
        $userId = I('user_id/d');
        $userInfo = null;
        $InviteSettingModel = new UserInviteSetting();
        $where["admin_id"] = session("user_auth.uid");
        $data_list = $InviteSettingModel->where($where)->select();
        $CodeTypeList = new GemapayCodeTypeModel();
        $codeTypeLists = $CodeTypeList->getAllType();
        //添加描述
        foreach ($data_list as $key => $data) {
            $data_list[$key]["desc"] = $InviteSettingModel->getInviteDesc($data["invite_setting"], $codeTypeLists);
        }

        $userId && $userInfo = M('user')->where(['id' => $userId])->find();
        if ($_POST) {

            $username = trim(I('post.username'));
            $mobile = trim(I('post.mobile'));
            $login_pwd = trim(I('post.login_pwd'));
            $relogin_pwd = trim(I('post.relogin_pwd'));
            $code = trim(I('post.code'));

            if (empty($code)) {
                $this->error('邀请费率不能为空,请先添加邀请配置');
            }

            if (strlen($login_pwd) < 7) {
                $this->error('密码必须大于6位');
            }
            if ($login_pwd != $relogin_pwd) {
                $this->error('两次密码不一致');
            }
            if (isMobile($mobile) == false) {
                $this->error('手机号码格式不正确');
            }
            $salt = strrand(4);
            $cuser = M('user')->where(array('account' => $mobile))->find();
            $muser = M('user')->where(array('mobile' => $mobile))->find();
            if (!empty($cuser) || !empty($muser)) {
                $re_data['status'] = 1;
                $re_data['message'] = "手机号已经被注册";
                ajaxReturn($re_data);
                exit;
            }
            $data['pid'] = 0;
            $data['gid'] = 0;
            $data['ggid'] = 0;
            $data['account'] = $mobile;
            $data['mobile'] = $mobile;
            $data['u_yqm'] = $code;
            $data['username'] = $username;
            $data['login_pwd'] = pwd_md5($login_pwd, $salt);
            $data['login_salt'] = $salt;
            $data['reg_date'] = time();
            $data['reg_ip'] = get_userip();
            $data['status'] = 1;
            $adminId = session('user_auth.uid');
            $data['add_admin_id'] = ($adminId == 1) ? 0 : $adminId;
            $ure_re = M('user')->add($data);
            if ($ure_re !== false) {
                $this->success('添加成功');
            }
            $this->error('添加失败');
        }
        $this->assign("data_list", $data_list);
        $this->display();
    }


    /**
     * 平台手动调整用户余额
     */
    public function changeUserBalance()
    {
        $userId = I('get.userid');
        $curretuserMoney = M('user')->where(['userid' => $userId])->getField('money');
        if ($_POST) {
            //看了存储引擎不支持事务算了 M()->startTrans();
            $data = I('post.');
            if (bccomp(0.00, $data['money']) != -1) {
                $this->error('操作资金不可小于或等于0.00');
            }
            if ($data['op_type'] == 0 && bccomp($data['money'], $curretuserMoney) == 1) { //减少
                $this->error('减少资金不可小于用户本金');
            }
            $ret = accountLog($userId, 6, $data['op_type'], $data['money'], $data['opInfo']);
            //修改余额同时写入流水
            if ($ret) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        }
        $this->assign('curretuserMoney', $curretuserMoney);
        $this->display('change_user_balance');

    }

    /**
     * 用户列表
     */
    public function index()
    {

        $userobj = M('user');
        $groupId = I('group_id', -1, 'intval');
        $this->assign('groupId', $groupId);
        $groups = $this->getGroups($groupId);

        if ($groups !== "") {
            $map['group_id'] = array("in", $groups . "");
        }
        $mobile = I('mobile/s');
        (!empty($mobile)) && $map['mobile'] = ['like', "%" . $mobile . "%"];
        $map['userid'] = ['in', tzUsers()];
        $count = $userobj->where($map)->count();
        $p = getpagee($count, 50);
        $list = $userobj->where($map)->order(' reg_date desc')->limit($p->firstRow, $p->listRows)->select();
        if (is_array($list) && count($list) > 0) {
            foreach ($list as $k => $v) {
                $addAdminId = $v['add_admin_id'];
                $where['a.id'] = $addAdminId;
                $info = M('admin')->alias('a')->field('a.id,b.title')
                    ->join('ysk_group  b ON a.auth_id=b.id', "LEFT")
                    ->where($where)
                    ->find();
                $list[$k]['role_name'] = $info['id'] ? $info['title'] : "超级管理员";
                $groupInfo = M('user_group')->field('name')->where(['id' => $v['group_id']])->find();
                $list[$k]['group_name'] = (!empty($groupInfo)) ? $groupInfo['name'] : '暂无分组';
                if ($v['work_status'] == UserGroupModel::STATUS_NOT_WORK) {
                    $list[$k]["work_status_name"] = "<a style='color: red'>停工中</a>";
                }
                if ($v['work_status'] == UserGroupModel::STATUS_WORK) {
                    $list[$k]["work_status_name"] = "<a style='color: green'>工作中</a>";
                }
            }
        }
        //用户组别如果是团长就查询自己的设置组别
        $_map = [];
        $adminId = session('user_auth.uid');
        checkIstz($adminId) && $_map['admin_id'] = $adminId;
        $groups = M('user_group')->where($_map)->field('id,parentid,name')->select();
        $this->assign('groups', getCategory($groups));
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display();
    }

    public function getGroups($groupId)
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

    //流水
    public function bill()
    {
        $userobj = M('somebill');
        $map['uid'] = ['in', tzUsers()];
        //增加的查询条件
        //时间
        $startTime = I('start_time','','strtotime');
        $endTime =I('end_time','','strtotime');
        if ($startTime && empty($endTime)) {
            $map['addtime'] = ['egt', $startTime];
        }
        if (empty($startTime) && $endTime) {
            $map['addtime'] = ['elt', $endTime];
        }
        if ($startTime && $endTime) {
            $map['addtime'] = ['between', [$startTime, $endTime]];
        }
        $billType =I('bill_type',0,'intval');
        $billType && $map['jl_class'] =$billType;
        $username = I('username','','trim');
        $username  && $map ['b.username']=$username;
        $count = $userobj->alias('a')
            ->join("ysk_user b on a.uid=b.userid", "left")
            ->where($map)
            ->count();

        $p = getpagee($count, 20);
        $list = $userobj->alias('a')
            ->join("ysk_user b on a.uid=b.userid", "left")
            ->where($map)
            ->order('addtime desc')->limit($p->firstRow, $p->listRows)->select();
        $this->assign('billTypes',C('bill_types'));
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display();
    }








    public function delbill()
    {
        $id = trim(I('get.id'));
        $re = M('somebill')->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
            exit;
        } else {
            $this->error('删除失败');
            exit;
        }
    }

    public function setWorkStatus()
    {
        $status = I('get.work_status');
        $ids = I('get.ids');
        $data["work_status"] = $status;
        if (empty($ids)) {
            $this->error('失败');
        }

        $re = M("user")->where(array('userid' => $ids))->save($data);
        if ($re === false) {
            $this->error('失败');
        }
        $this->success('成功', U('User/index'));
    }

    //提现列表
    public function recharge()
    {
        $querytype = trim(I('get.querytype'));
        $account = trim(I('get.keyword'));
        $coinpx = trim(I('get.coinpx'));
        if ($querytype != '') {
            if ($querytype == 'mobile') {
                $map['account'] = $account;
            } elseif ($querytype == 'userid') {
                $map['uid'] = $account;
            }
        } else {
            $map = '';
        }


        $userobj = M('recharge');
        $map['uid'] = ['in', tzUsers()];
        $count = $userobj->where($map)->count();
        $p = getpagee($count, 50);

        if ($coinpx) {
            if ($coinpx == 1) {
                $list = $userobj->where($map)->order('price desc')->limit($p->firstRow, $p->listRows)->select();
            } else {
                $list = $userobj->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
            }
        } else {
            $list = $userobj->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
        }

        $conf = M('system')->where(array('id' => 1))->find();
        $this->assign('conf', $conf);

        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display();

    }

    public function reedit()
    {
        $id = trim(I('get.id'));
        $st = trim(I('get.st'));
        $relist = M('recharge')->where(array('id' => $id))->find();
        $ulist = M('user')->where(array('userid' => $relist['uid']))->find();

        if ($st == 1) {
            if ($relist['status'] == 1) {
                $re = M('recharge')->where(array('id' => $id))->save(array('status' => 3));
                $ure = M('user')->where(array('userid' => $relist['uid']))->setInc('money', $relist['price']);
            } else {
                $re = 0;
                $ure = 0;
            }


        } elseif ($st == 2) {
            if ($relist['status'] == 1) {
                $re = M('recharge')->where(array('id' => $id))->save(array('status' => 2));
                $ure = 1;
            } else {
                $re = 0;
                $ure = 0;
            }


        } elseif ($st == 3) {
            if ($relist['status'] == 3) {
                $re = M('recharge')->where(array('id' => $id))->delete();
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


    //充值处理

    public function save_czset()
    {
        if ($_GET) {
            $data['cz_yh'] = trim(I('get.cz_yh'));
            $data['cz_xm'] = trim(I('get.cz_xm'));
            $data['cz_kh'] = trim(I('get.cz_kh'));

            $re = M('system')->where(array('id' => 1))->save($data);

            if ($re) {
                $this->success('修改成功');
                exit;
            } else {

                $this->error('修改失败');
                exit;
            }
        }

    }

    //充值处理

    public function withdraw()
    {
        $querytype = trim(I('get.querytype'));
        $account = trim(I('get.keyword'));
        $coinpx = trim(I('get.coinpx'));

        if ($querytype != '') {
            if ($querytype == 'mobile') {
                $map['account'] = $account;
            } elseif ($querytype == 'userid') {
                $map['uid'] = $account;
            }
        } else {
            $map = '';
        }


        $userobj = M('withdraw');
        $map['uid'] = ['in', tzUsers()];
        $count = $userobj->where($map)->count();
        $p = getpagee($count, 50);

        if ($coinpx) {
            if ($coinpx == 1) {
                $list = $userobj->where($map)->order('price desc')->limit($p->firstRow, $p->listRows)->select();
            } else {
                $list = $userobj->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
            }
        } else {
            $list = $userobj->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
        }


        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display();

    }


    //提现列表

    public function wiedit()
    {
        $id = trim(I('get.id'));
        $st = trim(I('get.st'));
        $relist = M('withdraw')->where(array('id' => $id))->find();

        if ($st == 1) {
            $re = M('withdraw')->where(array('id' => $id))->save(array('status' => 3));


        } elseif ($st == 2) {
            $re = M('withdraw')->where(array('id' => $id))->save(array('status' => 2));


        } elseif ($st == 3) {
            $re = M('withdraw')->where(array('id' => $id))->save(array('status' => 3));

        }

        if ($re) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }


    }


    //提现处理

    public function ewm()
    {

        $type = I('type',0,'intval');
        $type &&  $map['type'] =$type;
        $map['a.user_id'] = ['in', tzUsers()];
        $userobj = M('gemapay_code');
        $count = $userobj->alias('a')
            ->join('left join ' . C("DB_PREFIX") . 'user as b on a.user_id = b.userid')
            ->where($map)->count();
        $p = getpagee($count, 50);
        $list = $userobj->alias('a')->field('a.*,b.account,b.username,c.type_name')
            ->join('left join ' . C("DB_PREFIX") . 'user as b on a.user_id = b.userid')
            ->join('left join ' . C("DB_PREFIX") . 'gemapay_code_type as c on a.type = c.id')
            ->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

        $this->assign('count', $count);
        $this->getcodeType();
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display();
    }


    //提现列表

    protected function getcodeType()
    {
        $codeTypes = M('gemapay_code_type')->field('id,type_name')->order('sort asc,id desc')->select();
        $this->assign('codeTypes', $codeTypes);
    }


    //二维码详情

    public function ewminfo()
    {
        if ($_POST) {
            $id = trim(I('post.id'));
            $data["account_name"] = trim(I('post.account_name'));
            $data["account_number"] = trim(I('post.account_number'));
            $data["bonus_points"] = trim(I('post.bonus_points'));
            $data["limit_money"] = trim(I('post.limit_money'));
            $re = M('gemapay_code')->where(array('id' => $id))->save($data);
            if ($re) {
                $this->success('操作成功', U('ewm'));
            } else {
                $this->error('操作失败');
            }
        }
        $this->getcodeType();
        $id = trim(I('get.id'));
        $ewminfo = M('gemapay_code')->where(array('id' => $id))->find();
        $this->assign('info', $ewminfo);
        $this->display();
    }


    //删除二维码
    public function delewm()
    {
        $id = trim(I('get.id'));
        $re = M('ewm')->where(array('id' => $id))->delete();
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }


    //银行卡列表
    public function bankcard()
    {
        $querytype = trim(I('get.querytype'));
        $account = trim(I('get.keyword'));
        $coinpx = trim(I('get.coinpx'));

        if ($querytype != '') {
            if ($querytype == 'mobile') {
                $map['name'] = $account;
            } elseif ($querytype == 'userid') {
                $map['uid'] = $account;
            }
        } else {
            $map = '';
        }


        $userobj = M('bankcard');
        $map['uid'] = ['in', tzUsers()];
        $count = $userobj->where($map)->count();
        $p = getpagee($count, 50);

        if ($coinpx) {
            if ($coinpx == 1) {
                $list = $userobj->where($map)->order('addtime desc')->limit($p->firstRow, $p->listRows)->select();
            } else {
                $list = $userobj->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
            }
        } else {
            $list = $userobj->where($map)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
        }


        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $p->show()); // 賦值分頁輸出
        $this->display();

    }


    //冻结会员
    public function set_status()
    {
        if ($_GET) {
            $userid = trim(I('get.userid'));
            $st = trim(I('get.st'));
            $list = M('user')->where(array('userid' => $userid))->find();
            if (empty($list)) {
                $this->error('该会员不存在');
            }
            if ($st == 1) {
                $re = M('user')->where(array('userid' => $userid))->save(array('status' => 0));
                if ($re) {
                    $this->success('该会员已被冻结',U('index'));
                } else {
                    $this->error('网络错误！');
                }

            } elseif ($st == 2) {
                $re = M('user')->where(array('userid' => $userid))->save(array('status' => 1));
                if ($re) {
                    $this->success('该会员已被解冻',U('index'));
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
    public function del()
    {
        $userid = trim(I('get.userid'));
        M('user')->where(array('userid' => $userid))->delete();
        $this->success('会员删除成功');
    }


    //限制出售币和提币
    public function restrict()
    {
        $userid = trim(I('get.userid'));
        $ulist = M('user')->where(array('userid' => $userid))->find();
        if ($_POST) {

            $sell_status = trim(I('post.sell_status'));

            $tx_status = trim(I('post.tx_status'));

            $zz_status = trim(I('post.zz_status'));

            if ($ulist['sell_status'] == 1) {

                if ($sell_status != '') {
                    $data['sell_status'] = 0;
                }

            } else {

                if ($sell_status != '') {

                    $data['sell_status'] = 1;

                }

            }

            if ($ulist['tx_status'] == 1) {

                if ($tx_status != '') {
                    $data['tx_status'] = 0;
                }
            } else {

                if ($tx_status != '') {
                    $data['tx_status'] = 1;
                }
            }

            if ($ulist['zz_status'] == 1) {

                if ($zz_status != '') {
                    $data['zz_status'] = 0;
                }
            } else {

                if ($zz_status != '') {
                    $data['zz_status'] = 1;
                }
            }

            $re = M('user')->where(array('userid' => $userid))->save($data);

            if ($re) {

                $this->success('修改成功');

            } else {
                $this->error('修改失败');
            }


        } else {

            $this->assign('info', $ulist);
            $this->display();
        }
    }


    /**
     * 设置一条或者多条数据的状态
     *
     */
    public function setStatus($model = CONTROLLER_NAME)
    {

    }


    /**
     * 设置会员隐蔽的状态
     *
     */
    public function setStatus1($model = CONTROLLER_NAME)
    {
        $id = (int)I('request.id');
        $userid = (int)I('request.userid');

        $user_object = D('User');
        $result = D('User')->where(array('userid' => $userid))->setField('yinbi', $id);
        if ($result) {
            $this->success('更新成功', U('index'));
        } else {
            $this->error('更新失败', $user_object->getError());
        }
    }


    //用户登录
    public function userlogin()
    {
        $userid = I('userid', 0, 'intval');
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
    public function updateLock($model = CONTROLLER_NAME)
    {
        $id = I('request.id');
        $status = I('status');
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
