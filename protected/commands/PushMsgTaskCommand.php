<?php

class PushMsgTaskCommand extends CConsoleCommand 
{

    public function run($args) 
    {
        Yii::app()->getModule('util');
        Yii::app()->setImport(array(
            'act.models.*',
            'act.components.*',
            'user.models.*',
            'user.components.*',
            'business.models.*',
            'business.components.*',
            'admin.models.*',
            'admin.components.*'
        ));

        TimeLog::model()->timePushMsgTask();

        //推送消息发送给用户
        PushMsgTask::model()->refreshPushMsgTask();
    }

}

?>