<?php

class PayModule extends CWebModule
{
	public function init()
	{
        Yii::app()->getModule('util');
        
        $this->setImport(
            array(
                'pay.models.*',
                'pay.components.*',
                'pay.extensions.*',
                'pay.extensions.alipay.*',
                'pay.extensions.wxpay_api_php_v3.*',
                //外部关联
                'act.models.*',
            )
        );
        
        $this->setComponents(
            array(
            )
        );

        Yii::app()->setComponents(
            array(
                'pay' => array(
                    'class' => 'PayTool',
                ),
                'alipay' => array(
                    'class' => 'AlipayTool',
                    'partner' => Yii::app()->params['alipay']['partner'],
                    'privateKeyPath' => Yii::app()->params['alipay']['privateKeyPath'],
                    'aliPublicKeyPath' => Yii::app()->params['alipay']['aliPublicKeyPath'],
                ),
                'wxpay' => array(
                    'class' => 'WxpayTool',
                    'appid' => Yii::app()->params['wxpay']['appid'],
                    'mchid' => Yii::app()->params['wxpay']['mchid'],
                    'key' => Yii::app()->params['wxpay']['key'],
                    'appsecret' => Yii::app()->params['wxpay']['appsecret'],
                    'notify_url' => Yii::app()->params['wxpay']['notify_url'],
                    'sslcert_path' => Yii::app()->params['wxpay']['sslcert_path'],
                    'sslkey_path' => Yii::app()->params['wxpay']['sslkey_path'],
                    'curl_proxy_host' => Yii::app()->params['wxpay']['curl_proxy_host'],
                    'curl_proxy_port' => Yii::app()->params['wxpay']['curl_proxy_port'],
                    'report_level' => Yii::app()->params['wxpay']['report_level'],
                ),
            ), 
        false);
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}
}
