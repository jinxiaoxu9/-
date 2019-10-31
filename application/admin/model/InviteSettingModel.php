<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <bbs.sasadown.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use think\Model;

/**
 * 部门模型
 * @author jry <bbs.sasadown.cn>
 */
class InviteSettingModel extends Model
{
    /**
     * 数据库表名
     * @author jry <bbs.sasadown.cn>
     */
    protected $tableName = 'user_invite_setting';


    public function getInviteDesc($setting, $codeList)
    {
        $desc = "";
        $s = json_decode($setting, true);
        if(empty($s))
        {
            return $desc;
        }
        $codeList = filterDataMap($codeList, "id");
        $arrString = [];
        foreach ($s as $key=>$setting)
        {

            if(!empty($setting))
            {
                $arrString[] = $codeList[$key]['type_name']."费率:".$setting/1000;
            }
        }
        $desc = implode($arrString,",");
        return $desc;
    }

    public function getInviteLink($code)
    {
       return "http://".$_SERVER['HTTP_HOST']."/Login-register.html?code=".$code;
    }
}
