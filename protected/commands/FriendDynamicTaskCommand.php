<?php

class FriendDynamicTaskCommand extends CConsoleCommand 
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

        TimeLog::model()->timeFriendDynamicTask();

        //好友动态写入粉丝关联
        FriendDynamicTask::model()->refreshDynamicToFansTask();
    }

}

?>