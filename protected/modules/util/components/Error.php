<?php

class Error {
    //=============================常用错误=========================
    const NONE = 0;
    //用户名不存在
    const USERNAME_INVALID = 1;
    //用户名或密码错误
    const USERNAME_OR_USERPASS_INVALID = 2;
    //第三方登录验证失败
    const OPEN_TOKEN_INVALID = 3;
    //请求异常
    const REQUEST_EXCEPTION = 4;
    //cookie无效，登录无效
    const LOGIN_INVALID = 5;
    //操作异常
    const OPERATION_EXCEPTION = 9;
    //用户名已存在
    const USERNAME_EXIST = 10;
    //参数非法
    const PARAMS_ILLEGAL = 11;
    //权限不够
    const PERMISSION_DENIED = 14;
    //用户不存在
    const USER_NOT_EXIST = 15;
    //版本过低
    const VERSION_TOO_LOW = 18;
    //=============================数据相关=========================
    //记录不存在
    const RECORD_NOT_EXIST = 101;
    //记录已存在
    const RECORD_HAS_EXIST = 102;
    //=============================文件相关=========================
    //OSS操作异常
    const OSS_OPERATION_EXCEPTION = 201;
    //文件操作异常
    const FILE_OPERATION_EXCEPTION = 202;
    //=============================验证相关=========================
    //手机验证码超时
    const PHONE_CODE_TIMEOUT = 301;
    //手机验证码错误
    const PHONE_CODE_WRONG = 302;
    //手机验证码发送频繁
    const PHONE_CODE_SEND_FREQUENT = 303;
    //=============================短信相关=========================
    //sms短信api发送失败
    const SMS_SEND_FAIL = 330;
    //=============================业务相关=========================
    //人数已满
    const ACT_ENROLL_TOO_NUM = 350;
    //报名时间不满足
    const ACT_ENROLL_TIME_ERROR = 351;
    
    
    
}

?>
