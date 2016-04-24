<?php

class Response extends CComponent {

    function init() 
    {
        
    }
    
    
    /**
     * 输出接口数据
     * @param type $code
     * @param type $msg
     * @param type $body
     */
    public function output($code, $msg, $body = array())
    {
        $result = array(
            'code' => $code,
            'msg' => Error::NONE == $code ? "SUCCESS : '" . $msg . "'" : "Error reference : '" . $this->getErrorName($code) . " " . $msg . "'",
            'body' => $body,
        );
        print json_encode($result);
    }

    
    /**
     * 根据value获取Error的常量名
     * @param type $value
     */
    public function getErrorName($value)
    {
        $r = new ReflectionClass("Error");
        return array_search($value, $r->getConstants(), TRUE);
    }
    
}

?>
