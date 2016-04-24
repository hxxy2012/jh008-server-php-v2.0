<?php

class UserModule extends CWebModule
{
    public function init()
    {
        Yii::app()->getModule('util');
        
        $this->setImport(array(
            'user.models.*',
            'user.components.*',
            'act.components.*',
            'act.models.*',
        ));
        
        $this->setComponents(array(  
            
        ), false);
        
        Yii::app()->setComponents(array(
            'errorHandler' => array(
                'class' => 'CErrorHandler',
                'errorAction' => 'user/userInfo/error',
            ),
            
            'user' => array(
                'allowAutoLogin' => true,
                'class' => 'UWebUser', //后台登录类实例
                'stateKeyPrefix' => 'user', //后台session前缀
                'loginUrl' => Yii::app()->createUrl('user/userInfo/notlogin'),
            ),
            
        ));
    }

    
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action)){
			return true;
		}
		else{
			return false;
        }
	}
}
