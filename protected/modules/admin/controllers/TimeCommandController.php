<?php

class TimeCommandController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + ', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('getTimeLogs', 'getTimeTasks', 'refreshAll', 'refreshActInfo', 'refreshTagInfo', 'refreshIndexPageActList', 'refreshRegistCount', 'refreshPush', 'refreshDynamicToFans', 'refreshVipSearch', 'refreshSystemMsgUser', 'refreshCityVipRandomDynamics', 'refreshBaiduLbsSynchro'),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users' => array('@'),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}

    
    public function actionGetTimeLogs()
    {
        $rst = TimeLog::model()->getAll();
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    public function actionGetTimeTasks()
    {
        $rst = TimeTask::model()->getNotOver();
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }


    public function actionRefreshAll()
    {
        ActInfo::model()->refreshTimeStatus();
        TagInfo::model()->refreshCount();
        IndexPageActList::model()->refreshAll();
        UserRegistCount::model()->refreshRegistCount();
        echo 'success';
    }
    
    
    public function actionRefreshActInfo()
    {
        ActInfo::model()->refreshTimeStatus();
        echo 'success';
    }
    
    
    public function actionRefreshTagInfo()
    {
        TagInfo::model()->refreshCount();
        echo 'success';
    }
    
    
    public function actionRefreshIndexPageActList()
    {
        IndexPageActList::model()->refreshAll();
        echo 'success';
    }
    
    
    public function actionRefreshRegistCount()
    {
        UserRegistCount::model()->refreshRegistCount();
        echo 'success';
    }
    
    
    public function actionRefreshPush()
    {
        PushMsgInfo::model()->refreshPushs(TRUE);
        echo 'success';
    }
    
    
    public function actionRefreshMsgToAllUsers()
    {
        MsgInfo::model()->refreshMsgToAllUsers();
        echo 'success';
    }
    
    
    public function actionRefreshDynamicToFans()
    {
        FriendDynamicTask::model()->refreshDynamicToFansTask();
        echo 'success';
    }
    
    
    public function actionRefreshVipSearch()
    {
        VipSearchTask::model()->refreshVipSearchTask();
        echo 'success';
    }
    
    
    public function actionRefreshSystemMsgUser()
    {
        SystemMsgUserTask::model()->refreshSystemMsgUserTask();
        echo 'success';
    }
    
    
    public function actionRefreshPushMsg()
    {
        PushMsgTask::model()->refreshPushMsgTask();
        echo 'success';
    }
    
    
    public function actionRefreshCityVipRandomDynamics()
    {
        CityVipRandomDynamicsTask::model()->refreshCityVipRandomDynamicsTask();
        echo 'success';
    }
    
    
    public function actionRefreshBaiduLbsSynchro()
    {
        //for ($i = 0; $i < 120; $i++) {
        //    Yii::app()->baiduLBS->deletePoi($i);
        //}
        ActInfoBaiduSynchroTask::model()->refreshBaiduLbsSynchroTask();
        echo 'success';
    }
    
}