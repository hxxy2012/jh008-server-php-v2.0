<?php

class PayController extends OrgController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + rechargePayUrl, withdrawCashApply', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array(''),
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
        return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'not allow to recharge');
        $ck = Rules::instance(
            array(
                'totalFee' => Yii::app()->request->getPost('totalFee'),
                'way' => Yii::app()->request->getPost('way'),
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
        
        if ($ck->way == 'wxpay') {
            $wxRst = Yii::app()->wxpay->unifiedOrder2Url($order->trade_no, $order->subject, $order->total_amount);
            print_r(json_encode($wxRst));
            exit();
            
            //生成微信预支付
            //$wxRst = Yii::app()->wxpay->qrCodeUnifiedOrder($order->trade_no, $order->subject, $order->total_amount);
            //将预支付信息写入数据库
            //OrderPay::model()->upWxpayPreInfo($order->id, $wxRst);
            //$orderRst['wx'] = $wxRst;
        }  else {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'not allow alipay');
        }
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
                'totalFee' => Yii::app()->request->getPost('totalFee'),
                'realName' => Yii::app()->request->getPost('realName'),
                'way' => Yii::app()->request->getPost('way'),
                'outAccount' => Yii::app()->request->getPost('outAccount'),
            ),
            array(
                array('totalFee, realName, way, outAccount', 'required'),
                array('totalFee', 'numerical', 'integerOnly' => FALSE),
                array('way', 'in', 'range' => array('alipay', 'wxpay')),
                array('outAccount', 'length', 'max' => 128),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = Order::model()->applyWithDrawCashOrder(Yii::app()->user->id, $ck->totalFee, $ck->way, $ck->outAccount);
        if (!$rst) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'apply withdraw cash fail');
        }
        
        Yii::app()->res->output(Error::NONE, 'apply withdraw cash success');
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
        
        $rst = Order::model()->bills(Yii::app()->user->id, $ck->filter, $ck->type, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'bills success', $rst);
    }
    
}
