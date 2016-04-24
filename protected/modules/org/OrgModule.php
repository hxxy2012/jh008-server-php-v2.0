<?php

class OrgModule extends CWebModule
{
    
    private $_assetsUrl;

    public function getAssetsUrl()
    {
        if($this->_assetsUrl === null)
        $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.org.assets'), FALSE, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }

    
    public function setAssetsUrl($value)
    {
        $this->_assetsUrl = $value;
    }
    
    
    public function init()
	{
        Yii::app()->getModule('util');
        Yii::app()->getModule('pay');
        
		$this->setImport(array(
            'org.models.*',
			'org.components.*',
            'user.models.*',
            'user.components.*',
            'act.models.*',
            'act.components.*',
            'manager.models.*',
			'manager.components.*',
		));
        
        $this->setComponents(array(
            
        ), false);
        
        Yii::app()->setComponents(array(
            'errorHandler' => array(
                'class' => 'CErrorHandler',
                'errorAction' => 'org/default/error',
            ),
            
            'user' => array(
                'allowAutoLogin' => true,
                'class' => 'OrgWebUser', //后台登录类实例
                'stateKeyPrefix' => 'user', //后台session前缀
                'loginUrl' => Yii::app()->createUrl('org/default/login'),
            ),
            
            'viewRenderer' => array(
                'class' => 'application.vendor.yiiext.twig-renderer.ETwigViewRenderer',
                'twigPathAlias' => 'application.vendor.twig.twig.lib.Twig',

                // All parameters below are optional, change them to your needs
                'fileExtension' => '.twig',
                'options' => array(
                    'autoescape' => true,
                ),
                'globals' => array(
                    'html' => 'CHtml'
                ),
                'functions' => array(
                    'rot13' => 'str_rot13',
                ),
                'filters' => array(
                    'jencode' => 'CJSON::encode',
                ),
            ),
        ));
	}

    
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action)) {
            //未登录
            if (Yii::app()->user->isGuest) {
                return TRUE;
            }
            //接口accessRules，第一项无须登录配置包含此action（无须登录的接口）
            $rules = $controller->accessRules();
            if (!empty($rules[0]['users']) && in_array('*', $rules[0]['users']) && (empty($rules[0]['actions']) || in_array(strtolower($action->getId()), array_map('strtolower', $rules[0]['actions'])))) {
                return TRUE;
            }
            //已登录用户访问须登录接口须验证是否用社团用户
            //已登录uid为空
            $uid = Yii::app()->user->id;
            if (empty($uid)) {
                $controller->redirect('/org/default/login');
            }
            //非社团用户禁止使用社团web模块
            $orgId = Yii::app()->user->getOrgId();
            if (empty($orgId)) {
                $controller->redirect('/org/default/login');
            }
			return true;
		}
		else {
			return false;
        }
	}
    
}
