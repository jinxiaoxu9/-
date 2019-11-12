<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;

class UserController extends CommonController
{

    /*
     * 用户注册
     */
    public function register()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('');
            //基本参数校验
            $checkParams = $this->validParams($params, ['invent_code', 'username', 'mobile', 'login_pwd']);
            if($checkParams['status'] != 1){
                ajaxReturn($checkParams['message'], $checkParams['status']);
            }

            $logicRet  = $this->logicUser->register($params);
            ajaxReturn($logicRet['message'],$logicRet['status']);
        }
    }


    /**
     * 用户登录
     */
    public function  login(){
        if ($this->request->isPost())
        {
            $params = $this->request->post('');
            //基本参数校验
            $checkParams = $this->validParams($params, ['account', 'password']);
            if($checkParams['status'] != 1){
                ajaxReturn($checkParams['message'], $checkParams['status']);
            }

            $logicRet  = $this->logicUser->login($params['account'],$params['password']);
            if ($logicRet['code'] == CodeEnum::ERROR)
            {
                ajaxReturn($logicRet['msg'],0);
            }
            ajaxReturn('登录成功',1,'', $logicRet['data']);
        }
    }

    /**
     * 获取用户基本信息
     */
    public function  userBasicInfo(){
        $uid = $this->user_id;
        $fields  ='userid,mobile,money,work_status,status,username,wx_no,activate,alipay,truename,email,userqq,add_admin_id,group_id,token' ;
        $user  = $this->modelUser->getUser(['userid' =>$uid ],$fields);
        ajaxReturn('success',CodeEnum::SUCCESS,'',$user);
    }









    /****************************************end******************************************/







    public function test()
    {
        echo LOGIC_LAYER_NAME;
    }


    public function index()
    {
        $UserLogic = new UserLogic();
        $data = $UserLogic->getIndexInfo($where['userid'] = $this->user_id);
        $this->assign('userNavConfig', $data['config']);
        $this->assign('list', $data['userinfo']);
        $this->display();
    }

    //个人信息
    public function xinxi()
    {
        $uid = $this->user_id;
        $ulist = M('user')->where(array('userid' => $uid))->find();
        $this->assign('list', $ulist);
        $this->display();
    }

    //个人信息
    public function bill()
    {
        $uid = $this->user_id;
        $userobj = M('somebill');

        $count = $userobj->where(array('userid' => $uid))->count();

        $list = $userobj->where(array('uid' => $uid))->order('id desc')->limit($p->firstRow, $p->listRows)->select();

        foreach ($list as $k => $v) {
            $info = $v['info'];
            $list[$k]['info'] = mb_substr($info, 0, 7) . '...';
        }
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        //$this->assign ( 'page', $p->show() ); // 賦值分頁輸出
        $this->display();
    }

    //个人信息
    public function yjbill()
    {
        $uid = $this->user_id;
        $userobj = M('somebill');

        $count = $userobj->where(array('userid' => $uid))->count();
        //$p = $this->getpage($count,15);

        $list = $userobj->where(array('uid' => $uid, 'jl_class' => 5))->order('id desc')->limit($p->firstRow, $p->listRows)->select();

        $this->assign('list', $list); // 賦值數據集
        //print_R($list);
        $this->assign('count', $count);
        //$this->assign ( 'page', $p->show() ); // 賦值分頁輸出
        $this->display();
    }

    public function getpage(&$m, $where, $pagesize = 10)
    {
        $m1 = clone $m;//浅复制一个模型
        $count = $m->where($where)->count();//连惯操作后会对join等操作进行重置
        $m = $m1;//为保持在为定的连惯操作，浅复制一个模型
        $p = new Think\Page($count, $pagesize);
        $p->lastSuffix = false;
        $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $p->setConfig('prev', '上一页');
        $p->setConfig('next', '下一页');
        $p->setConfig('last', '末页');
        $p->setConfig('first', '首页');

        $p->parameter = I('get.');

        $m->limit($p->firstRow, $p->listRows);

        return $p;
    }

    //修改密码
    public function password()
    {
        $uid = $this->user_id;
        $ulist = M('user')->where(array('userid' => $uid))->find();
        $this->assign('info', $ulist);
        $this->display();
    }

    //资料
    public function zichan()
    {
        $uid = $this->user_id;
        $userinfo = M("user")->find($uid);

        $code = $userinfo["u_yqm"];
        $InviteSettingModel = new UserInviteSetting();
        $setting = $InviteSettingModel->where(array("code" => $code))->find();
        $setting = json_decode($setting["invite_setting"]);

        $GemapayCodeTypeModel = new GemapayCodeTypeModel();
        $codeTypeLists = $GemapayCodeTypeModel->getAllType();
        $codeTypeLists = filterDataMap($codeTypeLists, "id");
        $myCodeFee = [];
        foreach ($setting as $key => $s) {
            if (!empty($s)) {
                $codeTypeLists[$key]["max"] = $s;
                $myCodeFee[] = $codeTypeLists[$key];
            }
        }

        $ulist = M('user')->where(array('userid' => $this->user_id))->find();
        //奖励金额
        $sum_jj = M('somebill')->where(array('uid' => $this->user_id, 'jl_class' => 1))->sum('num');;
        //佣金费率
        $clist = M('system')->where(array('id' => 1))->find();
        $sum_ysk = M('gemapay_order')->where(array('gema_userid' => $this->user_id, 'status' => 1))->sum('order_price');
        $sum_dsk = M('gemapay_order')->where(array('gema_userid' => $this->user_id, 'status' => 0))->sum('order_price');
        $this->assign('sum_dsk', $sum_dsk); //待收款
        $this->assign('sum_ysk', $sum_ysk);//已收款
        $this->assign('clist', $clist);
        $this->assign('code_type_lists', $codeTypeLists);
        $this->assign('sum_jj', $sum_jj);//奖励
        $this->assign('info', $ulist);
        $this->display();
    }

    //重置密码
    public function set_password()
    {
        if ($_POST) {

            $uid = $this->user_id;
            $ulist = M('user')->where(array('userid' => $uid))->find();
            $salt = $ulist['login_salt'];

            $password = trim(I('post.password'));
            $sava['login_pwd'] = pwd_md5($password, $salt);
            $re = M('user')->where(array('userid' => $uid))->save($sava);
            if ($re) {
                $data['status'] = 1;
                $data['msg'] = '设置成功';
                ajaxReturn($data);
                exit;
            } else {
                $data['status'] = 0;
                $data['msg'] = '设置失败';
                ajaxReturn($data);
                exit;
            }

        } else {
            $data['status'] = 0;
            $data['msg'] = '网络错误';
            ajaxReturn($data);
            exit;
        }
    }

    //个人资料
    public function ziliao()
    {
        $this->display();
    }

    //保存资料
    public function set_info()
    {
        if ($_POST) {
            $mobile = trim(I('post.mobile'));
            $save['username'] = trim(I('post.username'));
            $save['truename'] = trim(I('post.truename'));
            $save['email'] = trim(I('post.email'));
            $save['userqq'] = trim(I('post.userqq'));
            $save['wx_no'] = trim(I('post.wx_no'));
            $save['alipay'] = trim(I('post.alipay'));
            $save['usercard'] = trim(I('post.usercard'));
            $save['rz_st'] = 1;

            $re = M('user')->where(array('account' => $mobile))->save($save);
            if ($re) {
                $data['status'] = 1;
                $data['msg'] = '保存成功';
                ajaxReturn($data);
                exit;
            } else {
                $data['status'] = 0;
                $data['msg'] = '保存失败';
                ajaxReturn($data);
                exit;
            }
        } else {
            $data['status'] = 0;
            $data['msg'] = '网络错误';
            ajaxReturn($data);
            exit;
        }
    }

    //银行卡管理

    public function yinhangka()
    {
        $uid = $this->user_id;
        $clist = M('bankcard')->where(array('uid' => $uid))->order('id desc')->select();
        $this->assign("clist", $clist);
        $this->display();
    }

    //添加银行卡处理
    public function tjyhk()
    {

        $this->display();
    }

    //添加银行卡处理
    public function set_addcard()
    {
        if ($_POST) {
            $uid = $this->user_id;
            $save['bankname'] = trim(I('post.bankname'));
            $save['name'] = trim(I('post.name'));
            $save['banknum'] = trim(I('post.banknum'));
            $save['uid'] = $uid;
            $save['addtime'] = time();

            $only = M('bankcard')->where(array('banknum' => $save['banknum']))->count();
            if ($only >= 1) {
                $data['status'] = 0;
                $data['msg'] = '该银行卡已存在';
                ajaxReturn($data);
                exit;
            }
            $cnum = M('bankcard')->where(array('uid' => $uid))->count();
            if ($cnum > 10) {
                $data['status'] = 0;
                $data['msg'] = '账号已达上限';
                ajaxReturn($data);
                exit;
            }
            $re = M('bankcard')->add($save);
            if ($re) {
                $data['status'] = 1;
                $data['msg'] = '保存成功';
                ajaxReturn($data);
                exit;
            } else {
                $data['status'] = 0;
                $data['msg'] = '保存失败';
                ajaxReturn($data);
                exit;
            }
        } else {
            $data['status'] = 0;
            $data['msg'] = '网络错误';
            ajaxReturn($data);
            exit;
        }
    }


    public function security()
    {
        $SecurityLogic = new SecurityLogic();
        $haveSecurity = $SecurityLogic->checkHadSetSecurity($this->user_id);
        $this->assign('hava_security', $haveSecurity);
        $this->display();
    }

    public function updatesSecurity()
    {
        if ($_POST) {
            $SecurityLogic = new SecurityLogic();
            $security = trim(I('post.new_security'));
            $re_security = trim(I('post.re_new_security'));
            $old_security = trim(I('post.old_security'));
            $res = $SecurityLogic->changeSecurity($this->user_id, $security, $re_security, $old_security);
            if ($res['code'] == CodeEnum::ERROR) {
                $data['status'] = 0;
                $data['msg'] = '保存失败,' . $res['msg'];
                ajaxReturn($data);
            }
            $data['status'] = 1;
            $data['msg'] = '保存成功';
            ajaxReturn($data);
            exit;
        }
    }

    //删除银行卡
    public function del_bankcard()
    {
        if ($_POST) {
            $id = trim(I('post.cid'));
            $cardinof = M('bankcard')->where(array('id' => $id))->find();
            if ($cardinof) {
                $re = M('bankcard')->where(array('id' => $id))->delete();
                if ($re) {
                    $data['status'] = 1;
                    $data['msg'] = '删除成功';
                    ajaxReturn($data);
                    exit;
                } else {
                    $data['status'] = 0;
                    $data['msg'] = '该银行卡已被删除';
                    ajaxReturn($data);
                    exit;
                }
            } else {
                $data['status'] = 0;
                $data['msg'] = '该银行卡已被删除';
                ajaxReturn($data);
                exit;
            }
        } else {
            $data['status'] = 0;
            $data['msg'] = '网络错误';
            ajaxReturn($data);
            exit;
        }
    }

    public function invitecode()
    {
        $uid = $this->user_id;
        $InviteSettingModel = new UserInviteSetting();
        $GemapayCodeTypeModel = new GemapayCodeTypeModel();
        $codeTypeLists = $GemapayCodeTypeModel->getAllType();
        $inventList = $InviteSettingModel->where(array('user_id' => $uid))->order("id desc")->select();
        foreach ($inventList as $key => $i) {
            $inventList[$key]["desc"] = $InviteSettingModel->getInviteDesc($i["invite_setting"], $codeTypeLists);
            $inventList[$key]["invite_url"] = $InviteSettingModel->getInviteLink($i["code"]);
        }
        $this->assign('invent_list', $inventList);
        $this->display();
    }

    public function addinvitecode()
    {
        $uid = $this->user_id;
        $userinfo = M("user")->find($uid);
        $code = $userinfo["u_yqm"];
        $InviteSettingModel = new UserInviteSetting();
        $setting = $InviteSettingModel->where(array("code" => $code))->find();
        $setting = json_decode($setting["invite_setting"]);

        if ($_POST) {
            /*  if (empty($userinfo["rz_st"])) {
                  $data['status'] = 0;
                  $data['msg'] = '用户未认证,或者系统未开通认证功能';
                  ajaxReturn($data);
                  exit;
              }*/
            $CodeTypeList = new GemapayCodeTypeModel();
            $codeTypeLists = $CodeTypeList->getAllType();
            $psetting = [];
            foreach ($codeTypeLists as $type) {
                if (I('post.type_' . $type["id"])) {
                    $points = I('post.type_' . $type["id"]);
                    if ($points > $setting->$type["id"]) {
                        $data['status'] = 0;
                        $data['msg'] = '发布失败,费率超过范围';
                        ajaxReturn($data);
                        exit;
                    }
                } else {
                    $points = 0;
                }
                $psetting[$type["id"]] = $points;
            }
            $Setting = M('user_invite_setting');
            $data["invite_setting"] = json_encode($psetting);
            $data["user_id"] = $this->user_id;
            $data["code"] = strrand(9);
            $data["admin_id"] = 0;
            $data["create_time"] = time();
            $re = $Setting->add($data);
            if ($re) {
                $data['status'] = 1;
                $data['msg'] = '发布成功';
                ajaxReturn($data);
                exit;
            } else {

                $data['status'] = 0;
                $data['msg'] = '发布失败';
                ajaxReturn($data);
                exit;
            }

        }

        $data = [];
        $GemapayCodeTypeModel = new GemapayCodeTypeModel();
        $codeTypeLists = $GemapayCodeTypeModel->getAllType();
        $codeTypeLists = filterDataMap($codeTypeLists, "id");
        $noemptySetting = [];
        foreach ($setting as $key => $s) {
            if (!empty($s)) {
                $codeTypeLists[$key]["max"] = $s;
                $noemptySetting[] = $codeTypeLists[$key];
            }
        }

        $this->assign('relist', 1);
        $this->assign('setting', $noemptySetting);
        $this->display();
    }

    //充值记录 管理
    public function rechargelist()
    {

        $this->display();
    }

    public function gorecharge()
    {

        $this->display();
    }


    public function wxerweima()
    {
        $uid = $this->user_id;
        $wxlist = M('ewm')->where(array('uid' => $uid, 'ewm_class' => 1))->select();
        $this->assign('wxlist', $wxlist);
        $this->display();
    }

    public function zfberweima()
    {
        $uid = $this->user_id;
        $wxlist = M('ewm')->where(array('uid' => $uid, 'ewm_class' => 2))->select();
        $this->assign('wxlist', $wxlist);
        $this->display();
    }


    public function yhkerweima()
    {
        $uid = $this->user_id;
        $wxlist = M('ewm')->where(array('uid' => $uid, 'ewm_class' => 3))->select();
        $this->assign('wxlist', $wxlist);
        $this->display();
    }

    //二维码详情
    public function erweimainfo()
    {
        header("Content-type:text/html;charset=utf-8");
        $id = trim(I('get.id'));
        $gemacodeinfo = M('gemapay_code')->where(array('id' => $id))->find();
        if (empty($gemacodeinfo)) {
            die("<script type='text/javascript'>window.location.href='javascript:history.go(-2)'</script>");
        } else {
            $this->assign('gemaCodeinfo', $gemacodeinfo);
            $this->display();
        }
    }

    //删除二维码
    public function delewmup()
    {
        if ($_POST) {
            $id = trim(I('post.id'));
            $ewmlist = M('gemapay_code')->where(array('id' => $id))->find();
            if (empty($ewmlist)) {
                $data['code'] = 0;
                $data['msg'] = '该二维码已被删除';
                ajaxReturn($data);
                exit;
            }
            $result = M('gemapay_code')->where(array('id' => $id))->delete();
            if ($result) {
                $data['code'] = 1;
                $data['msg'] = '删除成功';
                ajaxReturn($data);
                exit;
            } else {
                $data['code'] = 0;
                $data['msg'] = '删除失败';
                ajaxReturn($data);
                exit;
            }

        } else {
            $data['code'] = 1;
            $data['msg'] = '非法操作';
            ajaxReturn($data);
            exit;
        }
    }


    //发布页上传图片
    public function uploadfile()
    {
        if (C('ttk_open') == 2) {#阿里云oss
            //oss上传
            $bucketName = C('OSS_TEST_BUCKET');
            $ossClient = new \Org\OSS\OssClient(C('OSS_ACCESS_ID'), C('OSS_ACCESS_KEY'), C('OSS_ENDPOINT'), false);
            $web = C('OSS_WEB_SITE');
            //图片

            $fFiles = $_FILES['file'];
            //print_r($fFiles);die;
            $rs = ossUpPic($fFiles, 'ekcms', $ossClient, $bucketName, $web, 0);

            if ($rs['code'] == 1) {
                //图片
                $img = $rs['url'];
                $ajax['status'] = 1;
                $ajax['info'] = '上传成功';
                $ajax['data']['filename'] = $img;
                $ajax['data']['thumb'] = $img;
                $ajax['data']['url'] = $img;
                ajaxReturn($ajax);
            } else {
                $ajax['status'] = 0;
                $ajax['info'] = $rs['msg'];
                ajaxReturn($ajax);
            }

        } elseif (C('ttk_open') == 1) {#贴图库
            $ttk = new TTKClient(C('tietuku_accesskey'), C('tietuku_secretkey'));
            $res = $ttk->uploadFile(C('tietuku_aid'), $_FILES['file']['tmp_name'], $_FILES['file']['name']);
            $res = json_decode($res, true);
            if (!empty($res['linkurl'])) {
                $ajax['status'] = 1;
                $ajax['info'] = '上传成功';
                $ajax['data']['filename'] = $res['findurl'] . '.' . $res['type'];
                $ajax['data']['thumb'] = $res['t_url'];
                $ajax['data']['url'] = $res[C('tietuku_return_type')];
                ajaxReturn($ajax);
            } else {
                $ajax['status'] = 0;
                $ajax['info'] = $res['info'];
                ajaxReturn($ajax);
            }
            //$res=$ttk->uploadFromWeb('你的相册ID','网络图片地址');

        } else {
            $mimes = array('image/jpeg', 'image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
            $exts = array('jpeg', 'jpg', 'jpeg', 'png', 'pjpeg', 'gif', 'bmp', 'x-png');
            $rootPath = 'Public/';
            $savePath = 'attached/' . date('Y') . "/" . date('m') . "/";
            mkdirs($rootPath . $savePath);

            $upload = new Upload(array(
                'mimes' => $mimes,
                'exts' => $exts,
                'rootPath' => $rootPath,
                'savePath' => $savePath,
                'subName' => array('date', 'd'),
            ));

            $info = $upload->upload($_FILES);
            if ($info) {
                foreach ($info as $item) {
                    $file_path = "Public/" . $item['savepath'] . $item['savename'];
                    $filePath[] = $file_path;
                    //如果上传的二维码
                    $code = I('get.qrcode');
                    if (!empty($code)) {
                        $ret = getRawQrImage($file_path);
                        if (false == $ret) {
                            $ajax['status'] = 0;
                            $ajax['info'] = "没有识别到收款二维码,请截图精确的二维码上传";
                            ajaxReturn($ajax);
                        }
                        $filePath[] = $ret['path'];
                    }

                }
                $ImgStr = implode("?", $filePath);
                $ajax['status'] = 1;
                $ajax['info'] = '上传成功';
                $ajax['data']['filename'] = $ImgStr;
                $ajax['data']['thumb'] = $ImgStr;
                $ajax['data']['url'] = $ImgStr;
                ajaxReturn($ajax);
            } else {

                $ajax['status'] = 0;
                $ajax['info'] = $upload->getError();
                ajaxReturn($ajax);
            }
        }
    }

    /*     public function Imgup()
        {
            $uid = s$this->user_id;
            $picname = $_FILES['uploadfile']['name'];
            $picsize = $_FILES['uploadfile']['size'];
            if ($uid != "") {
                if ($picsize > 2014000) { //限制上传大小
                    ajaxReturn('图片大小不能超过2M', 0);
                }
                $type = strstr($picname, '.'); //限制上传格式
                if ($type != ".gif" && $type != ".jpg" && $type != ".png" && $type != ".jpeg") {
                    ajaxReturn('图片格式不对', 0);
                }
                $rand = rand(100, 999);
                $pics = uniqid() . $type; //命名图片名称
                //上传路径
                $pic_path = "./Public/home/wap/heads/" . $pics;
                move_uploaded_file($_FILES['uploadfile']['tmp_name'], $pic_path);
            }
            $size = round($picsize / 1024, 2); //转换成kb
            $pic_path = trim($pic_path, '.');
            if ($size) {
                $res = M('user')->where(array('user_id => $uid))->setField('img_head', $pics);
                ajaxReturn($pic_path, 1);
            }
        } */


    public function imgUps()
    {
        if (IS_AJAX) {
            $uid = $this->user_id;
            $dataflow = trim(I('dataflow'));
            $base64 = str_replace('data:image/jpeg;base64,', '', $dataflow);
            $img = base64_decode($base64);
            //保存地址
            $imgDir = './Public/home/wap/heads/';
            //要生成的图片名字
            $filename = md5(time() . mt_rand(10, 99)) . ".png"; //新图片名称
            $newFilePath = $imgDir . $filename;
            $res = file_put_contents($newFilePath, $img);//返回的是字节数
            if ($res > 1000) {
                //修改头像
                $res_change = M('user')->where(array('userid' => $uid))->setField('img_head', $filename);
                if ($res_change) {
                    ajaxReturn('头像修改成功', 1);
                } else {
                    ajaxReturn('头像修改失败', 0);
                }
            } else {
                ajaxReturn('头像修改失败', 0);
            }
        }
    }


    public function Setpwd()
    {
        $type = trim(I('type'));

        if ($type == 1) {
            $title = '修改登录密码';
        } else {
            $title = '修改交易密码';
        }
        if (IS_AJAX) {
            $user = D('Home/User');
            $user_object = D('Home/User');
            $uid = $this->user_id;
            $pwd = trim(I('pwd'));
            $pwdrpt = trim(I('pwdrpt'));
            $type = trim(I('pwdtype'));
            if ($pwdrpt == '') {
                ajaxReturn('新密码不能为空哦', 0);
            }
            $account = M('user')->where(array('userid' => $uid))->Field('account,mobile,login_pwd')->find();
            //验证初始密码
            $user_info = $user_object->Savepwd($account['mobile'], $pwd, $type);
            $salt = substr(md5(time()), 0, 3);
            if ($type == 1) {
                //密码加密
                $data['login_pwd'] = $user->pwdMd5($pwdrpt, $salt);
                $data['login_salt'] = $salt;
            } else {
                $data['safety_pwd'] = $user->pwdMd5($pwdrpt, $salt);
                $data['safety_salt'] = $salt;
            }
            $res_Sapwd = M('user')->where(array('userid' => $uid))->save($data);
            if ($res_Sapwd) {
                ajaxReturn('密码修改成功', 1, '/User/Personal');
            } else {
                ajaxReturn('密码修改失败', 0);
            }
        }
        $this->assign('title', $title);
        $this->assign('type', $type);
        $this->display();
    }

    public function News()
    {
        $newinfo = M('news')->order('id desc')->limit(8)->select();
        $this->assign('newinfo', $newinfo);
        $this->display();
    }

    public function Newsdetail()
    {
        $nid = I('nid', 'intval', 0);
        $newdets = M('news')->where(array('id' => $nid))->find();
        $this->assign('newdets', $newdets);
        $this->display();
    }

    //个人二维码
    public function Sharecode()
    {
        $time = time();
        $user_id = $this->user_id;

        $u_ID = M('user')->where(array('userid' => $user_id))->getField('u_yqm');
        $drpath = './Uploads/Scode';
        $imgma = 'codes' . $user_id . '.png';
        $urel = './Uploads/Scode/' . $imgma;
        if (!file_exists($drpath . '/' . $imgma)) {
            sp_dir_create($drpath);
            vendor("phpqrcode.phpqrcode");
            $phpqrcode = new \QRcode();
            $hurl = "http://" . $_SERVER['SERVER_NAME'] . U('Login/register/mobile/' . $u_ID);
            $size = "7";
            //$size = "10.10";
            $errorLevel = "L";
            $phpqrcode->png($hurl, $drpath . '/' . $imgma, $errorLevel, $size);


            $phpqrcode->scerweima1($hurl, $urel, $hurl);


        }
        $aurl = "http://" . $_SERVER['SERVER_NAME'] . U('Login/register/mobile/' . $u_ID);

        $this->urel = ltrim($urel, ".");
        $this->aurl = $aurl;
        $this->display();
    }


    //我的团队
    public function Teamdets()
    {
        //查询我的会员
        $uid = $this->user_id;
        if (IS_POST) {
            $uinfo = trim(I('uinfo'));
            if (!empty($uinfo) && $uinfo != '') {
                $where['mobile'] = array('like', '%' . $uinfo . '%');
                $this->assign('uinfo', $uinfo);
            }
        }
        $where['pid'] = $uid;
        $muinfo = M('user')->where($where)->order('user_id desc')->select();

        $this->assign('muinfo', $muinfo);
        $this->display();
    }

    /**
     * 修改密码
     */
    public function updatepassword()
    {
        if (!IS_AJAX)
            return;

        $password_old = I('post.old_pwdt');
        $password = I('post.new_pwd');
        $passwordr = I('post.rep_pwd');
        $two_password = I('post.new_pwdt');
        $two_passwordr = I('post.rep_pwdt');
        if (empty($password_old)) {
            ajaxReturn('请输入登录密码');
            return;
        }
        if ($password != $passwordr) {
            ajaxReturn('两次输入登录密码不一致');
            return;
        }

        if ($two_password != $two_passwordr) {
            ajaxReturn('两次输入交易密码不一致');
        }

        $user = D('User');
        $user->startTrans();
        //验证旧密码
        if (!$user->check_pwd_one($password_old)) {
            ajaxReturn('旧登录密码错误');
        }

        //=============登录密码加密==============
        if ($password) {
            $salt = substr(md5(time()), 0, 3);
            $data['login_salt'] = $salt;
            $data['login_pwd'] = md5(md5(trim($password)) . $salt);
        }

        //=============安全密码加密==============
        if ($two_password) {
            $two_salt = substr(md5(time()), 0, 3);
            $data['safety_salt'] = $two_salt;
            $data['safety_pwd'] = $two_password = md5(md5(trim($two_passwordr)) . $two_salt);
        }
        if (empty($data)) {
            ajaxReturn("请输入要修改的密码");
        }
        $user_id = $this->user_id;
        $where['userid'] = $user_id;
        $res = $user->where($where)->save($data);

        if ($res) {
            $user->commit();
            ajaxReturn("修改成功", 1);
        } else {
            $user->rollback();
            ajaxReturn("修改失败");
        }

    }


    //关于我们
    public function Aboutus()
    {
        $this->display();
    }

    //退出登录
    public function Loginout()
    {
        session_destroy();
        $this->redirect('Login/login');
    }

    /**
     * 返款信息
     *
     */
    public function bank()
    {
        $uid = $this->user_id;
        $uinfo = M('user')->where(array('userid' => $uid))->find();
        $bank = M('admin_bank')->where(array('admin_id' => $uinfo['add_admin_id']))->select();
        $this->assign('bank', $bank);
        $this->display();
    }


    /**
     * 用户可用抢单的二维码列表
     */
    public function qdQrcodes()
    {
        $gemaModel = new  GemapayCodeModel();
        $qrcodes = $gemaModel->getUserCode($this->user_id);
        //允许多少个二维码参与抢单
        $configQdQrcodeNum = C('qdQrcodeNum');
        $this->assign('configQdQrcodeNum', $configQdQrcodeNum);
        $this->assign('qrcodes', $qrcodes);
        $this->display();
    }

    public function orders()
    {
        $where['o.status'] = ['neq', '1,2'];
        $where['o.add_time'] = ['gt', time() - 3 * 60];
        $where['o.gema_userid'] = $this->user_id;
        $data = M('gemapay_order')->alias('o')->field('o.*')
            ->order(['add_time' => 'desc'])
            ->where($where)
            ->limit(0, 10)
            ->select();
        foreach ($data as $key => $d) {
            $data[$key]['date'] = date("H:i", $d['add_time']);
        }
        echo json_encode($data);

    }

    /**
     * 得到用户最新的一条订单
     */
    public function getLastorderId()
    {
        $where['o.gema_userid'] = $this->user_id;
        $lastOrderId = M('gemapay_order')->alias('o')->where($where)->order(['id' => 'desc'])->getField('id');
        echo json_encode(['last_order_id' => $lastOrderId]);
    }

}