<?php

class UWebUser extends CWebUser {

    //public function __get($key) 
    //{
    //    //如果cookie用户标识id存在，info不在则从db获取
    //    if (!$this->getIsGuest() && !$this->hasState('userInfo')) {
    //        $this->setState('userInfo', UserInfo::model()->findByPk($this->getId()));
    //    }
    //    if ($this->hasState('userInfo')) {
    //        $user = $this->getState('userInfo', array());
    //        if ('userInfo' == $key) {
    //            return $user;
    //        }
    //        if (isset($user[$key])) {
    //            return $user[$key];
    //        }
    //    }
    //    if ('userInfo' == $key) {
    //        return array();
    //    }
    //    return parent::__get($key);
    //}

    
    //public function __set($name, $value) 
    //{
    //    if ('userInfo' == $name) {
    //        $this->setState('userInfo', $value);
    //        return;
    //    }
    //    if ($this->hasState('userInfo')) {
    //        $user = $this->getState('userInfo', array());
    //        $user[$name] = $value;
    //        return;
    //    }
    //    parent::__set($name, $value);
    //}


    public function login($identity, $duration = NULL) 
    {
        $this->clearStates();
        //$this->setState('userInfo', $identity->getUserInfo());
        $duration = empty($duration) ? 7776000 : $duration; //3600 * 24 * 90
        return parent::login($identity, $duration);
    }
    
    
    public function coverLogin($uid) 
    {
        $this->setState('id', $uid);
    }

    
    public function getId() 
    {
        return $this->getState('id');
    }
    
    
    public function getPhoneNum()
    {
        return $this->getState('pho_num');
    }
    
}

?>