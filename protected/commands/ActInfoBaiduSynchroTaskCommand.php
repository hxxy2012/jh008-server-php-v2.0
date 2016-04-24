<?php

class ActInfoBaiduSynchroTaskCommand extends CConsoleCommand 
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

        TimeLog::model()->timeActInfoBaiduSynchroTask();

        //活动表数据同步至百度lbs云存储，提供给客户端lbs云检索
        ActInfoBaiduSynchroTask::model()->refreshBaiduLbsSynchroTask();
    }

}

?>