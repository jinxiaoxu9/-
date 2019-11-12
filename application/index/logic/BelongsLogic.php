<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use think\Db;
//use app\index\model\ConfigModel;
use app\index\model\User;

class BelongsLogic extends BaseLogic
{


    //获取某个用户在每种二维码下面的佣金费率
    public function  getYjconfig($where){
        $setting= $this->modelUserInviteSetting->getInviteSetting($where,'*');
        $setting = json_decode($setting["invite_setting"]);
        $codeTypeLists = $this->modelGemapayCodeType->getCodeTypes();
        $codeTypeLists = filterDataMap($codeTypeLists, "id");
        $myCodeFee = [];
        if(is_array($setting)){
            foreach ($setting as $key => $s) {
                if (!empty($s)) {
                    $codeTypeLists[$key]["max"] = $s/10;
                    $myCodeFee[] = $codeTypeLists[$key];
                }
            }
        }
        return $codeTypeLists ;
    }



    /*****************************************************************************/



}