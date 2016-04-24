<?php

class UtilModule extends CWebModule {

    public function init() 
    {
        $this->setImport(
            array(
                'util.models.*',
                'util.components.*',
                'util.extensions.*',
                'util.extensions.oss_php_sdk_20140625.*',
                'util.extensions.JPush.*',
                'util.extensions.Baidu_Push_SDK_Php_2_0_4_advanced.*',
                'util.extensions.alipay.*',
                'util.extensions.wxpay_api_php_v3.*',
                //'util.extensions.Httpful.*',
            )
        );

        $this->setComponents(
            array(
            )
        );

        Yii::app()->setComponents(
            array(
                'res' => array(
                    'class' => 'Response',
                ),
                'openid' => array(
                    'class' => 'OpenidTool',
                    'sinaAppkey' => Yii::app()->params['openid']['sinaAppkey'],
                    'qqClientId' => Yii::app()->params['openid']['qqClientId'],
                ),
                'sms' => array(
                    'class' => 'SmsTool',
                    'uid' => Yii::app()->params['sms']['uid'],
                    'keyMD5' => Yii::app()->params['sms']['keyMD5'],
                ),
                'imgUpload' => array(
                    'class' => 'ImgUpload',
                    'rootPath' => Yii::app()->params['imgUpload']['rootPath'],
                    'downUrlPre' => Yii::app()->params['imgUpload']['downUrlPre'],
                ),
                'fileUpload' => array(
                    'class' => 'FileUpload',
                    'rootPath' => Yii::app()->params['fileUpload']['rootPath'],
                    'downUrlPre' => Yii::app()->params['fileUpload']['downUrlPre'],
                ),
                'webPage' => array(
                    'class' => 'WebPageTool',
                    'viewUrlPre' => Yii::app()->params['webPage']['viewUrlPre'],
                ),
                'qrCode' => array(
                    'class' => 'QrCodeTool',
                ),
                'jPush' => array(
                    'class' => 'JPushTool',
                    'masterSecret' => Yii::app()->params['jPush']['masterSecret'],
                    'appKey' => Yii::app()->params['jPush']['appKey'],
                ),
                'baiduPush' => array(
                    'class' => 'BaiduPushTool',
                    'apiKey' => Yii::app()->params['baiduPush']['apiKey'],
                    'secretKey' => Yii::app()->params['baiduPush']['secretKey'],
                    //如果ios应用当前部署状态为开发状态，指定DEPLOY_STATUS为1，默认是生产状态，值为2.
                    'iosDeployStatus' => Yii::app()->params['baiduPush']['iosDeployStatus'],
                ),
                'aliOss' => array(
                    'class' => 'AliOssTool',
                    'accessId' => Yii::app()->params['aliOss']['accessId'],
                    'accessKey' => Yii::app()->params['aliOss']['accessKey'],
                    'hostname' => Yii::app()->params['aliOss']['hostname'],
                    'bucket' => Yii::app()->params['aliOss']['bucket'],
                ),
                'baiduLBS' => array(
                    'class' => 'BaiduLbsTool',
                    'ak' => Yii::app()->params['baiduLBS']['ak'],
                    'sk' => Yii::app()->params['baiduLBS']['sk'],
                    'geotable_id' => Yii::app()->params['baiduLBS']['geotable_id'],
                ),
                'memcached' => array(
                    'class' => 'ext.MemCacheSASL.CMemCacheSASL',
                    'server' => array(
                        'host' => Yii::app()->params['memcached']['host'],
                        'port' => Yii::app()->params['memcached']['port'],
                        'username' => Yii::app()->params['memcached']['username'],
                        'password' => Yii::app()->params['memcached']['password'],
                    ),
                ),
            ), 
        false);
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else {
            return false;
        }
    }

}
