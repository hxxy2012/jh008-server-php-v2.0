<?php

class WebsiteWebUser extends CWebUser {

    public function __get($key) 
    {
        if ($this->hasState('websiteInfo')) {
            $website = $this->getState('websiteInfo', array());
            if ('websiteInfo' == $key) {
                return $website;
            }
            if (isset($website[$key])) {
                return $website[$key];
            }
        }
        if ('websiteInfo' == $key) {
            return array();
        }
        return parent::__get($key);
    }

    
    public function __set($name, $value) 
    {
        if ('websiteInfo' == $name) {
            $this->setState('websiteInfo', $value);
            return;
        }
        if ($this->hasState('websiteInfo')) {
            $website = $this->getState('websiteInfo', array());
            $website[$name] = $value;
            return;
        }
        parent::__set($name, $value);
    }


    public function login($identity, $duration=NULL) {
        $this->setState('websiteInfo', $identity->getWebsiteInfo());
        return parent::login($identity, $duration);
    }

}

?>