<?php

class ImgUpload  extends CComponent {

    public $rootPath;
    
    public $downUrlPre;
            
    function init() 
    {
        
    }
    
    
    /**
     * 生成下载全路径
     * @param type $imgPath
     * @return type
     */
    public function getDownUrl($imgPath)
    {
        if (empty($imgPath)) {
            return NULL;
        }
        return $this->downUrlPre . '/' . $imgPath;
    }
    
    
    /**
     * 获取保存的下载路径（不包含root目录）
     * @param type $extensionName
     * @return type
     */
    function getImgPath($extensionName) 
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
     * 保存头像文件
     * @param type $file
     * @return type
     * @throws CResException
     */
    public function uHeadImg($file) 
    {
        //if (!is_object($file) || get_class($uploadedGoodsImage) !== 'CUploadedFile')
        //    return FALSE;
        //$fileName = $file->name;
        //$fileSize = $file->size;
        //$fileType = $file->type;
        //fileExt = $file->extensionName;
        //数据库中要存放文件名
        //$model->filename = $filename;
        //这里是处理中文的问题，非中文不需要
        //$filename1 = iconv('utf-8', 'gb2312', $filename);
        
        $uploadPath = $this->getImgPath($file->extensionName);
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
     * 保存用户上传的图片文件
     * @param type $file
     * @return type
     * @throws CResException
     */
    public function uImg($file) 
    {
        $uploadPath = $this->getImgPath($file->extensionName);
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
     * 保存活动图片文件
     * @param type $file
     * @return type
     * @throws CResException
     */
    public function uActImg($file) 
    {
        $uploadPath = $this->getImgPath($file->extensionName);
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
     * 保存商家图片文件
     * @param type $file
     * @return type
     * @throws CResException
     */
    public function uBusinessImg($file) 
    {
        $uploadPath = $this->getImgPath($file->extensionName);
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
     * 保存管理员图片文件
     * @param type $file
     * @return type
     * @throws CResException
     */
    public function uManagerImg($file) 
    {
        $uploadPath = $this->getImgPath($file->extensionName);
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
     * 百度编辑器图片上传
     * @param type $tmpName
     * @param type $extName
     * @return boolean
     */
    public function uBaiduEditorImg($tmpName, $extName) 
    {
        $uploadPath = $this->getImgPath($extName);
        $savePath = $this->rootPath . DIRECTORY_SEPARATOR . $uploadPath;
        if (!move_uploaded_file($tmpName , $savePath)) {
            return FALSE;
        }
        if (!Yii::app()->aliOss->upload($uploadPath)) {
            return FALSE;
        }
        //$fileName;
        $fullName = $uploadPath;
        return array(
            'tmpName' => $tmpName,
            //'fileName' => $fileName,
            'fullName' => $fullName
        );
    }
    
}

?>
