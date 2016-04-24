<?php

class ConstPushMsgSendType {
    
    //tags:platform_android_versionCode_0 platform_ios_versionCode_0
    
    //发送给所有人
    const TO_All = 1;
    //发送给标签人群（交集）
    const TO_TAG_AND = 2;
    //发送给标签人群（并集）
    const TO_TAG_OR = 3;
    //发送给指定用户
    const TO_USER = 4;
    
}

?>
