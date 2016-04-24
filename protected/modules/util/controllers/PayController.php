<?php

class PayController extends CController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + rechargePayUrl', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('rechargePayUrl'),
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
     * 充值支付url
     */
    public function actionRechargePayUrl()
    {
        $ck = Rules::instance(
            array(
                'totalFee' => Yii::app()->request->getPost('totalFee', 0.01),
                'way' => Yii::app()->request->getPost('way', 'wxpay'),
            ),
            array(
                array('totalFee, way', 'required'),
                array('totalFee', 'numerical', 'integerOnly' => FALSE),
                array('way', 'in', 'range' => array('alipay', 'wxpay')),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $orderRst = array();
        $order = new Order();
        $r = Order::model()->recordRechargeOrder(Yii::app()->user->id, $ck->totalFee, $order);
        if (!$r) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'create order fail');
        }
        
        $wxRst = Yii::app()->wxpay->unifiedOrder2Url($order->trade_no, $order->subject, $order->total_amount);
        print_r(json_encode($wxRst));
        exit();
        
        
        $orderRst['id'] = $order->id;
        $orderRst['trade_no'] = $order->trade_no;
        
        if (ConstPayPlatform::WECHATPAY == $ck->payPlatform) {
            //生成微信预支付
            //$wxRst = Yii::app()->wxpay->qrCodeUnifiedOrder($order->trade_no, $order->subject, $order->total_amount);
            //将预支付信息写入数据库
            //OrderPay::model()->upWxpayPreInfo($order->id, $wxRst);
            //$orderRst['wx'] = $wxRst;
        }
        
        //Yii::app()->res->output(Error::NONE, 'create order success', array('order' => $orderRst));
    }

    
    /**
     * 提现参数
     */
    public function actionWithdrawCashAllow()
    {
        $user = UserInfo::model()->findByPk(Yii::app()->user->id);
        $extend = UserInfoExtend::model()->find('t.u_id=:uid', array(':uid' => $user->id));
        $withdrawCashInfo = array();
        $withdrawCashInfo['allow_fee'] = $user->account_balance;
        $withdrawCashInfo['real_name'] = empty($extend) ? NULL : $extend->real_name;
        $withdrawCashInfo['allow_num'] = 3;
        Yii::app()->res->output(Error::NONE, 'withdraw cash allow info success', array('withdraw_cash_info' => $withdrawCashInfo));
    }
    
    
    /**
     * 申请提现
     */
    public function actionWithdrawCashApply()
    {
        $ck = Rules::instance(
            array(
                'totalFee' => Yii::app()->request->getPost('totalFee', 0.01),
                'realName' => Yii::app()->request->getPost('realName', '周垚岑'),
                'way' => Yii::app()->request->getPost('way', 'alipay'),
                'outAccount' => Yii::app()->request->getPost('outAccount'),
            ),
            array(
                array('totalFee, realName, way, outAccount', 'required'),
                array('totalFee', 'numerical', 'integerOnly' => FALSE),
                array('way', 'in', 'range' => array('alipay', 'wxpay')),
                array('outAccoutn', 'length', 'max' => 128),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $orderRst = array();
        $order = new Order();
        $r = Order::model()->recordRechargeOrder(Yii::app()->user->id, $ck->totalFee, $order);
        if (!$r) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'create order fail');
        }
        
        $wxRst = Yii::app()->wxpay->unifiedOrder2Url($order->trade_no, $order->subject, $order->total_amount);
        print_r(json_encode($wxRst));
        exit();
        
        
        $orderRst['id'] = $order->id;
        $orderRst['trade_no'] = $order->trade_no;
        
        if (ConstPayPlatform::WECHATPAY == $ck->payPlatform) {
            //生成微信预支付
            //$wxRst = Yii::app()->wxpay->qrCodeUnifiedOrder($order->trade_no, $order->subject, $order->total_amount);
            //将预支付信息写入数据库
            //OrderPay::model()->upWxpayPreInfo($order->id, $wxRst);
            //$orderRst['wx'] = $wxRst;
        }
        
        //Yii::app()->res->output(Error::NONE, 'create order success', array('order' => $orderRst));
    }
    
    
    /**
     * 账单
     */
    public function actionBills()
    {
        $ck = Rules::instance(
            array(
                'filter' => Yii::app()->request->getParam('filter', 0),
                'type' => Yii::app()->request->getParam('type', 'all'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 100),
            ),
            array(
                array('filter, type, page, size', 'required'),
                array('filter', 'in', 'range' => array(0, 1, 2)),
                array('type', 'in', 'range' => array('all', 'recharge', 'withdraw_cash')),
                array('page, size', 'numerical', 'integerOnly' => TRUE),
                
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        Yii::app()->res->output(Error::NONE, 'bills success', $rst);
    }
    
}
