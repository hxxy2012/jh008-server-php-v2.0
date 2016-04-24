<?php

class ManagerLoginForm extends CFormModel
{
	public $u_name;
	public $u_pass;
	public $rememberMe;

	private $_identity;

	public function rules()
	{
		return array(
			array('u_name, u_pass', 'required'),
            array('u_name', 'CZhEnV', 'min' => 1, 'max' => 16),
            array('u_pass', 'CUserPassV'),
            array('rememberMe', 'in', 'range' => array(0, 1)),
            array('u_pass', 'authenticate'),
		);
	}

    
    /**
     * 验证
     * @param type $attribute
     * @param type $params
     */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity = new ManagerIdentity($this->u_name,$this->u_pass);
            $this->_identity->authenticate();
            //print_r($this->_identity->errorMessage);
			if(Error::NONE !== $this->_identity->errorCode) {
				$this->addError('password','Incorrect username or password.');
            }
		}
	}

    
    /**
     * 登录
     * @return boolean
     */
	public function login()
	{
		if(Error::NONE === $this->_identity->errorCode) {
			$duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else {
			return false;
        }
	}
    
}
