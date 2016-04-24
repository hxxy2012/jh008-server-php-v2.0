<?php

class WebPageTool  extends CComponent {

    public $viewUrlPre;
            
    function init() 
    {
        
    }
    
    
    /**
     * 生成网页查看全路径
     * @param type $uri
     * @param array $parms
     * @return string
     */
    public function getViewUrl($uri, $parms)
    {
        $url = $this->viewUrlPre;
        if (!empty($uri)) {
            $url .= '/' . $uri;
        }
        if (!empty($parms)) {
            $url .= '?';
            foreach ($parms as $k => $v) {
                if (0 != $k) {
                    $url .= '&';    
                }
                $url .= $k . '=' . $v;
            }
        }
        return $url;
    }
    
}

?>
