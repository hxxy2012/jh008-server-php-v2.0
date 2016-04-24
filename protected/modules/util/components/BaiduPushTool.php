<?php

class BaiduPushTool extends CComponent
{
    public $apiKey;
    public $secretKey;
    //如果ios应用当前部署状态为开发状态，指定DEPLOY_STATUS为1，默认是生产状态，值为2.
    public $iosDeployStatus;


    //public $apiKey = "W04WEydvC7ab9G9tFyToKSHY";
    //public $secretKey = "L0SEOyZr8hIyeAyUdeKBpa2rtoMVDrrl";

    protected $channel;
    
    public function init() 
    {
        $this->channel = new BaiduChannel($this->apiKey, $this->secretKey);
    }

    
    /**
     * 发送推送消息
     * 
     * @param type $pushPlatform 推送平台
     * @param type $pushType 推送类型
     * @param type $userId 百度生成的用户id
     * @param type $channelId 百度生成的频道id
     * @param type $tag 群发标签
     * @param type $title 标题
     * @param type $descri 描述
     * @param array $customKv 自定义字段
     * @param type $msgId 消息id
     */
    public function pushMsg($pushPlatform, $pushType, $userId, $channelId, $tag, $title, $descri, array $customKv, $msgId) 
    {
        switch ($pushPlatform) {
            case ConstPushMsgPlatform::TO_ALL:
                $this->pushToIos($pushType, $userId, $channelId, $tag, $title, $descri, $customKv, $msgId);
                $this->pushToAndroid($pushType, $userId, $channelId, $tag, $title, $descri, $customKv, $msgId);
                break;
            case ConstPushMsgPlatform::TO_ANDROID:
                $this->pushToAndroid($pushType, $userId, $channelId, $tag, $title, $descri, $customKv, $msgId);
                break;
            case ConstPushMsgPlatform::TO_IOS:
                $this->pushToIos($pushType, $userId, $channelId, $tag, $title, $descri, $customKv, $msgId);
                break;
            default:
                return FALSE;
        }
    }
    
    
    function pushToAndroid($pushType, $userId, $channelId, $tag, $title, $descri, array $customKv, $msgId) 
    {
        $optional[BaiduChannel::DEVICE_TYPE] = 3;
        //指定消息类型为通知1，消息0
        $optional[BaiduChannel::MESSAGE_TYPE] = 1;
        
        switch ($pushType) {
            case ConstPushMsgType::TO_All:
                $push_type = 3;
                break;
            case ConstPushMsgType::TO_TAG:
                $push_type = 2;
                $optional[BaiduChannel::TAG_NAME] = $tag;
                break;
            case ConstPushMsgType::TO_USER:
                $optional[BaiduChannel::USER_ID] = $userId;
                $optional[BaiduChannel::CHANNEL_ID] = $channelId;
                $push_type = 1;
                break;
            default:
                return FALSE;;
        }
        
        $messageArr = array();
        $messageArr['title'] = $title;
        $messageArr['description'] = $descri;
        $messageArr['notification_basic_style'] = 7;
        $messageArr['open_type'] = 1;
        $messageArr['url'] = 'www.jhla.com.cn';
        if (!empty($customKv)) {
            foreach ($customKv as $k => $v) {
                $messageArr['custom_content'][$k] = $v;
            }
        }
        $message = json_encode($messageArr);

        $message_key = $msgId;
        
        return $this->pushMessage($push_type, $message, $message_key, $optional);
    }
    
    
    function pushToIos($pushType, $userId, $channelId, $tag, $title, $descri, array $customKv, $msgId) 
    {
        $optional[BaiduChannel::DEVICE_TYPE] = 4;
        //指定消息类型为通知1，消息0
        $optional[BaiduChannel::MESSAGE_TYPE] = 1;
        
        //如果ios应用当前部署状态为开发状态，指定DEPLOY_STATUS为1，默认是生产状态，值为2.
        $optional[Channel::DEPLOY_STATUS] = $this->iosDeployStatus;
        
        switch ($pushType) {
            case ConstPushMsgType::TO_All:
                $push_type = 3;
                break;
            case ConstPushMsgType::TO_TAG:
                $push_type = 2;
                $optional[BaiduChannel::TAG_NAME] = $tag;
                break;
            case ConstPushMsgType::TO_USER:
                $optional[BaiduChannel::USER_ID] = $userId;
                $optional[BaiduChannel::CHANNEL_ID] = $channelId;
                $push_type = 1;
                break;
            default:
                return FALSE;;
        }
        
        $messageArr = array();
        $messageArr['title'] = $title;
        $messageArr['description'] = $descri;
        $messageArr['notification_basic_style'] = 7;
        $messageArr['open_type'] = 1;
        $messageArr['url'] = 'www.jhla.com.cn';
        if (!empty($customKv)) {
            foreach ($customKv as $k => $v) {
                $messageArr[$k] = $v;
            }
        }
        //$aps = array();
        //$aps['alert'] = 'descri';
        //$aps['sound'] = '';
        //$aps['badge'] = '0';
        //$messageArr['aps'] = $aps;
        $message = json_encode($messageArr);

        $message_key = $msgId;
        
        return $this->pushMessage($push_type, $message, $message_key, $optional);
    }
    
    
    function pushMessage($pushType, $message, $messageKey, $optional)
    {
        $ret = $this->channel->pushMessage($push_type, $message, $message_key, $optional);
        return $ret;
        //if ( false === $ret )
        //{
        //    echo 'WRONG,' . __FUNCTION__ . 'ERROR!!!!';
        //    echo 'ERROR NUMBER:' . __FUNCTION__ . $this->channel->errno();
        //    echo 'ERROR MESSAGE:' . __FUNCTION__ . $this->channel->errmsg();
        //    echo 'REQUEST ID:' . $this->channel->getRequestId();
        //}
        //else
        //{
        //    echo 'SUCC,' . __FUNCTION__ . ' OK!!!!!';
        //    echo 'result:' . print_r($ret, TRUE);
        //}
    }
    
}

?>
