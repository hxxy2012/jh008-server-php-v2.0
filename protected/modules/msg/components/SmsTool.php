<?php

class SmsTool extends CComponent {
    public $uid;
    
    public $keyMD5;
    
    function init() {
        //【集合啦】尊敬的用户您好，您本次的验证码为：{}。
    }
    
    
    /**
     * 发送通知短信
     * @param type $phoneNums
     * @param type $content
     */
    public function send($phoneNums, $content) {
        $phoneStrT = '';
        foreach ($phoneNums as $phoneNum){
            $phoneStrT .= $phoneNum . ',';
        }
        $phoneStr = trim($phoneStrT, ',');
        $url = "http://utf8.sms.webchinese.cn/?Uid={$this->uid}&KeyMD5={$this->keyMD5}&smsMob={$phoneStr}&smsText={$content}";
        if (count($phoneNums) == $this->getUrl($url)) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    function getUrl($url)
    {
        if(function_exists('file_get_contents')){
            $file_contents = file_get_contents($url);
        }else{
            $ch = curl_init();
            $timeout = 5;
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    } 

}

?>
