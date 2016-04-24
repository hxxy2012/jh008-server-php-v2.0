<?php

class LoadClass {
    
    static function autoLoadJPush($classname) 
    { 
        if (substr($classname, 0, 5) == 'JPush') {
            $base = Yii::app()->modulePath . DIRECTORY_SEPARATOR . 'util' . DIRECTORY_SEPARATOR . 'extensions';
            $path = str_replace("\\", DIRECTORY_SEPARATOR, $classname);
            $absolute_path = $base . DIRECTORY_SEPARATOR . $path . '.php';
            //echo $absolute_path;
            if (file_exists($absolute_path)) {
                require_once $absolute_path;
            } else {
                die("$classname not found");
            }
        }
    }
    
    
    static function autoLoadHttpful($classname) 
    { 
        if (substr($classname, 0, 7) == 'Httpful') {
            $base = Yii::app()->modulePath . DIRECTORY_SEPARATOR . 'util' . DIRECTORY_SEPARATOR . 'extensions';
            $path = str_replace("\\", DIRECTORY_SEPARATOR, $classname);
            $absolute_path = $base . DIRECTORY_SEPARATOR . $path . '.php';
            //echo $absolute_path;
            if (file_exists($absolute_path)) {
                require_once $absolute_path;
            } else {
                die("$classname not found");
            }
        }
    }
    
    /** 
    * 设置对象的自动载入 
    * spl_autoload_register — Register given function as __autoload() implementation 
    */ 
    //spl_autoload_register(array('LOAD', 'loadClass')); 
    //$a = new Test();//实现自动加载，很多框架就用这种方法自动加载类
}

?>