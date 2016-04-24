<?php

class BusinessIdentity extends CUserIdentity
{
    
    public function authenticate()
	{
        $business = BusinessInfo::model()->find('u_name=:u_name', array(':u_name' => $this->username));
        if (empty($business) || ConstStatus::DELETE == $business->status){
            return $this->errorCode = Error::USERNAME_INVALID;
        }
        if($business->u_pass !== md5($business->salt . $this->password)){
            return $this->errorCode = Error::USERNAME_OR_USERPASS_INVALID;
        }
        
        $business->last_login_time = date("Y-m-d H:i:s", time());
        $business->update();
        
        $this->setState('id', $business->id);
        
        $this->errorCode = Error::NONE;
	}
    
}