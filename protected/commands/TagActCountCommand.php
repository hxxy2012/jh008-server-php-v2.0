<?php

class TagActCountCommand extends CConsoleCommand 
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

        TimeLog::model()->timeTagCount();

        //更改标签所有的未结束的活动的数量
        TagInfo::model()->refreshCount();
    }

}

?>