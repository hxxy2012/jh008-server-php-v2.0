<?php

class PayController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + createOrder', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('alipayNotifyUrl', 'wxpayNotifyUrl'),
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
     * 创建订单v2.0
     */
    public function actionCreateOrder()
    {
        $ck = Rules::instance(
            array(
                'subject' => Yii::app()->request->getPost('subject'),
                'body' => Yii::app()->request->getPost('body'),
                'total_fee' => Yii::app()->request->getPost('totalFee'),
            ),
            array(
                array('total_fee', 'required'),
                array('subject', 'length', 'max' => 128),
                array('body', 'length', 'max' => 512),
                array('total_fee', 'numerical', 'integerOnly' => FALSE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new PayOrder();
        $ck->setModelAttris($model);
        $model->u_id = Yii::app()->user->id;
        $rst = PayOrder::model()->add($model);
        if ($rst) {
            return Yii::app()->res->output(
                    Error::NONE, 
                    'create order success', 
                    array(
                        'id' => $model->id,
                        'trade_no' => $model->trade_no
                        )
                    );
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'create order fail');
    }
    
    
    /**
     * 下订单
     */
    public function actionPlaceOrder_v2()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getPost('type', 'act_enroll'),
                'productId' => Yii::app()->request->getPost('productId'),
                'number' => Yii::app()->request->getPost('number'),
                'payPlatform' => Yii::app()->request->getPost('payPlatform'),
            ),
            array(
                array('type, productId, number, payPlatform', 'required'),
                array('type', 'in', 'range' => array(ConstOrderType::ACT_ENROLL)),
                array('productId, number', 'numerical', 'integerOnly' => TRUE),
                array('payPlatform', 'in', 'range' => array(1, 2, 3)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = Order::model()->validProductId($ck->type, $ck->productId);
        if (!$r) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'order params valid fail');
        }
        
        $orderRst = array();
        $order = new Order();
        $r = Order::model()->createOrder($ck->type, $ck->productId, $ck->number, Yii::app()->user->id, NULL, $order);
        if (!$r) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'create order fail');
        }
        
        $orderRst['id'] = $order->id;
        $orderRst['trade_no'] = $order->trade_no;
        
        if (ConstPayPlatform::WECHATPAY == $ck->payPlatform) {
            //生成微信预支付
            $wxRst = Yii::app()->wxpay->unifiedOrder($order->trade_no, $order->subject, $order->total_amount);
            //将预支付信息写入数据库
            OrderPay::model()->upWxpayPreInfo($order->id, $wxRst);
            $orderRst['wx'] = $wxRst;
        }
        
        Yii::app()->res->output(Error::NONE, 'create order success', array('order' => $orderRst));
    }


    /**
     * 支付宝回调url
     */
    public function actionAlipayNotifyUrl() 
    {
        Yii::app()->alipay->notifyUrlValid();
    }
    
    
    /**
     * 微信回调url
     */
    public function actionWxpayNotifyUrl()
    {
        Yii::app()->wxpay->notifyUrlValid();
    }
    
    
    /**
     * 查询订单信息
     */
    public function actionOrderInfo_v2()
    {
        $ck = Rules::instance(
            array(
                'orderId' => Yii::app()->request->getPost('orderId'),
            ),
            array(
                array('orderId', 'required'),
                array('orderId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $orderInfo = Order::model()->profile($ck->orderId);
        Yii::app()->res->output(Error::NONE, 'order info success', array('order' => $orderInfo));
    }
    
}
