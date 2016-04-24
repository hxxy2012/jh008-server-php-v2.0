<?php

class MsgModule extends CWebModule
{
	public function init()
	{
        $this->setImport(
            array(
                'msg.models.*',
                'msg.components.*',
                'msg.extensions.*',
                'msg.extensions.JPush.*',
            )
        );
        
        $this->setComponents(
            array(
            )
        );

        Yii::app()->setComponents(
            array(
                'sms' => array(
                    'class' => 'SmsTool',
                    'uid' => Yii::app()->params['sms']['uid'],
                    'keyMD5' => Yii::app()->params['sms']['keyMD5'],
                ),
                'jPush' => array(
                    'class' => 'JPushTool',
                    'masterSecret' => Yii::app()->params['jPush']['masterSecret'],
                    'appKey' => Yii::app()->params['jPush']['appKey'],
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
