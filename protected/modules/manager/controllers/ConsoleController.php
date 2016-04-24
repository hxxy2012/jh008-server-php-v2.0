<?php

class ConsoleController extends ManagerController
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
                'actions' => array(''),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('baiduLBSListColumn', 'baiduLBSInitColumn', 'baiduLBSUpdateColumn', 'baiduLBSDeleteColumn', 'baiduLBSListPoi'),
				'users' => array('@'),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}


    /**
     * 查看百度lbs的column
     */
    public function actionBaiduLBSListColumn()
    {
        Yii::app()->baiduLBS->listColumn();
    }
    
    
    /**
     * 初始化百度lbs的column
     */
    public function actionBaiduLBSInitColumn()
    {
        Yii::app()->baiduLBS->initColumn();
    }
    
    
    /**
     * 修改百度lbs的column
     */
    public function actionBaiduLBSUpdateColumn()
    {
        Yii::app()->baiduLBS->updateColumn();
    }
    
    
    /**
     * 删除百度lbs的column
     */
    public function actionBaiduLBSDeleteColumn()
    {
        Yii::app()->baiduLBS->deleteColumn();
    }
    
    
    /**
     * 查看百度lbs的poi
     */
    public function actionBaiduLBSListPoi()
    {
        $page_index = Yii::app()->request->getParam('page_index', 0);
        Yii::app()->baiduLBS->listPoi($page_index);
    }
    
}