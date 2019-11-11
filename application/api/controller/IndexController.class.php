<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-11
 * Time: 上午10:24
 */
namespace Gemapay\Controller;

use app\common\model\OwnpayOrder;
use Gemapay\Model\GemapayOrderModel;
use think\Controller;
use think\Db;
use think\Log;

class IndexController extends Controller
{
    //展示所有的订单
    public function lists()
    {
        $GemaPayOrder = new GemapayOrderModel();
        $datas  = $GemaPayOrder->getList();
        $datasPage = $GemaPayOrder->getListPage();
        $this->assign('datas',$datas);
        $this->assign('datasPage',$datasPage);
        $pay_url = "http://".$_SERVER['SERVER_NAME']."/gemapay/index/pay?orderid=";
        $this->assign('datas',$datas);
        $this->assign('pay_url',$pay_url);
        return $this->display();
    }

    //用户添加二维码
    public  function add()
    {
        return $this->display();
    }

    public  function getstatus()
    {
        $orderId= intval($_POST['id']);
        $GemaPayOrder = new \app\gemapay\model\GemapayOrder();
        $result = $GemaPayOrder->getOrderInfo($orderId);
        $array['status'] =$result['status'];
        echo json_encode($array);die();
    }

    public  function addDo()
    {
        //判断订单号是否已经存在
        $orderNum = $_POST['orderNum'];

        if(empty( $orderNum)){
            $this->error('订单号码不能为空','/ownpay/add',3);
        }
        $where['orderNum']= $orderNum;
        $result =  Db::table('cm_ownpay_order')
            ->where($where)->find();
        if( $result['status']!=0){
            $this->error('该订单二维码已经上传完成','/ownpay/add',3);
        }
        if(empty($result)){
            $this->error('订单还未生成请稍后重试，或者请检测订单号码是否正确','/ownpay/add',3);
        }

        if (( ($_FILES["file"]["type"] == "image/jpeg")
                || ($_FILES["file"]["type"] == "image/pjpeg")
                || ($_FILES["file"]["type"] == "image/png"))
            && ($_FILES["file"]["size"] < 2000000))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                $this->error("Return Code: " . $_FILES["file"]["error"],3);
            }
            else
            {
                if($_FILES["file"]["type"] == "image/jpeg"){
                    $ext = '.jpg';
                }
                if($_FILES["file"]["type"] == "image/pjpeg"){
                    $ext = '.jpg';
                }
                if($_FILES["file"]["type"] == "image/png"){
                    $ext = '.png';
                }
                $name = md5(microtime()).$ext;
                if (file_exists("uploads/" . $name))
                {
                    echo $_FILES["file"]["name"] . " already exists. ";
                }
                else
                {
                    move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/" .$name);
                  //  $this->success("Stored in: " . "upload/" . $_FILES["file"]["name"],'/ownpay/add',3);
                }
            }
        }
        else
        {
            $this->error('非法图片，请选择二维码图片','/ownpay/add',3);
        }
        $qr_image = $name;
        Db::table('cm_ownpay_order')->where('orderNum', $orderNum)->update(['qr_image' => $qr_image,'status'=>1]);
        $this->success('操作完成','/ownpay/add',3);
    }

    /**
     * 去支付
     * @return mixed
     */
    public  function pay()
    {
        //需要一个key
        $orderId = intval($_GET['id']);
        $GemaPayOrder = new \app\gemapay\model\GemapayOrder();
        $result = $GemaPayOrder->getOrderInfo($orderId);
        if(empty($result))
        {
            echo "error";die();
        }

        $qr_img = "/public/uploads/".$result['qr_image'];
        $id = $result['id'];
        $posturl = "http://".$_SERVER['SERVER_NAME']."/gemapay/index/getStatus";
        $return_url = "http://".$_SERVER['SERVER_NAME']."/gemapay/index/lists";
        $really_pirce = $result['order_pay_price'];
        $this->assign('qr_img', $qr_img);
        $this->assign('really_pirce', $really_pirce);
        $this->assign('id', $id);
        $this->assign('posturl', $posturl);
        $this->assign('return_url', $return_url);

        return $this->fetch();
    }

}