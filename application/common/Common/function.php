<?php

function p($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function isAjax()
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
            return true;
    }
    if (!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]))
        // 判断Ajax方式提交
        return true;
    return false;

}

//检查网站是否关闭
function is_close_site(){
    
    $where['name']='TOGGLE_WEB_SITE';
    $info=M('Config','ysk_')->where($where)->field('value,tip')->find();
    return $info;    
}
//检查商城是否关闭
function is_close_mall(){
    
    $where['name']='TOGGLE_MALL_SITE';
    $info=M('Config','ysk_')->where($where)->field('value,tip')->find();
    return $info;    
}


/**
 * curl  模拟请求
 * @param $url
 * @param string $method
 * @param null $postfields
 * @param array $headers
 * @param bool $debug
 * @return bool|string
 */
function httpRequest($url, $method = "GET", $postfields = null, $headers = array(), $debug = false)
{
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i', $url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if ($ssl) {
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);

    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}
function sp_dir_create($path, $mode = 0777)
{
    if (is_dir($path)) return true;
    $ftp_enable = 0;
    $path = sp_dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}

function sp_dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

/**
 * [get_car_no 生成矿车编号]
 * @return [type] [description]
 */
function get_car_no(){
    $no=M('nzusfarm')->max('no');
    $no=intval($no);
    $no=$no+1;
    $len=strlen($no);
    if($len<6){
        $arr[1]='00000';
        $arr[2]='0000';
        $arr[3]='000';
        $arr[4]='00';
        $arr[5]='0';
        $no=$arr[$len].$no;
    }
    return $no;
}

/** 
*添加公共上传方法
*获取上传路径
*$picture
*/
function get_cover_url($picture){
    if($picture){
        $url = __ROOT__.'/Uploads/'.$picture;
    }else{
        $url = __ROOT__.'/Uploads/photo.jpg';
    }
    return $url;
}

/**
 * 用于生成插入datetime类型字段用的字符串
 * @param string $str 支持偏移字符串
 */
function datetime($str = 'now')
{
    return @date("Y-m-d H:i:s", strtotime($str));
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author jry <bbs.sasadown.cn>
 */
function time_format($time = null, $format = 'Y-m-d H:i')
{
    $time = $time === null ? time() : intval($time);
    return date($format, $time);
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 * @author jry <bbs.sasadown.cn>
 */
function user_md5($str, $auth_key)
{
    if (!$auth_key) {
        $auth_key = config('AUTH_KEY') ?: '0755web';
    }
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * [user_salt 用户密码加密链接串]
 * @return [type] [description]
 */
function user_salt($time=null){
    if(isset($time) || empty($time)){
        $time=time();
    }
   return substr(md5($time),0,3);
}

/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 * @author jry <bbs.sasadown.cn>
 */
function get_cover($id = null, $type = null)
{
    return D('Admin/Upload')->getCover($id, $type);
}



/**
 * 检测是否使用手机访问
 * @access public
 * @return bool
 */
function is_wap()
{
    if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * 是否微信访问
 * @return bool
 * @author jry <bbs.sasadown.cn>
 */
function is_weixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * [get_verify 生成验证码]
 * @return [type] [description]
 */
function get_verify(){
    ob_clean();
    $config =    array(
    'codeSet' =>  '0123456789',   
    'fontSize'    =>    50,    // 验证码字体大小   
    'length'      =>    4,     // 验证码位数    
    'fontttf'     =>   '5.ttf',
    'useCurve'    => false,
    'bg'          => array(229, 237, 240),
    );
    $Verify =     new \Think\Verify($config);
    $Verify->entry();
}


/**
 * [ajaxReturn ajax提示款]
 * @param  [type]  $message [提示文字]
 * @param  integer $status  [1=成功 0=失败]
 * @param  string  $url     [跳转地址]
 * @param  string  $extra   [回传数据]
 * @return [type]           [json数据]
 */
function ajaxReturn($message,$status=0, $url ='',$extra='') {
    // 返回JSON数据格式到客户端 包含状态信息
    header('Content-Type:application/json; charset=utf-8');
    $result = array(
        'message' => $message,
        'status'  =>  $status,
        'url' => $url,
        'result'  =>  $extra
    );
    
    exit(json_encode($result));
}

// =陶==js消息提示框===
function error_alert($mes){
    echo "<meta charset=\"utf-8\"/><script>alert('".$mes."');javascript:history.back(-1);</script>";
    exit;
}
function success_alert($mes,$url=''){
    if($url!=''){
        echo "<meta charset=\"utf-8\"/><script>alert('".$mes."');location.href='" .$url. "';</script>";
    }else{
       echo "<meta charset=\"utf-8\"/><script>alert('".$mes."');location.href='" .$jumpUrl. "';</script>"; 
    }
    exit;
}
// =陶==js消息提示框===



//防注入，字符串处理，禁止构造数组提交
//字符过滤
//陶
function safe_replace($string) {
    if(is_array($string)){ 
       $string=implode('，',$string);
       $string=htmlspecialchars(str_shuffle($string));
    } else{
        $string=htmlspecialchars($string);
    }
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string=str_replace("select","",$string);
    $string=str_replace("join","",$string);
    $string=str_replace("union","",$string);
    $string=str_replace("where","",$string);
    $string=str_replace("insert","",$string);
    $string=str_replace("delete","",$string);
    $string=str_replace("update","",$string);
    $string=str_replace("like","",$string);
    $string=str_replace("drop","",$string);
    $string=str_replace("create","",$string);
    $string=str_replace("modify","",$string);
    $string=str_replace("rename","",$string);
    $string=str_replace("alter","",$string);
    $string=str_replace("cas","",$string);
    $string=str_replace("or","",$string);
    $string=str_replace("=","",$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('--','',$string);
    $string = str_replace('(','',$string);
    $string = str_replace(')','',$string);

    return $string;
}

function payway($value){
    $arr=array('支付宝','微信');
    return $arr[$value];
}

/**
 * 获取父级账号
 */
function get_parent_account($pid){
    $account=D('User')->where('userid ='.$pid)->getField('account');
    if($account)
        return $account;
    else
        return '无';
}

function get_user_name($uid){
    $where['userid']=$uid;
    $info=M('user')->where($where)->field('account,username')->find();
    return $info['username']."(".$info['account'].")";
}


//验证码
 function set_verify(){
        ob_clean();
        $config =    array(
        'codeSet' =>  '0123456789',   
        'fontSize'    =>    30,    // 验证码字体大小   
        'length'      =>    4,     // 验证码位数    
        'fontttf'     =>   '5.ttf',
        'useCurve'    => false,
        'expire'    =>  1800,//过期时间
        );
        $Verify =     new \Think\Verify($config);
        $Verify->entry();
    }

//验证验证码
function check_verify($code)
{
    $verify = new \Think\Verify();
    return $verify->check($code);
}

//获取以及好友人数
function get_children_count($uid){
    $where['pid']=$uid;
    return M('user')->where($where)->count(1);
}
/**
 * 打印函数
 * @param $data  打印数据
 */
function dd($data){

    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
}


/**
 * [trading 添加用户交易记录明细]
 * @param  [type] $data [添加的数据]
 * @return [type]         [description]
 */
function add_trading($data){
    if(empty($data))
        return false;

    $trading=M('fruitdetail');
    
    if(empty($data['uid'])){
      $userid=session('userid');
      $data['uid']=$userid;
    }
    $data['add_time']=time();
    $res=$trading->add($data);
    return $res;
}
/**
 * 生成支付订单号
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 * @return string
 */
function create_order_no()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn =
        $yCode[intval(date('Y')) - 2018] . date('YmdHis') . strtoupper(dechex(date('m')))
        . date('d') . sprintf('%02d', rand(0, 999));
    return $orderSn;
}


/**
 * @param $uid  uwer_id
 * @param int $type   资金操作类型对应数据库中jl_class
 * @param int $add_subtract  添加或者减少
 * @param float $money    操作金额
 * @param string $tip_message    资金流水备注
 * @return bool
 */
function accountLog($uid, $type=1,$add_subtract = 1, $money=0.00, $tip_message = '')
{
    $userTable = M('user');
    $user=$userTable->where(['userid'=>$uid])->find();

    //转账身份检测
    if ($user) {  //当前用户状态正常
        $moneys = ($add_subtract == 1) ? $money : 0 - $money;
        $updateBalanceRes = $userTable->where(['userid' => $uid])->setInc('money', $moneys);
        if ($updateBalanceRes) {
            //记录流水
            $insert['uid'] = $uid;
            $insert['jl_class'] = $type;
            $insert['info'] = $tip_message;
            $insert['addtime']= time();
            $insert['jc_class']= ($add_subtract)?"+":"-";
            $insert['num']= $money;
            $insert['pre_amount']= $user['money'];
            $insert['last_amount']= $user['money']+$moneys;
            if (M('somebill')->add($insert)) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }
    return false;
}


//识别图片
function identify($imagePath)
{

  /*  $cmd = "/usr/local/imagemagick/bin/identify ".$imagePath;
    exec($cmd,$res);
    $imageInfo = explode(" ",$res[0])[2];
    $width = explode("x", $imageInfo)[0];
    $higth = explode("x", $imageInfo)[1];
    $scale = intval($higth*(7/20));
    $widthStart = intval($width*(1/6));
    $lengthStart = intval($higth*(1/5));

    if($width == 720 &&  $higth == 1280)
    {
        $res = getQrcodeFromImage($imagePath, $imagePath, 448, 510, 130);
        if(!empty($res))
        {
            return $res;
        }
    }

    if($width == 1080 &&  $higth == 1920)
    {
        $res = getQrcodeFromImage($imagePath, $imagePath, 680, 809, 199, 9, 1,1);
        if(!empty($res))
        {
            return $res;
        }
    }

    if($width == 1080 &&  $higth == 2280)
    {
        $res = getQrcodeFromImage($imagePath, $imagePath, 680, 989, 199, 9, 1,1);
        if(!empty($res))
        {
            return $res;
        }

        $res = getQrcodeFromImage($imagePath, $imagePath, 680, 800, 199, 9, 1,1);
        if(!empty($res))
        {
            return $res;
        }
    }

    $res = getQrcodeFromImage($imagePath, $imagePath, $scale, $lengthStart, $widthStart);
    if(empty($res))
    {
        $res = getQrcodeFromImage($imagePath, $imagePath, intval($higth*(6/20)), $lengthStart, $widthStart);
        if(empty($res))
        {
            $res = getQrcodeFromImage($imagePath, $imagePath, intval($higth*(8/20)), $lengthStart, $widthStart);
            if(empty($res))
            {
                $res = getQrcodeFromImage($imagePath, $imagePath, intval($higth*(3063/10000)), intval($higth*(3/10))+40,  intval($width*(22/100)), 0);
                if(empty($res))
                {
                    $res = getQrcodeFromImage($imagePath, $imagePath, intval($higth*(11/40)), intval($higth*(3/10)), $widthStart+10, 0);
                }
            }
        }

    }
    return $res;*/
}

//获取纯的ｕｒｌ图片和ｕｒｌ路径
function getRawQrImage($imgPath)
{
    $url = getQrcodeFromImage($imgPath);
    if(empty($url))
    {
        return false;
    }
 
    $imagePath = encodeFromUrl($url);
    $data["url"] = $url;
    $data["path"] = $imagePath;
    return $data;
}

//获取二维码图片
function getQrcodeFromImage($imagePath)
{

    ini_set('memory_limit','3072M');
    set_time_limit(0);
    $res = [];
    //读取二维码内容
    require "./vendor/autoload.php";
    $qrcode = new   \Zxing\QrReader($imagePath);
    $text = $qrcode->text();
    if(!empty($text))
    {
        return $text;
    }
    exec("zbarimg ".$imagePath, $res);
    if(empty($res[0]) || strpos($res[0], "QR-Code:") === false)
    {
        return false;
    }

    return str_replace("QR-Code:","", $res[0]);
}

//加密图片地址
function encodeFromUrl($url)
{
    $res = [];

    $qrDir = "Public/attached/gema_codes/";
    if(!file_exists($qrDir)){
        mkdirs($qrDir);
    }
    $outputiamge = $qrDir.md5(time().$url)."_qr.png";
    $cmd = "qrencode '".$url."' -o ".$outputiamge; //yum 源安装即可
    exec($cmd, $res);
    return $outputiamge;

}

/**
 * 获取数组的字典
 * @param array $data
 * @param string $key
 * @return array
 */
function filterDataMap($data,$key)
{
    if(empty($data))
    {
        return [];
    }
    $keys = explode('.', $key);
    $key = array_shift($keys);
    $newData = array();
    foreach($data as $val)
    {
        if(is_array($val))
        {
            if(empty($keys))
            {
                $newData[$val[$key]] = $val;
            }
            else
            {
                $newData[$val[$key]] = filterDataMap(filterData($data, $key, $val[$key]), join('.', $keys));
            }
        }
        else
        {
            if(empty($keys))
            {
                $newData[$val->$key] = $val;
            }
            else
            {
                $newData[$val->$key] = filterDataMap(filterData($data, $key, $val->$key), join('.', $keys));
            }
        }
    }

    return $newData;
}

/*随机生成邀请码*/

function strrand($length = 12, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    if(!is_int($length) || $length < 0) {
        return false;
    }
    $string = '';
    for($i = $length; $i > 0; $i--) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
    }
    return $string;
}

/**
 * [pwdMd5 用户密码加密]
 *
 */
function pwdMd5($value, $salt)
{
    $user_pwd = md5(md5($value) . $salt);
    return $user_pwd;
}