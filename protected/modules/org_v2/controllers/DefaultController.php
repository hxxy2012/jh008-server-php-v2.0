<?php

/**
 * 页面controller
 */
class DefaultController extends Org_v2Controller
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
                'actions' => array('error', 'login','assnInfor', 'index'),
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
     * 首页--创建
     */
    public function actionIndex()
	{
		$this->render('index');
	}
    /**
     * 首页--正式
     */
    public function actionMain()
    {
        $this->render('main');
    }
    
    
    /**
     * 登录
     */
	public function actionlogin()
	{  
	    //echo 111;exit();
		$this->render('login');
	}
    
    /**
    *社团信息-基本资料
    */
    public function actionprofile()
    {
        $this->render('profile');
    }
    
    /**
    *社团信息-加入条件
    */
    public function actioncondition()
    {
        $this->render('condition');
    }
    
    /**
    *社团信息-二维码
    */
    public function actionqrcode()
    {
        $this->render('qrcode');
    }
 
    /**
    *社团信息-成员管理
    */
    public function actionManage()
    {
        $this->render('manage');
    }   
    /**
    *社团信息-成员审批
    */
    public function actionExamine()
    {
        $this->render('examine');
    }

    /**
    *社团信息-成员审批-已拒绝
    */
    public function actionExamineRefuse()
    {
        $this->render('examine_refuse');
    }

    /**
    *社团信息-成员审批-黑名单
    */
    public function actionExamineBlacklist()
    {
        $this->render('examine_blacklist');
    }

    /**
    *社团信息-成员审批-白名单
    */
    public function actionExamineWhitelist()
    {
        $this->render('examine_whitelist');
    }

    /**
    *社团信息-通知-发送通知
    */
    public function actionNotice()
    {
        $this->render('notice');
    }
    
    /**
    *社团信息-通知-历史记录
    */
    public function actionNoticeList()
    {
        $this->render('notice_list');
    }

    /**
    *社团信息-数据统计
    */
    public function actionstats()
    {
        $this->render('stats');
    }
    
    /**
    *社团信息-密码修改
    */
    public function actionpasswd()
    {
        $this->render('passwd');
    }
    
   
    /**
    *活动运营-发布活动
    */
    public function actionactivity_post()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_post',array('aid'=>$aid));
    }
    /**
    *活动运营-活动列表
    */
    public function actionactivity_list()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_list',array('aid'=>$aid));
    }
    /**
    *活动运营-活动分享
    */
    public function actionactivity_share()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_share',array('aid'=>$aid));
    }
    /**
    *活动运营-活动签到
    */
    public function actionactivity_sign()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_sign',array('aid'=>$aid));
    }
    /**
    *活动运营-活动报名审核
    */
    public function actionactivity_check()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_check',array('aid'=>$aid));
    }
    /**
    *活动运营-活动分组
    */
    public function actionactivity_group()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_group',array('aid'=>$aid));
    }
    /**
    *活动运营-活动通知
    */
    public function actionactivity_inform()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_inform',array('aid'=>$aid));
    }
    /**
    *活动运营-活动相册
    */
    public function actionactivity_photo()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_photo',array('aid'=>$aid));
    }
    
    /**
    *活动运营-活动用户相册
    */
    public function actionactivity_usersphoto()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_usersphoto',array('aid'=>$aid));
    }
    
    /**
    *活动运营-活动用户审核相册
    */
    public function actionactivity_usersphotocheck()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_usersphotocheck',array('aid'=>$aid));
    }
    /**
    *活动运营-活动报道
    */
    public function actionactivity_reports()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_reports',array('aid'=>$aid));
    }
    /**
    *活动运营-活动报道发布
    */
    public function actionactivity_news()
    {
        $aid=Yii::app()->request->getParam( 'id' );
        $this->render('activity_reports',array('aid'=>$aid));
    }
     /**
    *财务信息-缴费详情
    */
    public function actionpayment()
    {
        $this->render('payment');
    }
    /**
    *财务信息-社团流水
    */
    public function actioninventory()
    {
        $this->render('inventory');
    }
    
    /**
    *财务信息-申请提现
    */
    public function actiontransfer()
    {
        $this->render('transfer');
    }
    /**
    *财务信息-申请提现列表
    */
    public function actiontransfer_list()
    {
        $this->render('transfer_list');
    }
    /**
    *财务信息-退费
    */
    public function actionrefund()
    {

        $this->render('refund');
    }




    /**
     * 社团资料
     */
    public function actionAssnInfor()
    {

        $this->render('assnInfor');
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