<?php

class GatherCommand extends CConsoleCommand 
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
        //Yii::app()->getModule('act');
        //Yii::app()->getModule('admin');
        //TimeLog::model()->timeAll();
        //更改活动时间状态
        //ActInfo::model()->refreshTimeStatus();
        //更改标签所有的未结束的活动的数量
        //TagInfo::model()->refreshCount();
        //首页标签筛选活动，缓存数据更新
        //IndexPageActList::model()->refreshAll();
        //刷新每天注册用户统计
        //UserRegistCount::model()->refreshRegistCount();
        //刷新发送推送消息
        //PushMsgInfo::model()->refreshPushs();
        //群发消息写入群发消息与用户关联
        //MsgInfo::model()->refreshMsgToAllUsers();
        //刷新执行定时任务表
        //TimeTask::model()->refreshExcTasks();
    }

}

?>