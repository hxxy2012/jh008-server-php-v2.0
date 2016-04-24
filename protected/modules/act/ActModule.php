<?php

class ActModule extends CWebModule
{
    
    private $_assetsUrl;

    public function getAssetsUrl()
    {
        if($this->_assetsUrl === null)
        //$this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.act.assets'));
        $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.act.assets'), FALSE, -1, YII_DEBUG);
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
        //Yii::app()->getModule('user');
        //Yii::app()->getModule('business');
        
        $this->setImport(array(
            'act.models.*',
            'act.components.*',
            'user.models.*',
            'user.components.*',
            'business.models.*',
            'business.components.*',
            'admin.models.*',
            'admin.components.*',
            'manager.models.*',
            'manager.components.*',
            'org.models.*',
            'org.components.*',
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
                'class' => 'ActWebUser', //后台登录类实例
                'stateKeyPrefix' => 'user', //后台session前缀
                'loginUrl' => Yii::app()->createUrl('user/userInfo/notlogin'),
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
            if (isset($_GET['cityId']) && 1 == $_GET['cityId']) {
                $_GET['cityId'] = 385;
            }
            if (isset($_POST['cityId']) && 1 == $_POST['cityId']) {
                $_POST['cityId'] = 385;
            }
            return true;
        }
        else {
            return false;
        }
    }
}
