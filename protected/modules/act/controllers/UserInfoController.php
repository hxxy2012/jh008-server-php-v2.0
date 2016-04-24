<?php

class UserInfoController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + t', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(''),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    
    /**
     * 获取获取资料
     */
    public function actionGetInfo()
    {
        $model = UserInfo::model()->findByPk(Yii::app()->user->id);
        $info = $model->getMyUInfo();
        Yii::app()->res->output(Error::NONE, '获取成功', array('user' => $info));
    }
    
}
