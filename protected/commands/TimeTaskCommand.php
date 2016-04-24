<?php

class TimeTaskCommand extends CConsoleCommand 
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

        TimeLog::model()->timeTask();

        //每分钟刷新定时任务表开启需要执行的任务进程
        TimeTask::model()->refreshExcTasks();
    }

}

?>