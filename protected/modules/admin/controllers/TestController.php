<?php

class TestController extends AdminController
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
                'actions' => array('test'),
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

    
    public function actionTest()
    {
        //$rst = Yii::app()->wxpay->placeOrder('0.01', '测试订单' . date('Y-m-d H:i:s'), '20150408205201');
        //echo '<br>';
        //if (!$rst) {
        //    echo 'false';
        //}
        //print_r($rst);
        //echo KeyTool::des_do_mencrypt("{\"filter\":\"checkin_id\",\"value\":99}", 'zero2all');
        //Yii::app()->res->output(Error::NONE, 'success', array());
        
        //$rst = Yii::app()->wxpay->unifiedOrder('20150410120306', '测试订单', '0.01');
        //print_r($rst);
        $r = new Memcached(NULL, NULL);
    }
    
}