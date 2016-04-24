<?php

class DefaultController extends Controller
{
    
    //public $layout = 'application.modules.business.views.layouts.main' ;
    public $layout = '//layouts/blank';
    
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
                'actions' => array('error', 'index', 'product', 'contactUs', 'aboutUs', 'zhaopin', 'dingzhi'),
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
    
    
	public function actionError()
	{
        if ($error = Yii::app()->errorHandler->error) {
            if ($error['type'] == 'ResError') {
                Yii::app()->res->output($error['code'], $error['message']);
                return;
            }
        }
        Yii::app()->res->output(Error::REQUEST_EXCEPTION, "请求异常");
		//if($error=Yii::app()->errorHandler->error)
		//{
		//	if(Yii::app()->request->isAjaxRequest)
		//		echo $error['message'];
		//	else
		//		$this->render('error', $error);
		//}
	}

    
    /**
     * 首页
     */
    public function actionIndex()
	{
		$this->render('index');
	}
    
    
    /**
     * 产品
     */
    public function actionProduct()
	{
		$this->render('product');
	}
    
    
    /**
     * 联系我们
     */
    public function actionContactUs()
	{
		$this->render('contact-us');
	}
    
    
    /**
     * 关于
     */
    public function actionAboutUs()
	{
		$this->render('about-us');
	}

    /**
     * 招聘
     */
    public function actionZhaopin()
    {
        $this->render('zhaopin');
    }

    /**
     * 定制
     */ 
    public function actionDingzhi()
    {
        $this->render('dingzhi');
    }
    
}