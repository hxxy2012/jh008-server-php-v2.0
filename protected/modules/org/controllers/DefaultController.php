<?php

/**
 * 页面controller
 */
class DefaultController extends OrgController
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
                'actions' => array('error', 'login'),
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
        $up = new Uploader( "file" , $config );
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
    *活动数据
    */
   	public function actionData()
	{
		$this->render('data');
	}
    
    
    /**
    *设置-密码
    */
   	public function actionPwd()
	{
		$this->render('set_pwd');
	}
    /**
    *设置-管理员
    */
   	public function actionAdmin()
	{
		$this->render('set_admin');
	}
    /**
    *设置-资料
    */
   	public function actionZl()
	{
		$this->render('set_zl');
	}
    /**
    *活动
    */
    public function actionActivity()
    {
        $aid= Yii::app()->request->getParam( 'id' );
        $this->render('activity',array('aid'=>$aid));
    }
    
    /**
    *管理-报名
    */
    public function actionBm()
    {
        $aid= Yii::app()->request->getParam( 'id' );
        $this->render('gl_bm',array('aid'=>$aid));
    }
    
    /**
    *管理-分组
    */
    public function actionFz()
    {
       $aid=Yii::app()->request->getParam( 'id' );
        $this->render('gl_fz',array('aid'=>$aid));
    }
    /**
    *管理-相册
    */
    public function actionPhoto()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('gl_Photo',array('aid'=>$aid));
    }
    
    /**
    *管理-签到点
    */
    public function actionQd()
    {
         $aid=Yii::app()->request->getParam( 'id' );
        $this->render('gl_qd',array('aid'=>$aid));
    }
    
    /**
    *管理-信息
    */
    public function actionMag()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('gl_mag',array('aid'=>$aid));
    }
    
     /**
    *款项-添加
    */
    public function actionadd()
    {
        //$aid=Yii::app()->request->getParam( 'id' );
        $this->render('money_add');
    }
    /**
    *款项-申请提现
    */
    public function actiondraw()
    {
        //$aid=Yii::app()->request->getParam( 'id' );
        $this->render('money_draw');
    }
    
    /**
    *款项-明细
    */
    public function actioncostdetail()
    {
        //$aid=Yii::app()->request->getParam( 'id' );
        $this->render('money_costdetail');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}