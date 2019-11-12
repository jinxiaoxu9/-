<?php
namespace app\index\controller;
use app\common\library\enum\CodeEnum;
use app\index\model\UserModel;
use think\Controller;

class CommonController extends Controller {
    protected  $user_id;
    protected  $request;

	public function __construct(){
        //验证用户登录
        parent::__construct();
        $this->request = request();
        $this->throughVisites();
        if(!in_array($this->request->action(),$this->throughVisites())){
            $this->is_user();
        }
    }


    /**
     * 开放不需要判读登录的地址访问
     */
    protected function  throughVisites(){
        return  [
             'register','login'
         ];
    }



    protected function is_user(){
        $UserModel =  new UserModel();
        $token = $this->request->post("token",'','trim');
        if(empty($token)){
            ajaxReturn('参数错误[token]',CodeEnum::FORBId);
        }
        $where['token'] = $token;
        $where['status'] =1;
        $userInfo = $UserModel->where($where)->find();

        if(!$userInfo)
        {
            ajaxReturn('请先登录,您被迫下线',0);
        }
        $this->user_id = $userInfo->userid;
    }





    /**
     * 获取逻辑层实例  --魔术方法
     * @param $logicName
     * @return \think\Model|\think\Validate
     */
    public function __get($logicName)
    {
        $layer = $this->getLayerPre($logicName);

        $model = sr($logicName, $layer);

        return VALIDATE_LAYER_NAME == $layer ? validate($model) : model($model, $layer);
    }



    /**
     * 获取层前缀
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $name
     * @return bool|mixed
     */
    public function getLayerPre($name)
    {

        $layer = false;

        $layer_array = [MODEL_LAYER_NAME, LOGIC_LAYER_NAME, VALIDATE_LAYER_NAME, SERVICE_LAYER_NAME];
        foreach ($layer_array as $v)
        {
            if (str_prefix($name, $v)) {

                $layer = $v;

                break;
            }
        }

        return $layer;
    }


    /**
     * 验证接口请求参数
     * 后续也可以切换为责任链模式不引入validate类
     * @param array $param
     *
     */
    protected function  validParams($params=[],$checkParams=[]){
        //必要参数判断
        foreach($checkParams as $k=>$v){
              if(!isset($params[$v])){
                  return  [
                      'message'=>"缺少必要参数[{$v}]",
                      'status'=>CodeEnum::LOST_PARAM,
                  ];
              }
        }
        //参数值判断
        foreach ($params as $k=>$v) {
                if ( in_array($k,$checkParams) && (!isset($params[$k]) || empty($params[$k]))) {
                    return  [
                        'message'=>"参数值不合法[{$k}]",
                        'status'=>CodeEnum::EMPTY_PARAM,
                    ];
                }
        }
        return ['message' => 'check success' ,'status' =>CodeEnum::SUCCESS];
    }

}

