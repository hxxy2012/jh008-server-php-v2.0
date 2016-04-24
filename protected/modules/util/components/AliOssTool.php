<?php

class AliOssTool extends CComponent
{
    //参数配置
    public $accessId;
    public $accessKey;
    public $hostname;
    public $bucket;
    
    protected $ossSdkService;


    public function init() 
    {
        $this->ossSdkService = new AliOss($this->accessId, $this->accessKey, $this->hostname);

        //设置是否打开curl调试模式
        $this->ossSdkService->set_debug_mode(FALSE);

        //设置开启三级域名，三级域名需要注意，域名不支持一些特殊符号，所以在创建bucket的时候若想使用三级域名，最好不要使用特殊字符
        $this->ossSdkService->set_enable_domain_style(TRUE);
    }


    /**
     * 对应目录上传本地文件至阿里oss
     * @param type $filePath
     * @return boolean
     */
    public function upload($filePath)
    {
        try{
            if (!$this->createDir(dirname($filePath))) {
                return FALSE;
            }
            
            if (!$this->uploadByFile($filePath, Yii::app()->imgUpload->rootPath . '/' . $filePath)) {
                return FALSE;
            }
            return TRUE;
        }catch (Exception $ex){
            //die($ex->getMessage());
            die(json_encode(array('code' => Error::OSS_OPERATION_EXCEPTION,
                    'msg' => 'OSS操作异常 ' . $ex->getMessage(),
                    'body' => array(),
                    )
                )
            );
        }
    }
    

    //创建目录
    function createDir($dir)
    {
        $response  = $this->ossSdkService->create_object_dir($this->bucket,$dir);
        if (200 === $response->status) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    //通过路径上传文件
    function uploadByFile($object, $filePath)
    {
        //$object = 'netbeans-7.1.2-ml-cpp-linux.sh';	
        //$file_path = "D:\\TDDOWNLOAD\\netbeans-7.1.2-ml-cpp-linux.sh";
        
        $response = $this->ossSdkService->upload_file_by_file($this->bucket, $object, $filePath);
        if (200 === $response->status) {
            return TRUE;
        }
        return FALSE;
    }
    
}

?>
