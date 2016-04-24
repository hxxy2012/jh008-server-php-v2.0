<?php

class AdminIdentity extends CUserIdentity
{
    
    public function authenticate()
	{
        $admin = AdminInfo::model()->find('u_name=:u_name', array(':u_name' => $this->username));
        if (empty($admin) || ConstStatus::DELETE == $admin->status){
            return $this->errorCode = Error::USERNAME_INVALID;
        }
        if($admin->u_pass !== md5($admin->salt . $this->password)){
            return $this->errorCode = Error::USERNAME_OR_USERPASS_INVALID;
        }
        
        $admin->last_login_time = date("Y-m-d H:i:s");
        $admin->update();
        
        $this->setState('id', $admin->id);
        
        $this->errorCode = Error::NONE;
	}
    
}