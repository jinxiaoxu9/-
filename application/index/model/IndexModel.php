<?php

namespace app\index\model;

use Common\Model\ModelModel;
use think\Model;

/**
 * 用户模型
 *
 */
class IndexModel extends Model
{
	protected $tableName = 'user';
    public function Getlasts($userid, $y, $lujing)
    {
        $where['userid'] = $userid;
        $path = M('user')->where($where)->getField($lujing);

        $newstr = ltrim($path, '0');
        $newstr = trim($newstr, '-');
        $arr = explode('-', $newstr);

        if (!empty($arr)) {
            rsort($arr);
            $mun = count($arr);
            $newarr = array();
            if ($mun > $y) {
                for ($x = 0; $x < $y; $x++) {
                    $newarr[] = $arr[$x];
                }
            } else {
                $newarr = $arr;
            }
            return $newarr;
        }
    }
    //判断用户等级
    public function Checklevel($memberid){
        $U_money = M('store')->where(array('uid'=>$memberid))->getField('fengmi_num');
        $old_level = M('user')->where(array('userid'=>$memberid))->getField('use_grade');
        $new_level = 0;
        if($U_money<1000){
            $new_level = 0;
        }else if($U_money >= 1000 && $U_money<50000){
            $new_level = 1;
        }else if($U_money >= 50000 && $U_money<1000000){
            $new_level = 2;
        }else if($U_money >= 1000000){
            $new_level = 3;
        }

        if($new_level>$old_level){
            M('user')->where(array('userid'=>$memberid))->setField('use_grade',$new_level);
        }
        

    }
}
