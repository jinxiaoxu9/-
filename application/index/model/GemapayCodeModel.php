<?php


namespace app\index\model;

use Think\Model;

class GemapayCodeModel extends Model
{

       public $errormsg;

       public function getUserCode($userId=0,$status=null){
           $userId && $where['user_id']=$userId;
           (!is_null($status)) && $where['status']=$status;
           $gemaCode = $this->alias('a')->field('a.*,c.type_name')
               ->join("ysk_gemapay_code_type c on a.type=c.id", "left")
               ->where($where)
               ->order("create_time desc")
               ->select();
           return $gemaCode;
       }


       public function  getInfo($where)
       {
           $ret = $this->where($where)->find();
           if(empty($ret)){
               $this->errormsg ="二维码不存在";
               return false;
           }

           if($ret['status'] == 1){
               $this->errormsg ="二维码被禁用";
               return false;
           }
           return $ret;
       }

}