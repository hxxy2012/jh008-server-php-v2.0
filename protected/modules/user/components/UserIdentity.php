<?php

class UserIdentity extends CUserIdentity
{
    
    //private $userId;
    //protected $userInfo;

    public $loginMethod;
    public $name;
    public $token;
    public $expiresIn;

    /**
     * 
     * @param type $loginMethod
     * @param type $name
     * @param type $token
     */
    public function __construct($loginMethod, $name, $token, $expiresIn = 0)
    {
        parent::__construct($name, $token);
        $this->loginMethod = $loginMethod;
        $this->name = $name;
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }
    
    
    //public function getUserInfo() {
    //    return $this->userInfo;
    //}


    public function authenticate()
	{
        $loginMethod = $this->loginMethod;
        $user = $this->$loginMethod();
        if (Error::NONE !== $this->errorCode || empty($user))
            return;
        
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->update();
        
        $this->setState('id', $user->id);
	}
    
    
    function phCodeNewAuthenticate() 
    {
        $model = UserPhoIdfy::model()->getLast($this->name);
        if (empty($model)) {
            return $this->errorCode = Error::PHONE_CODE_WRONG;
        }
        $this->phCodeAuthenticate($model);
        return NULL;
    }
    
    
    function phCodeHasAuthenticate() 
    {
        $user = UserInfo::model()->getByPhoneNum($this->name);
        if (empty($user) || ConstStatus::DELETE == $user->status || 1 != $user->is_regist) {
            return $this->errorCode = Error::USERNAME_INVALID;
        }
        $model = UserPhoIdfy::model()->getLast($this->name);
        if (empty($model)) {
            return $this->errorCode = Error::PHONE_CODE_WRONG;
        }
        $code = $this->phCodeAuthenticate($model);
        if (Error::NONE != $code) {
            return;
        }
        return UserInfo::model()->getByPhoneNum($model->pho_num);
    }
    
    
    /**
     * 手机号验证码验证
     * 
     * @param type $userPhoIdfy
     */
    function phCodeAuthenticate($userPhoIdfy) 
    {
        //验证码超时
        if (time() > strtotime($userPhoIdfy->invalid_time)) {
            return $this->errorCode = Error::PHONE_CODE_TIMEOUT;
        }
        //已验证
        if (!empty($userPhoIdfy->pass_time)) {
            return $this->errorCode = Error::PHONE_CODE_WRONG;
        }
        //验证码错误
        if (strtoupper(md5(strtoupper($userPhoIdfy->salt) . strtoupper($this->token))) !== strtoupper($userPhoIdfy->idfy_code)) {
            return $this->errorCode = Error::PHONE_CODE_WRONG;
        }
        
        $userPhoIdfy->pass_time = date('Y-m-d H:i:s');
        $userPhoIdfy->update();
        $this->setState('pho_num', $userPhoIdfy->pho_num);
        
        return $this->errorCode = Error::NONE;
    }
    
    
    /**
     * 手机号密码验证
     */
    function phoneAuthenticate() 
    {
        $user = UserInfo::model()->getByPhoneNum($this->name);
        if (empty($user) || ConstStatus::DELETE == $user->status || 1 != $user->is_regist) {
            return $this->errorCode = Error::USERNAME_INVALID;
        }
        if(strtoupper($user->user_pass) !== strtoupper(md5(strtoupper($user->salt) . strtoupper($this->token)))){
            return $this->errorCode = Error::USERNAME_OR_USERPASS_INVALID;
        }
        
        $this->errorCode = Error::NONE;
        return $user;
    }
    
    
    function sinaAuthenticate() 
    {
        $user = UserInfo::model()->getBySina($this->name);
        //if (empty($user) || ConstStatus::DELETE == $user->status || 0 == $user->is_regist) {
        if (empty($user)) {
            //未使用过第三方登录
            //if (!UserInfo::model()->sinaRegist($this->name, $this->token, $this->expiresIn)) {
            //    return $this->errorCode = Error::OPEN_TOKEN_INVALID;
            //}
            //$user = UserInfo::model()->getBySina($this->name);
            $this->errorCode = Error::USER_NOT_EXIST;
            return NULL;
        }  else {
            if (ConstStatus::DELETE == $user->status) {
                return $this->errorCode = Error::USERNAME_INVALID;
            }
            //已使用过第三方登录
            if ($user->sina_token !== $this->token) {
                UserInfo::model()->updateByPk($user->id, array('sina_token' => $this->token));
            }
        }
        
        $user->sina_expires_in = $this->expiresIn;
        $user->update();
        $this->errorCode = Error::NONE;
        return $user;
    }
    
    
    function qqAuthenticate() 
    {
        $user = UserInfo::model()->getByQq($this->name);
        //if (empty($user) || ConstStatus::DELETE == $user->status || 0 == $user->is_regist) {
        if (empty($user)) {
            //未使用过第三方登录
            //if (!UserInfo::model()->qqRegist($this->name, $this->token, $this->expiresIn)) {
            //    return $this->errorCode = Error::OPEN_TOKEN_INVALID;
            //}
            //$user = UserInfo::model()->getByQq($this->name);
            $this->errorCode = Error::USER_NOT_EXIST;
            return NULL;
        }  else {
            if (ConstStatus::DELETE == $user->status) {
                return $this->errorCode = Error::USERNAME_INVALID;
            }
            //已使用过第三方登录
            if ($user->qq_token !== $this->token) {
                UserInfo::model()->updateByPk($user->id, array('qq_token' => $this->token));
            }
        }
        
        $user->qq_expires_in = $this->expiresIn;
        $user->update();
        $this->errorCode = Error::NONE;
        return $user;
    }
    
    
    function wechatAuthenticate() 
    {
        $user = UserInfo::model()->getByWechat($this->name);
        //if (empty($user) || ConstStatus::DELETE == $user->status || 0 == $user->is_regist) {
        if (empty($user)) {
            //未使用过第三方登录
            //if (!UserInfo::model()->wechatRegist($this->name, $this->token, $this->expiresIn)) {
            //    return $this->errorCode = Error::OPEN_TOKEN_INVALID;
            //}
            //$user = UserInfo::model()->getByWechat($this->name);
            $this->errorCode = Error::USER_NOT_EXIST;
            return NULL;
        }  else {
            if (ConstStatus::DELETE == $user->status) {
                return $this->errorCode = Error::USERNAME_INVALID;
            }
            //已使用过第三方登录
            if ($user->wechat_token !== $this->token) {
                UserInfo::model()->updateByPk($user->id, array('wechat_token' => $this->token));
            }
        }
        
        $user->wechat_expires_in = $this->expiresIn;
        $user->update();
        $this->errorCode = Error::NONE;
        return $user;
    }
    
}