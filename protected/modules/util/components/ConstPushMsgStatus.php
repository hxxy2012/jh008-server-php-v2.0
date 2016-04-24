<?php

class ConstPushMsgStatus {
    
    //最大失败重发次数
    const MAX_FAIL_NUM = 3;
    //失败重发时间间隔 60 * 60 * 24 * 7 10分钟
    const FAIL_RESEND_TIME_INTERVAL = 600;

    //已删除
    const DELETE = -1;
    //还没成功发送
    const NOT_SEND = 0;
    //已发送成功
    const SEND_SUCCESS = 1;
    
}

?>
