<?php

class AppInfoController extends ActController
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
                'actions' => array('lastVersion', 'lastVersionApk', 'downloadPage'),
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


    /**
     * 获取最新的版本信息
     */
    public function actionLastVersion() 
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getParam('type', 1),
            ),
            array(
                array('type', 'required'),
                array('type', 'in', 'range' => array(1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $app = AppInfo::model()->getLast($ck->type);
        Yii::app()->res->output(Error::NONE, '获取成功', array('app' => $app));
    }
    
    
    /**
     * 获取最新的安卓版本信息
     */
    public function actionLastVersionApk() 
    {
        $app = AppInfo::model()->getLast(1);
        if (empty($app)) {
            echo '404 Exception';
        }  else {
            $this->redirect($app['ver_url']);
        }
    }
    
    
    /**
     * 获取最新的Ios版本信息
     */
    public function actionLastVersionIos() 
    {
        $this->redirect("http://www.jhla.com.cn");
    }
    
    
    /**
     * 下载页面
     */
    public function actionDownloadPage()
    {
        $this->layout = '//layouts/blank';
        $this->render('download');
    }
    
}
