<?php

class FileUpload  extends CComponent {

    public $rootPath;
    
    public $downUrlPre;
            
    function init() 
    {
        
    }
    
    
    /**
     * 生成下载全路径
     * @param type $filePath
     * @return type
     */
    public function getDownUrl($filePath)
    {
        if (empty($filePath)) {
            return NULL;
        }
        return $this->downUrlPre . '/' . $filePath;
    }
    
    
    /**
     * 获取保存的下载路径（不包含root目录）
     * @param type $extensionName
     * @return type
     */
    function getFilePath($extensionName) 
    {
        $time = time();
        $dir = date("Ymd", $time);
        if (!file_exists($this->rootPath . DIRECTORY_SEPARATOR . $dir)) {
            mkdir($this->rootPath . DIRECTORY_SEPARATOR . $dir, 0777, true);
        }
        $fileName = date("YmdHis", $time) . StrTool::getRandNumStr(4) . '.' .$extensionName;
        return $dir . '/' . $fileName;
    }
    
    
    /**
     * 保存文件
     * @param type $file
     * @return type
     * @throws ResError
     */
    public function upAdminFile($file) 
    {
        $uploadPath = $this->getFilePath($file->extensionName);
        $savePath = $this->rootPath . DIRECTORY_SEPARATOR . $uploadPath;
        
        if (!$file->saveAs($savePath, true)) {
            throw new ResError(Error::FILE_OPERATION_EXCEPTION, "文件操作异常");
        }
        
        if (!Yii::app()->aliOss->upload($uploadPath)) {
            throw new ResError(Error::OSS_OPERATION_EXCEPTION, "OSS操作失败");
        }
        return $uploadPath;
    }
    
    
    /**
     * 保存文件
     * @param type $file
     * @return type
     * @throws ResError
     */
    public function upFile($file) 
    {
        $uploadPath = $this->getFilePath($file->extensionName);
        $savePath = $this->rootPath . DIRECTORY_SEPARATOR . $uploadPath;
        
        if (!$file->saveAs($savePath, true)) {
            throw new ResError(Error::FILE_OPERATION_EXCEPTION, "文件操作异常");
        }
        
        if (!Yii::app()->aliOss->upload($uploadPath)) {
            throw new ResError(Error::OSS_OPERATION_EXCEPTION, "OSS操作失败");
        }
        return $uploadPath;
    }
    
}

?>
