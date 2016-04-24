<?php

class BusinessModule extends CWebModule
{
    
    private $_assetsUrl;

    public function getAssetsUrl()
    {
        if($this->_assetsUrl === null)
        //$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.business.assets'));
        $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.business.assets'), FALSE, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }

    
    public function setAssetsUrl($value)
    {
        $this->_assetsUrl = $value;
    }
    
    
	public function init()
	{
        Yii::app()->getModule('util');
        
		$this->setImport(array(
			'business.models.*',
			'business.components.*',
            'user.models.*',
            'user.components.*',
            'act.models.*',
            'act.components.*',
		));
        
        $this->setComponents(array(
            
        ), false);
        
        Yii::app()->setComponents(array(
            'errorHandler' => array(
                'class' => 'CErrorHandler',
                'errorAction' => 'business/default/error',
            ),
            
            'user' => array(
                'allowAutoLogin' => true,
                'class' => 'BusinessWebUser', //后台登录类实例
                'stateKeyPrefix' => 'business', //后台session前缀
                'loginUrl' => Yii::app()->createUrl('business/default/login'),
            ),
            
            'viewRenderer' => array(
                'class' => 'application.vendor.yiiext.twig-renderer.ETwigViewRenderer',
                'twigPathAlias' => 'application.vendor.twig.twig.lib.Twig',

                // All parameters below are optional, change them to your needs
                'fileExtension' => '.twig',
                'options' => array(
                    'autoescape' => true,
                ),
                //'extensions' => array(
                //    'My_Twig_Extension',
                //),
                'globals' => array(
                    'html' => 'CHtml'
                ),
                'functions' => array(
                    'rot13' => 'str_rot13',
                ),
                'filters' => array(
                    'jencode' => 'CJSON::encode',
                ),
                // Change template syntax to Smarty-like (not recommended)
                //'lexerOptions' => array(
                //    'tag_comment'  => array('{*', '*}'),
                //    'tag_block'    => array('{', '}'),
                //    'tag_variable' => array('{$', '}')
                //),
                //all
                //'lexerOptions' => array(
                //    'tag_comment'   => array('{#', '#}'),
                //    'tag_block'     => array('{%', '%}'),
                //    'tag_variable'  => array('{{', '}}'),
                //    'interpolation' => array('#{', '}'),
                //),
            ),
        ));
        
	}

    
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action)) {
			return true;
		}
		else {
			return false;
        }
	}
    
}
