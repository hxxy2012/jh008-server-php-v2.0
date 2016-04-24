<?php

class OpenidTool extends CComponent {
    
    public $sinaAppkey;
    
    public $qqClientId;
    
    function init() {
        
    }
    
    
    /**
     * 验证sina的token是否有效
     * @param type $openid
     * @param type $token
     * @return boolean
     */
    public function sinaTokenValid($openid, $token) {
        //App Key:2247106580 App Secret:a1d70870e62c56c698e900b7174e49e0 openid:2371851464 token:2.00muCWaCWEdE9Ce5425f629fQ5YSeB 7776000
        $postData = array('access_token' => $token);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weibo.com/oauth2/get_token_info");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result_str = curl_exec($ch);
        curl_close($ch);
        //{"uid":2371851464,"appkey":"2247106580","scope":null,"create_at":1410418355,"expire_in":127049} 
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['appkey']) && isset($rst['uid']) && $this->sinaAppkey == $rst['appkey'] && $openid == $rst['uid']) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    /**
     * 验证qq的token是否有效
     * @param type $openid
     * @param type $token
     */
    public function qqTokenValid($openid, $token) {
        //appid:1102364598 openid:2395821C32127FC094C9EB52EF417205 token:434C7B9FAA53CC3C4E5DF828E301F56C 7776000
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.qq.com/oauth2.0/me?access_token={$token}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result_str = curl_exec($ch);
        curl_close($ch);
        //callback( {"client_id":"1102364598","openid":"2395821C32127FC094C9EB52EF417205"} ); 
        preg_match('/callback\(\s+(.*?)\s+\)/i', $result_str, $result_arr);
        $rst = json_decode($result_arr[1], true);
        if (isset($rst['client_id']) && isset($rst['openid']) && $this->qqClientId==$rst['client_id'] && $openid==$rst['openid']) {
            return TRUE;
        }
        return FALSE;
    }
    
    
    /**
     * 验证wechat的token是否有效
     * @param type $openid
     * @param type $token
     */
    public function wechatTokenValid($openid, $token) {
        $ch = curl_init();
        //https://api.weixin.qq.com/sns/auth?access_token=ACCESS_TOKEN&openid=OPENID
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/sns/auth?access_token={$token}&openid={$openid}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result_str = curl_exec($ch);
        //print_r($result_str);
        curl_close($ch);
        //{ 
        //    "errcode":0,"errmsg":"ok"
        //}
        $rst = json_decode($result_str, TRUE);
        if (isset($rst['errcode']) && isset($rst['errmsg']) && 0 == $rst['errcode'] && 'ok' == $rst['errmsg']) {
            return TRUE;
        }
        return FALSE;
    }
    
}

?>
