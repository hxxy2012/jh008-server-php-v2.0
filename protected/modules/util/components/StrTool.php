<?php

class StrTool {
    
    /**
     * 获取随机字符串
     * @param type $length
     * @return string
     */
    public static function getRandStr($length) 
    {  
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randString = '';
        $len = strlen($str)-1;
        for($i = 0;$i < $length;$i ++){
            $num = mt_rand(0, $len);
            $randString .= $str[$num];
        } 
        return $randString;
    }
    
    
    /**
     * 获取随机字符串
     * @param type $length
     * @return string
     */
    public static function getRandNumStr($length) 
    {  
        $str = '0123456789'; 
        $randString = '';
        $len = strlen($str)-1;
        for($i = 0;$i < $length;$i ++){
            $num = mt_rand(0, $len);
            $randString .= $str[$num];
        }
        return $randString;
    }
    
}

?>
