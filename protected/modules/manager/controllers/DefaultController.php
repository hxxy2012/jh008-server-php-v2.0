<?php

class DefaultController extends ManagerController
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
                'actions' => array('error', 'login', 'cityLogin'),
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
		$this->render('managers');
	}

    
    /**
     * 注册
     */
	public function actionRegist()
	{
		$this->render('regist');
	}
    
    /**
     * 个人设置-修改密码
     */
    public function actionPersonalUpdatePass()
    {
        $this->render('personal_update_pass');
    }
    
    /**
     * 登录
     */
	public function actionLogin()
	{
		$this->render('login');
	}

    /**
     * 城市管理员登录
     */
    public function actionCityLogin()
    {
        $this->render('cityM_login');
    }   

    /**
     * 资讯
     */
    public function actionInformation()
    {
        $this->render('information');
    }

    /**
     * 票务
     */
    public function actionTicket()
    {
        $this->render('ticket');
    }   

    /**
     * 记忆
     */
    public function actionMemory()
    {
        $this->render('memory');
    }

    /**
     * 轮播
     */
    public function actionCarousel()
    {
        $this->render('carousel');
    }


    /**
     * 活动-列表
     */
    public function actionActList() {
        $this->render('act_list');
    }

    /**
     * 活动-标签管理
     */
    public function actionActTags() {
        $this->render('act_tags');
    }

    /**
     * 活动-上传
     */
    public function actionActAdd() {
        $this->render('act_add');
    }   

    /**
     * 活动-热门活动列表
     */
    public function actionActHot() {
        $this->render('act_hot');
    }    

    /**
     * 人物-用户列表
     */
    public function actionFigureUsers() {
        $this->render('figure_users');
    }  

    /**
     * 人物-达人列表
     */
    public function actionFigureMasters() {
        $this->render('figure_masters');
    } 

     /**
     * 人物-达人列表排序
     */
    public function actionFigureMastersSort() {
        $this->render('figure_masters_sort');
    }    

    /**
     * 申请达人列表
     */
    public function actionFigureApplyVips() {
        $this->render('figure_applyVips');
    }  

    /**
     * 人物-达人标签
     */
    public function actionFigureTags() {
        $this->render('figure_tags');
    }       
    
    /**
     * 管理员管理
     */
    public function actionManagers()
	{
		$this->render('managers');
	}

    /**
     * 添加城市
     */
    public function actionCitys()
    {
        $this->render('citys');
    } 

    /**
     * 向我们爆料
     */
    public function actionTipoff()
    {
        $this->render('tipoff');
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
    
}