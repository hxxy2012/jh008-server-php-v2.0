<?php

class ActBaseGrowNumsCommand extends CConsoleCommand 
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

        TimeLog::model()->timeActBaseGrowNums();

        //活动的各种数目的增长数基数按增长率刷新增长
        ActInfoAdmin::model()->refreshBaseGrowNums();
    }

}

?>