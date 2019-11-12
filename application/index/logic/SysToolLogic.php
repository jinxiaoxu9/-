<?php

namespace app\index\logic;

use app\common\library\enum\CodeEnum;
use think\Db;
//use app\index\model\ConfigModel;
use app\index\model\User;

class SysToolLogic extends BaseLogic
{

    /**
     * 上传逻辑
     */
    public function upload($file, $savaPath = '')
    {
        // 移动到框架应用根目录/public/uploads/ 目录下
        $uploadConfig = config('uploads');
        $info = $file->rule('uniqid')->validate(['size' => $uploadConfig['size'], 'ext' => $uploadConfig['ext']])->move($uploadConfig['root_path'] . $savaPath);
        if ($info) {
            return ['status' => CodeEnum::SUCCESS, 'message' => 'success','result'=>['file_name'=>$info->getSaveName()]];
        }
        return ['status' => CodeEnum::ERROR, 'message' => $file->getError()];
    }





}