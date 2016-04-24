<?php

class CityManagerIdentity extends CUserIdentity
{
    
    protected $cityManager;

    public function getManagerInfo() {
        return $this->cityManager;
    }


    public function authenticate()
	{
        $manager = CityManager::model()->find('u_name=:u_name', array(':u_name' => $this->username));
        if (empty($manager) || ConstStatus::DELETE == $manager->status){
            return $this->errorCode = Error::USERNAME_INVALID;
        }
        if($manager->u_pass !== md5($manager->salt . $this->password)){
            return $this->errorCode = Error::USERNAME_OR_USERPASS_INVALID;
        }
        
        $cm = ManagerCityMap::model()->cityManager($manager->id);
        if (empty($cm)) {
            return $this->errorCode = Error::PERMISSION_DENIED;
        }
        
        
        $manager->last_login_time = date("Y-m-d H:i:s", time());
        $manager->update();
        
        $this->setState('id', $manager->id);
        $this->setState('type', $cm['type']);
        
        $this->cityManager = $manager;
        $this->errorCode = Error::NONE;
	}
    
}