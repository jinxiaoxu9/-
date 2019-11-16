<?php

namespace app\admin\logic;


class InviteSettingLogic
{
    public function getInviteDesc($setting, $codeList)
    {
        $desc = "";
        $s = json_decode($setting, true);
        if(empty($s))
        {
            return $desc;
        }
        //$codeList = $this->filterDataMap($codeList, "id");
        $arrString = [];
        foreach ($s as $key=>$setting)
        {
            if(!empty($setting) && isset($codeList[$key]['type_name'])) {
                $arrString[] = $codeList[$key]['type_name'] . "费率:" . $setting/1000;
            }
        }
        $desc = implode($arrString,",");
        return $desc;
    }

    public function getInviteLink($code)
    {
        return "http://".$_SERVER['HTTP_HOST']."/login/register.html?code=".$code;
    }

}