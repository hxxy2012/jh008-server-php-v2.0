<?php

class JPushTool {
    const PUSH_URL = 'https://api.jpush.cn/v3/push';
    const ALIAS_URL = 'https://device.jpush.cn/v3/aliases/{alias}';
    const DEVICES_URL = 'https://device.jpush.cn/v3/devices/{registration_id}';
    
    public  $masterSecret;
    public  $appKey;
    private $auth;
    private $headers;

    
    public function init()
    {
        //\JPush\JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
        //$this->client = new \JPush\JPushClient($this->appKey, $this->masterSecret);
    }
    
    
    /**
     * 生成消息内容
     */
    public function makeMsgContent($type, $title, $text, $url, $filter) {
        $msgContent = array();
        $msgContent['type'] = $type;
        $msgContent['title'] = $title;
        $msgContent['text'] = $text;
        $msgContent['url'] = $url;
        $msgContent['filter'] = $filter;
        return json_encode($msgContent);
    }

    
    /**
     * 调用极光发送消息platform_android_versionCode_1 platform_ios_versionCode_1
     * @param type $type
     * @param type $content
     */
    public function sendMsg($sendType, $recv, $type, $title, $text, $url, $filter) {
        $content = $this->makeMsgContent($type, $title, $text, $url, $filter);
        switch ($sendType) {
            case ConstPushMsgSendType::TO_All:
                return $this->send('all', $content);
            case ConstPushMsgSendType::TO_TAG_AND:
                return $this->send(\JPush\Model\Audience::audience(\JPush\Model\Audience::tag_and(json_decode($recv))), $content);
            case ConstPushMsgSendType::TO_TAG_OR:
                return $this->send(\JPush\Model\Audience::audience(\JPush\Model\Audience::tag(json_decode($recv))), $content);
            case ConstPushMsgSendType::TO_USER:
                return $this->send(\JPush\Model\Audience::audience(\JPush\Model\Audience::alias(json_decode($recv))), $content);
        }
    }

    
    /**
     * 发送推送
     * @param type $receiver 目标用户 全部：all
            array('alias'=>array(),
                'tag'=>array(),
                'tag_and'=>array(),
            )
     * @param type $content 发送的内容
     * @param type $extras 附带信息
     * @param type $platform 目标用户终端手机的平台类型android,ios,winphone
     */
  public function push($receiver, $title = '', $content='', $extras = '' , $platform = 'all', $m_time='86400'){
        if(empty($content)){
            return false;
        }
        if(empty($receiver)){
            $receiver = 'all';
        }
        if(empty($platform)){
            $platform = 'all';
        }
        if(empty($m_time) || $m_time <= 0 || $m_time > 86400){
            $m_time='86400';
        }

        $data = array();
        $data['platform'] = 'all';          //目标用户终端手机的平台类型android,ios,winphone
        $data['audience'] = $receiver;      //目标用户
        
        $data['notification'] = array(
            //统一的模式--标准模式
            "alert"=>$content,   
            //安卓自定义
            "android"=>array(
                "title"     =>$title,//标题
                "builder_id"=>1,
                "extras"    =>$extras,
            ),
            //ios的自定义
            "ios"=>array(
                "title"     => $title,
                "badge"     => "1",
                "sound"     => "default",
                "extras"    => $extras,
            ),
        );
 
//        //苹果自定义---为了弹出值方便调测
//        $data['message'] = array(
//            "msg_content"=>$content,
//            "extras"=>$extras,
//        );
        
        $sendno = time();
        //附加选项
        $data['options'] = array(
            "sendno"=>$sendno,
            "time_to_live"=>$m_time, //保存离线时间的秒数默认为一天
            "apns_production"=> YII_DEBUG ? 0 : 1,        //指定 APNS 通知发送环境：0开发环境，1生产环境。
        );

        $response = $this->auth()->send(self::PUSH_URL, json_encode($data), 'POST');
        if(YII_DEBUG){
            echo 'sendno:', $sendno, '<br>';
            var_dump($response);
        }
        return $response;
    }
    
    private function addHeaders($headers){
        if(!is_null($headers) && is_array($headers)){
            foreach ($headers as $header => $value) {
                $this->headers[$header] = $value;
            }
         }
        return $this;
    }
    private function buildHeaders(){
        $headers = array();
        foreach ($this->headers as $header => $value) {
            $headers[] = "{$header}:{$value}";
        }
        return $headers;
    }

    private function auth(){
        if(empty($this->auth)){
            $base64=base64_encode("64c6ff75d37c1ffb00786ed5:46c393aa6225e82af7e245d9");
            $this->auth = array('Authorization' => "Basic {$base64}",'Content-Type' => "application/json");
        }
        return $this;
    }

        //推送的Curl方法
    public function send($uri = null, $params = '', $method = 'GET', $headers = null, $ssl = true) {
        if (empty($uri) || empty($this->auth)) { return false; }
        $this->addHeaders($this->auth)->addHeaders($headers);
        if(YII_DEBUG){
            echo $method, ' ' , $uri , '<br>';
            echo 'param: ';
            if(is_string($params)){echo $params;}else var_dump($params);
            echo '<br> headers:';
            var_dump($this->headers);
            echo '<br>';
        }
        
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$uri);                     //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, FALSE);                 //表示需要response header
        //curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeaders());      // 增加 HTTP Header（头）里的字段
        
        if('POST' == $method){
            curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        
        if($ssl){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $response = curl_exec($ch);                                 //运行curl
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        $output['http_code'] = $http_code;
        //$output['header'] = substr($response, 0, $headerSize);
        $output['body'] = $response;
        curl_close($ch);
        
        return $output;
    }
    
    
    /**
     * 获取指定alias下的用户，最多输出10个
     * @param $alias
     * @param null $platform
     * @return DeviceResponse
     */
    public function getAliasDevices($alias, $platform = null) {
        if (is_null($alias) || !is_string($alias)) {
            return null;
        }

        $url = str_replace('{alias}', $alias, self::ALIAS_URL);


        if (is_array($platform)) {
            $isFirst = true;
            foreach($platform as $item) {
                if ($isFirst) {
                    $url = $url . '?platform=' . $item;
                    $isFirst = false;
                } else {
                    $url = $url . ',' . $item;
                }
            }
        }

        $response = $this->auth()->send($url, null, 'GET');
        YII_DEBUG ? var_dump($response) : 1;
        return $response;
    }
    
    /**
     * 获取指定RegistrationId的所有属性，包含tags, alias。
     * @param $registrationId
     * @return DeviceResponse
     */
    public function getDeviceTagAlias($registrationId) {
        if (!is_string($registrationId)) {
            return false;
        }
        $url = str_replace('{registration_id}' , $registrationId, self::DEVICES_URL);
        $response = $this->auth()->send($url, null, 'GET');
        YII_DEBUG ? var_dump($response) : 1;
        return $response;
    }
    
    
    /**
     * 移除指定RegistrationId的所有tag
     * @param $registrationId
     * @return DeviceResponse
     * @throws \InvalidArgumentException
     */
    public function removeDeviceTag($registrationId) {
        if (!is_string($registrationId)) {
            return false;
        }
        $payload = array('tags'=>'');
        $url = str_replace('{registration_id}' , $registrationId, self::DEVICES_URL);
        $response = $this->auth()->send($url, json_encode($payload), 'POST');
        YII_DEBUG ? var_dump($response) : 1;
        return $response;
    }
    
    /**
     * 更新指定RegistrationId的指定属性，当前支持tags, alias
     * @param $registrationId
     * @param null $alias
     * @param null $addTags
     * @param null $removeTags
     * @return DeviceResponse
     * @throws \InvalidArgumentException
     */
    public function updateDeviceTag($registrationId, $addTags = null, $removeTags = null) {
        if (!is_string($registrationId)) {
            return false;
        }
        if (empty($addTags) && empty($removeTags)) {
            return false;
        }
        
        $payload = array();
        $tags = array();

        if (!empty($addTags)) {
           if (is_array($addTags)) {
               $tags['add'] = $addTags;
           } else {
               $tags['add'] = array($addTags);
           }
        }

        if (!empty($removeTags)) {
            if (is_array($removeTags)) {
                $tags['remove'] = $removeTags;
            } else {
                $tags['remove'] = array($removeTags);
            }
        }

        if (count($tags) > 0) {
            $payload['tags'] = $tags;
        }
        
        $url = str_replace('{registration_id}' , $registrationId, self::DEVICES_URL);
        $response = $this->auth()->send($url, json_encode($payload), 'POST');
        YII_DEBUG ? var_dump($response) : 1;
        return $response;
    }
}

?>
