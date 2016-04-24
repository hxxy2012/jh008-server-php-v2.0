<?php

class ConstPushMsgTaskType {
    
    //发送给所有人
    const TO_All = 1;
    //发送给单个用户
    const TO_USER = 2;
    
    //发送给安卓用户
    const TO_ANDROID = 11;
    //发送给IOS用户
    const TO_IOS = 12;
    
    //发送给用户粉丝
    const TO_USER_FANS = 21;
    
    //发送给活动当前城市所有用户
    const TO_ACT_CITY_ALL = 31;
    //发送给活动报名者
    const TO_ACT_ENROLL_USERS = 32;
    //发送给活动签到者
    const TO_ACT_CHECKIN_USERS = 33;
    
    //发送给资讯当前城市所有用户
    const TO_NEWS_CITY_ALL = 41;
    
    //发送给动态相关用户
    const TO_DYNAMIC_USERS = 51;
    //发送给动态评论相关用户
    const TO_DYNAMIC_COMMENT_USERS = 52;
    
}

?>
