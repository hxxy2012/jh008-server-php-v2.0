<?php

class QrCodeTool {
    
    public function init()
    {
        
    }


    /**
     * 生成分享二维码的json字符串，数据格式为：{'filter':'act_id','value':66}
     * @param type $key
     * @param type $value
     */
    public function makeQrJson($key, $value)
    {
        $qrJson = array();
        $qrJson['filter'] = $key;
        $qrJson['value'] = $value;
        return json_encode($qrJson);
    }
    
}

?>
