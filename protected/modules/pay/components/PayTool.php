<?php

class PayTool extends CComponent
{

    public function init() 
    {
        
    }
    
    
    /**
     * 得到新唯一序列号
     */
    function buildUniqueNo()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    
}

?>
