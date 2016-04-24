<?php

class ManagerIdentity extends CUserIdentity
{
    
    //protected $managerInfo;

    //public function getManagerInfo() 
    //{
    //    return $this->managerInfo;
    //}


    public function authenticate()
	{
        $manager = ManagerInfo::model()->find('u_name=:u_name', array(':u_name' => $this->username));
        if (empty($manager) || ConstStatus::DELETE == $manager->status){
            return $this->errorCode = Error::USERNAME_INVALID;
        }
        if($manager->u_pass !== md5($manager->salt . $this->password)){
            return $this->errorCode = Error::USERNAME_OR_USERPASS_INVALID;
        }
        
        $manager->last_login_time = date("Y-m-d H:i:s");
        $manager->update();
        
        $this->setState('id', $manager->id);
        $this->setState('type', $manager->type);
        
        //$this->managerInfo = $manager;
        $this->errorCode = Error::NONE;
	}
    
}