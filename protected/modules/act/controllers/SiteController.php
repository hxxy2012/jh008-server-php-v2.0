<?php

class SiteController extends ActController
{
    //public $layout = 'application.modules.business.views.layouts.main' ;
    public $layout = '//layouts/blank';
    
	/**
	 * Declares class-based actions.
	 */
	//public function actions()
	//{
		//return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			//'captcha'=>array(
			//	'class'=>'CCaptchaAction',
			//	'backColor'=>0xFFFFFF,
			//),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			//'page'=>array(
			//	'class'=>'CViewAction',
			//),
		//);
	//}

    
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
                'actions' => array('userAgreeMent', 'BusinessLogin', 'AboutUs', 'CopyrightStatement', 'FunctionIntroduction', 'VipRecruit', 'BusinessCooperation', 'FeedbackProblem', 'SoftwareUpdate', 'OfficialWebsite'),
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
     * 用户使用协议
     */
    public function actionUserAgreeMent()
	{
		$this->render('user_agreement');
	}
    
    
    /**
     * 用户使用协议
     */
    public function actionBusinessLogin()
	{
		$this->render('business_login');
	}
    
    
    /**
     * 关于我们
     */
    public function actionAboutUs()
    {
        $this->render('about_us');
    }
    
    
    /**
     * 版权声明
     */
    public function actionCopyrightStatement()
    {
        $this->render('copyright_statement');
    }
    
    
    /**
     * 功能介绍
     */
    public function actionFunctionIntroduction()
    {
        $this->render('function_introduction');
    }
    
    
    /**
     * 招募达人
     */
    public function actionVipRecruit()
    {
        $this->render('vip_recruit');
    }


    /**
     * 业务合作
     */
    public function actionBusinessCooperation()
    {
        $this->render('business_cooperation');
    }
    
    
    /**
     * 问题反馈
     */
    public function actionFeedbackProblem()
    {
        $this->render('feedback_problem');
    }
    
    
    /**
     * 软件更新
     */
    public function actionSoftwareUpdate()
    {
        $this->render('software_update');
    }
    
    
    /**
     * 官方网站
     */
    public function actionOfficialWebsite()
    {
        $this->render('official_website');
    }
    
}