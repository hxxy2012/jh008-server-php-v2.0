<?php

class BinaryTool {

    /**
     * 验证位移量是否合法
     * 
     * @param type $pos 位移量
     */
    public static function validPos($pos)
    {
        if ($pos > 64) {
            return FALSE;
        }
        return TRUE;
    }
    

    /**
     * 设置第几位为1
     * 
     * @param type $src 被处理的
     * @param type $pos 位移量
     */
    public static function setOne($src, $pos)
    {
        return $src | (1 << $pos - 1);
    }
    
    
    /**
     * 设置第几位为0
     * 
     * @param type $src 被处理的
     * @param type $pos 位移量
     */
    public static function setZero($src, $pos)
    {
        return $src & (PHP_INT_MAX ^ (1 << ($pos - 1)));
    }
    
}

?>
