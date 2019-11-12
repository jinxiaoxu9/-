<?php

namespace app\index\controller;


use app\common\library\enum\CodeEnum;


/**
 * 系统工具类控制器-----暂时先这样吧
 * Class SysFunController
 * @package app\index\controller
 */
class SysToolController extends CommonController
{


    /**
     * 发送短信
     */
    public function sendSms()
    {

    }

    /**
     * 发送邮件
     */
    public function sendEmai()
    {

    }


    /**
     * 文件上传
     */
    public function upload()
    {
        if($this->request->isPost()) {
            try{
                $params = $this->request->post('');
                //基本参数校验
                $checkParams = $this->validParams($params, ['path']);
                if($checkParams['status'] != CodeEnum::SUCCESS)
                {
                    ajaxReturn($checkParams['message'], $checkParams['status']);
                }

                $file = $this->request->file('file');
                if (empty($file))
                {
                    ajaxReturn('No file upload or server upload limit exceeded',CodeEnum::ERROR);
                }
                $uploadRet = $this->logicSysTool->upload($file,$params['path']);
                if($uploadRet['status']!=CodeEnum::SUCCESS)
                {
                    ajaxReturn($uploadRet['message'], $checkParams['status']);
                }
                $fileName = 'uploads'.DS.$params['path'].DS.$uploadRet['result']['file_name'];
                //上传成功
                if(trim($params['path']) == 'qrcodes')
                {
                    $filePath = config('uploads.root_path').'qrcodes'.DS.$uploadRet['result']['file_name'];
                    $rowImage = getRawQrImage($filePath);
                    $fileName  = $rowImage['path'];
                    @unlink($filePath);
                }
                ajaxReturn('上传成功',CodeEnum::SUCCESS,'',['file_name'=>$fileName]);
            }catch(\Exception $e){
                ajaxReturn($e->getMessage(),CodeEnum::ERROR);
            }
        }
    }


    /****************************************end******************************************/


}