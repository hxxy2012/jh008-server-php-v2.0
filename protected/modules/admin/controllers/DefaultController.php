<?php

class DefaultController extends AdminController
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
                'actions' => array('error', 'regist', 'login'),
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
		$this->render('usercount');
	}

    
    /**
     * 注册
     */
	public function actionRegist()
	{
		$this->render('regist');
	}
    
    
    /**
     * 登录
     */
	public function actionLogin()
	{
		$this->render('login');
	}
    
    
    /**
     * 用户统计
     */
    public function actionUserCount()
	{
		$this->render('usercount');
	}
    
    
    /**
     * 管理员管理
     */
    public function actionAdmins()
	{
		$this->render('admins');
	}
    
    
    /**
     * 管理员管理
     */
    public function actionAdminViewInfo()
	{
		$this->render('adminviewinfo');
	}
    
    
    /**
     * 用户管理
     */
    public function actionUsers()
	{
		$this->render('users');
	}
    
    
    /**
     * 商家管理
     */
    public function actionBusiness()
	{
		$this->render('business');
	}
    
    
    /**
     * 标签管理
     */
    public function actionTags()
	{
		$this->render('tags');
	}
    
    
    /**
     * 活动管理
     */
    public function actionActs()
	{
		$this->render('acts');
	}
    
    
    /**
     * 签到管理
     */
    public function actionCheckins()
	{
		$this->render('checkins');
	}
    
    
    /**
     * 消息管理
     */
    public function actionMsgs()
	{
		$this->render('msgs');
	}
    
    
    /**
     * 版本管理
     */
    public function actionApps()
	{
		$this->render('apps');
	}
    
    
    /**
     * 推送列表
     */
    public function actionPushs()
	{
		$this->render('pushs');
	}
    
    
    /**
     * 消息类型
     */
    public function actionMsgTypes()
	{
		$this->render('msg_types');
	}
    
    
    /**
     * push类型
     */
    public function actionPushTypes()
	{
		$this->render('push_types');
	}
    
    
    /**
     * 抽奖页面
     */
    public function actionPrize()
    {
        $this->render('prize');
    }


    /**
     * 百度编辑器图片上传
     */
    public function actionUeditorImgUp()
    {
        //header("Content-Type:text/html;charset=utf-8");
        //error_reporting( E_ERROR | E_WARNING );
        //date_default_timezone_set("Asia/chongqing");
        //include "Uploader.class.php";
        //上传配置
        $config = array(
            "savePath" => "upload/" ,             //存储文件夹
            //"maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
            "maxSize" => 1024 ,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
        );
        //上传文件目录
        $Path = "upload/";

        //背景保存在临时目录中
        $config[ "savePath" ] = $Path;
        $up = new Uploader( "upfile" , $config );
        //$type = $_REQUEST['type'];
        //$callback=$_GET['callback'];

        $info = $up->getFileInfo();
        /**
         * 返回数据
         */
        //if($callback) {
        //    echo '<script>'.$callback.'('.json_encode($info).')</script>';
        //} else {
            echo json_encode($info);
        //}
    }
    
    /**
     * push类型
     */
    public function actionRecommends()
    {
        $this->render('recommends');
    }
}