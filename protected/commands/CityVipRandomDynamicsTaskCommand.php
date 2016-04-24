<?php

class CityVipRandomDynamicsTaskCommand extends CConsoleCommand 
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

        TimeLog::model()->timeCityVipRandomDynamicsTask();

        //城市达人随机动态
        CityVipRandomDynamicsTask::model()->refreshCityVipRandomDynamicsTask();
    }

}

?>