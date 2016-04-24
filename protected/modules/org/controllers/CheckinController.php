<?php

/**
 * 活动分组页面
 * @author      吴华
 * @company     成都哈希曼普科技有限公司
 * @site        http://www.hashmap.cn
 * @email       wuhua@hashmap.cn
 * @date        2015-05-02
 */
class CheckinController extends OrgController
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
    public function actionCanvasdown()
	{
        $file = Yii::app()->request->getParam( 'file' );
        $filename = Yii::app()->request->getParam( 'filename' );
        //header('');
        $file = str_replace('data:image/png;base64,', '', $file);
        $file = str_replace(' ', '+', $file);
        $data = base64_decode($file);
        header('Content-Description: File Transfer');
        header('Content-Type: image/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($data));
        echo  $data ;
	}

    
    


    
    
    
    
    
    
}