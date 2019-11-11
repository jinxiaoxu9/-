<?php

namespace app\admin\logic;


class UserGroupLogic
{
    /**
     * @param $array
     * @param int $pid
     * @param int $level
     * @return array
     */
    function getCategory($array, $pid =0, $level = 1){
        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];
        foreach ($array as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['parentid'] == $pid){
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getCategory($array, $value['id'], $level+1);
            }
        }
        return $list;
    }
}