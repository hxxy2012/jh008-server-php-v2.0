<?php

class ManagerWebUser extends CWebUser {

    //public function __get($key) 
    //{
    //    //如果cookie用户标识id存在，info不在则从db获取
    //    if (!$this->getIsGuest() && !$this->hasState('manager')) {
    //        $this->setState('manager', ManagerInfo::model()->findByPk($this->getId()));
    //    }
    //    if ($this->hasState('manager')) {
    //        $manager = $this->getState('manager', array());
    //        if ('manager' == $key) {
    //            return $manager;
    //        }
    //        if (isset($manager[$key])) {
    //            return $manager[$key];
    //        }
    //    }
    //    if ('manager' == $key) {
    //        return array();
    //    }
    //    return parent::__get($key);
    //}

    
    //public function __set($name, $value) 
    //{
    //    if ('manager' == $name) {
    //        $this->setState('manager', $value);
    //        return;
    //    }
    //    if ($this->hasState('manager')) {
    //        $manager = $this->getState('manager', array());
    //        $manager[$name] = $value;
    //        return;
    //    }
    //    parent::__set($name, $value);
    //}


    public function login($identity, $duration=NULL) {
        //$this->setState('manager', $identity->getManagerInfo());
        return parent::login($identity, $duration);
    }

    
    public function getId() {
        return $this->getState('id');
    }


    public function getType()
    {
        return $this->getState('type');
    }
    
    
    public function superManager()
    {
        return ConstManagerStatus::SUPER_MANAGER == $this->getType();
    }
    
    
    public function managerDataRegulator()
    {
        return ConstManagerStatus::MANAGER_DATA_REGULATOR == $this->getType();
    }
    
    
    public function managerOperator()
    {
        return ConstManagerStatus::MANAGER_OPERATOR == $this->getType();
    }
    
    
    public function cityManager()
    {
        return ConstCityManagerStatus::CITY_MANAGER == $this->getType();
    }
    
    
    public function cityOperator()
    {
        return ConstCityManagerStatus::CITY_OPERATOR == $this->getType();
    }

}

?>