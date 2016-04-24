<?php

/**
 * This is the model class for table "order".
 *
 * The followings are the available columns in table 'order':
 * @property string $id
 * @property string $payer_id
 * @property string $payee_id
 * @property string $trade_no
 * @property string $trade_type
 * @property string $subject
 * @property string $body
 * @property double $total_amount
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property UserInfo $payee
 * @property UserInfo $payer
 * @property OrderDetail[] $orderDetails
 * @property OrderPay[] $orderPays
 */
class Order extends PayModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('total_amount, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('total_amount', 'numerical'),
			array('payer_id, payee_id', 'length', 'max'=>10),
			array('trade_no, subject', 'length', 'max'=>128),
			array('trade_type', 'length', 'max'=>255),
			array('body', 'length', 'max'=>512),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, payer_id, payee_id, trade_no, trade_type, subject, body, total_amount, status, create_time, modify_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			//'payee' => array(self::BELONGS_TO, 'UserInfo', 'payee_id'),
			//'payer' => array(self::BELONGS_TO, 'UserInfo', 'payer_id'),
			//'orderDetails' => array(self::HAS_MANY, 'OrderDetail', 'order_id'),
			//'orderPays' => array(self::HAS_MANY, 'OrderPay', 'order_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '订单id',
			'payer_id' => '付款方id',
			'payee_id' => '收款方id',
			'trade_no' => '订单号',
			'trade_type' => '订单类型',
			'subject' => '订单名称',
			'body' => '订单描述',
			'total_amount' => '总金额',
			'status' => '订单状态',
			'create_time' => '创建时间',
			'modify_time' => '更新时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Order the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 验证商品id
     * 
     * @param type $orderType
     * @param type $productId
     */
    public function validProductId($orderType, $productId)
    {
        if (ConstOrderType::ACT_ENROLL == $orderType) {
            $model = ActInfoExtend::model()->find('t.product_id=:productId', array(':productId' => $productId));
            if (!empty($model)) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    
    /**
     * 记录短信订单
     * 
     * @param type $uid 用户id
     * @param type $num 短信条数
     */
    public function recordSmsOrder($uid, $num)
    {
        //扣除1的钱
        //增加uid的钱
    }
    
    
    /**
     * 短信发送失败退费订单
     * 
     * @param type $uid
     * @param type $num
     */
    public function refundSmsOrder($uid, $num) 
    {
        //扣除1的钱
        //增加uid的钱
    }
    
    
    /**
     * 记录充值订单
     * 
     * @param type $uid 用户id
     * @param type $totalAmount 金额
     * @param type $model
     */
    public function recordRechargeOrder($uid, $totalAmount, $model = NULL)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            if (empty($model)) {
                $model = new Order();
            }
            $order = $model;
            $order->payer_id = $uid;
            $order->payee_id = $uid;
            $order->trade_no = Yii::app()->pay->buildUniqueNo();
            $order->trade_type = ConstOrderType::RECHARGE;
            $order->subject = '充值';
            $order->body = '充值';
            $order->total_amount = $totalAmount;
            $order->status = ConstOrderStatus::WAIT_PAY;
            $order->create_time = date('Y-m-d H:i:s');
            $order->modify_time = date('Y-m-d H:i:s');
            $r = $order->save();
            if (!$r) {
                //print_r($order->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $detail = new OrderDetail();
            $detail->order_id = $order->id;
            $detail->product_id = NULL;
            $detail->unit_price = $totalAmount;
            $detail->number = 1;
            $detail->total_amount = $totalAmount;
            $detail->create_time = date('Y-m-d H:i:s');
            $detail->modify_time = date('Y-m-d H:i:s');
            $r = $detail->save();
            if (!$r) {
                //print_r($detail->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    
    /**
     * 申请提现订单
     * 
     * @param type $uid 用户id
     * @param type $totalAmount 金额
     * @param type $way 途径
     * @param type $outAccount 外部账号
     */
    public function applyWithDrawCashOrder($uid, $totalAmount, $way, $outAccount)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            if (empty($model)) {
                $model = new Order();
            }
            $order = $model;
            $order->payer_id = $uid;
            $order->payee_id = 1;
            $order->trade_no = Yii::app()->pay->buildUniqueNo();
            $order->trade_type = ConstOrderType::WITHDRAW_CASH_APPLY;
            $order->subject = '提现申请';
            $order->body = '提现申请';
            $order->total_amount = $totalAmount;
            $order->status = ConstOrderStatus::WAIT_PAY;
            $order->create_time = date('Y-m-d H:i:s');
            $order->modify_time = date('Y-m-d H:i:s');
            $r = $order->save();
            if (!$r) {
                //print_r($order->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $detail = new OrderDetail();
            $detail->order_id = $order->id;
            $detail->product_id = NULL;
            $detail->unit_price = $totalAmount;
            $detail->number = 1;
            $detail->total_amount = $totalAmount;
            $detail->create_time = date('Y-m-d H:i:s');
            $detail->modify_time = date('Y-m-d H:i:s');
            $r = $detail->save();
            if (!$r) {
                //print_r($detail->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $withdraw = new WithdrawCashApply();
            $withdraw->order_id = $order->id;
            $withdraw->total_fee = $totalAmount;
            $withdraw->way = $way;
            $withdraw->out_account = $outAccount;
            $withdraw->create_time = date('Y-m-d H:i:s');
            $withdraw->modify_time = date('Y-m-d H:i:s');
            $r = $withdraw->save();
            if (!$r) {
                //print_r($detail->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $transaction->commit();
            Trans::model()->add(ConstTransType::APPLY_WITHDRAW_CASH, $order->trade_no, $order->payer_id, 1, (-1 * $order->total_amount), '支出-提现申请');
            Trans::model()->add(ConstTransType::TEM_APPLY_WITHDRAW_CASH, $order->trade_no, 1, $order->payer_id, $order->total_amount, '收入-暂存提现申请');
            return TRUE;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    
    /**
     * 提现处理成功
     * 
     * @param type $tradeNo 订单号
     * @param type $isSuccess 是否已成功
     */
    public function overWithDrawCashOrder($tradeNo, $isSuccess = FALSE)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $order = $this->getByTradeNo($tradeNo);
            if (empty($order) || ConstStatus::DELETE == $order->status || ConstOrderType::WITHDRAW_CASH_APPLY != $order->trade_type) {
                $transaction->rollBack();
                return FALSE;
            }
            if ($isSuccess) {
                $order->status = ConstOrderStatus::HAS_PAY;
                $order->modify_time = date('Y-m-d H:i:s');
                $r = $order->update();
                if (!$r) {
                    $transaction->rollBack();
                    return FALSE;
                }
                Trans::model()->add(ConstTransType::WITHDRAW_CASH, $order->trade_no, 1, 1, (-1 * $order->total_amount), '支出-提现');
            }  else {
                $order->status = ConstOrderStatus::FAIL_PAY;
                $order->modify_time = date('Y-m-d H:i:s');
                $r = $order->update();
                if (!$r) {
                    $transaction->rollBack();
                    return FALSE;
                }
                Trans::model()->add(ConstTransType::REFUND_TEM_APPLY_WITHDRAW_CASH, $order->trade_no, 1, $order->payer_id, (-1 * $order->total_amount), '支出-暂存提现申请退费');
                Trans::model()->add(ConstTransType::REFUND_APPLY_WITHDRAW_CASH, $order->trade_no, $order->payer_id, 1, $order->total_amount, '收入-提现申请退费');
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    
    /**
     * 创建订单
     * 
     * @param type $type 订单类型
     * @param type $productId 商品id
     * @param type $number 份数
     * @param type $payer_id 付款方id
     * @param type $payee_id 收款方id
     * @param type $model 订单model
     */
    public function createOrder($type, $productId, $number, $payer_id, $payee_id, $model = NULL)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $product = Product::model()->findByPk($productId);
            if (empty($model)) {
                $model = new Order();
            }
            $order = $model;
            $order->payer_id = $payer_id;
            if (empty($payee_id)) {
                $payeeId = Product::model()->getPayeeId($productId);
                $order->payee_id = empty($payeeId) ? NULL : $payeeId;
            }  else {
                $order->payee_id = $payee_id;
            }
            $order->trade_no = Yii::app()->pay->buildUniqueNo();
            $order->trade_type = $type;
            $order->subject = $product->subject;
            $order->body = $product->body;
            $order->total_amount = $product->unit_price * $number;
            $order->status = ConstOrderStatus::WAIT_PAY;
            $order->create_time = date('Y-m-d H:i:s');
            $order->modify_time = date('Y-m-d H:i:s');
            $r = $order->save();
            if (!$r) {
                //print_r($order->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $product->id;
            $orderDetail->unit_price = $product->unit_price;
            $orderDetail->number = $number;
            $orderDetail->total_amount = $product->unit_price * $number;
            $orderDetail->create_time = date('Y-m-d H:i:s');
            $orderDetail->modify_time = date('Y-m-d H:i:s');
            $r = $orderDetail->save();
            if (!$r) {
                //print_r($orderDetail->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    
    /**
     * 订单信息
     * 
     * @param type $orderId 订单id
     */
    public function profile($orderId)
    {
        $model = $this->findByPk($orderId);
        if (empty($model)) {
            return NULL;
        }
        
        $order = array();
        $order['id'] = $model->id;
        $order['trade_no'] = $model->trade_no;
        $order['trade_type'] = $model->trade_type;
        $order['subject'] = $model->subject;
        $order['body'] = $model->body;
        $order['total_amount'] = $model->total_amount;
        $order['status'] = $model->status;
        $order['create_time'] = $model->create_time;
        
        //查详情
        $detail = OrderDetail::model()->profile($model->id);
        if (!empty($detail)) {
            array_merge($order, $detail);
        }
        
        //未支付查微信预支付信息
        if (ConstOrderStatus::WAIT_PAY == $model->status) {
            $wx = OrderPay::model()->wxpayPreInfo($model->id);
            if (!empty($wx)) {
                array_merge($order, $wx);
            }
        }
        return $order;
    }
    
    
    /**
     * 处理支付宝回调业务
     * 
     * @param type $data 数据
     */
    public function alipayNotify($data)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        $transactionAct = Yii::app()->dbAct->beginTransaction();
        $transactionUser = Yii::app()->dbUser->beginTransaction();
        try {
            //验证订单合法
            $order = $this->getByTradeNo($data['out_trade_no']);
            $rst = $this->validOrder($order, $data['total_fee']);
            if (!$rst) {
                $transaction->rollBack();
                $transactionAct->rollBack();
                $transactionUser->rollBack();
                return;
            }

            //处理订单及支付宝记录
            $rst = $this->dealWithOrderAndAliPay($order, $data['trade_no'], $data['total_fee'], $data['buyer_email'], $data['gmt_create_time'], $data['gmt_payment']);
            if (!$rst) {
                $transaction->rollBack();
                $transactionAct->rollBack();
                $transactionUser->rollBack();
                return;
            }
            $transaction->commit();
            
            $detail = OrderDetail::model()->get($order->id);
            if (empty($detail) || empty($detail->product_id)) {
                $transactionAct->rollBack();
                $transactionUser->rollBack();
                return;
            }
            
            //订单匹配后，记录流水
            switch ($order->trade_type) {
                case ConstOrderType::RECHARGE:
                    //往收款方账户入账
                    $rst = $this->modifyBalance($order->payee_id, $order->payer_id, $order->total_amount, ConstTransType::RECHARGE, '充值-到帐', $order->trade_no);
                    if (!$rst) {
                        $transactionAct->rollBack();
                        $transactionUser->rollBack();
                        return;
                    }
                    $transactionAct->commit();
                    $transactionUser->commit();
                    return;
                case ConstOrderType::WITHDRAW_CASH:
                    break;
                case ConstOrderType::ACT_ENROLL:
                    //记录报名方支付流水（-）
                    Trans::model()->add(ConstTransType::PAY_ACT_ENROLL, $order->trade_no, $order->payer_id, $order->payee_id, (-1 * $order->total_amount), '支付-活动报名费');
                    //处理报名相关业务
                    $r = $this->notifyActEnroll($order, $detail);
                    if (!$r) {
                        $transactionAct->rollBack();
                        $transactionUser->rollBack();
                        return;
                    }
                    //往收款方账户入账，记录主办方报名费收取流水（+）
                    $rst = $this->modifyBalance($order->payee_id, $order->payer_id, $order->total_amount, ConstTransType::REV_ACT_ENROLL, '收入-活动报名费', $order->trade_no);
                    //手续费结算
                    $rstFee = TRUE;
                    $rstFeeO = TRUE;
                    if ($detail->payee_fee > 0) {
                        $rstFee = $this->modifyBalance($order->payee_id, 1, (-1 * $detail->payee_fee), ConstTransType::REV_ACT_ENROLL_FEE, '支出-活动报名费的手续费', $order->trade_no);
                        $rstFeeO = $this->modifyBalance(1, $order->payee_id, $detail->payee_fee, ConstTransType::FEE, '收入-活动报名费的手续费', $order->trade_no);
                    }
                    if (!$rst || !$rstFee || !$rstFeeO) {
                        $transactionAct->rollBack();
                        $transactionUser->rollBack();
                        return;
                    }
                    $transactionAct->commit();
                    $transactionUser->commit();
                    return;
                case ConstOrderType::SMS:
                    break;
                default:
                    Trans::model()->add(ConstTransType::EXPENDITURE, $order->trade_no, $order->payer_id, $order->payee_id, $order->total_amount, '支出');
                    break;
            }
            $transactionAct->rollBack();
            $transactionUser->rollBack();
            return;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
            $transactionAct->rollBack();
            $transactionUser->rollBack();
            return;
        }
    }
    
    
    /**
     * 处理微信回调业务
     * 
     * @param type $data 数据
     */
    public function wxpayNotify($data)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        $transactionAct = Yii::app()->dbAct->beginTransaction();
        $transactionUser = Yii::app()->dbUser->beginTransaction();
        try {
            //验证订单合法
            $order = $this->getByTradeNo($data['out_trade_no']);
            $rst = $this->validOrder($order, $data['total_fee'] / 100.00);
            if (!$rst) {
                $transaction->rollBack();
                $transactionAct->rollBack();
                $transactionUser->rollBack();
                return;
            }

            //处理订单及微信记录
            $rst = $this->dealWithOrderWxPay($order, $data['transaction_id'], $data['total_fee'] / 100.00, $data['openid'], date('Y-m-d H:i:s', strtotime($data['time_end'])));
            if (!$rst) {
                $transaction->rollBack();
                $transactionAct->rollBack();
                $transactionUser->rollBack();
                return;
            }
            $transaction->commit();
            
            $detail = OrderDetail::model()->get($order->id);
            if (empty($detail) || empty($detail->product_id)) {
                $transactionAct->rollBack();
                $transactionUser->rollBack();
                return;
            }
            
            //处理业务及对应流水
            switch ($order->trade_type) {
                case ConstOrderType::RECHARGE:
                    //往收款方账户入账
                    $rst = $this->modifyBalance($order->payee_id, $order->payer_id, $order->total_amount, ConstTransType::RECHARGE, '充值-到帐', $order->trade_no);
                    if (!$rst) {
                        $transactionAct->rollBack();
                        $transactionUser->rollBack();
                        return;
                    }
                    $transactionAct->commit();
                    $transactionUser->commit();
                    return;
                case ConstOrderType::WITHDRAW_CASH:
                    break;
                case ConstOrderType::ACT_ENROLL:
                    //记录报名方支付流水（-）
                    Trans::model()->add(ConstTransType::PAY_ACT_ENROLL, $order->trade_no, $order->payer_id, $order->payee_id, (-1 * $order->total_amount), '支付-活动报名费');
                    //处理报名相关业务
                    $r = $this->notifyActEnroll($order, $detail);
                    if (!$r) {
                        $transactionAct->rollBack();
                        $transactionUser->rollBack();
                        return;
                    }
                    //往收款方账户入账，记录主办方报名费收取流水（+）
                    $rst = $this->modifyBalance($order->payee_id, $order->payer_id, $order->total_amount, ConstTransType::REV_ACT_ENROLL, '收入-活动报名费', $order->trade_no);
                    //手续费结算
                    $rstFee = TRUE;
                    $rstFeeO = TRUE;
                    if ($detail->payee_fee > 0) {
                        $rstFee = $this->modifyBalance($order->payee_id, 1, (-1 * $detail->payee_fee), ConstTransType::REV_ACT_ENROLL_FEE, '支出-活动报名费的手续费', $order->trade_no);
                        $rstFeeO = $this->modifyBalance(1, $order->payee_id, $detail->payee_fee, ConstTransType::FEE, '收入-活动报名费的手续费', $order->trade_no);
                    }
                    if (!$rst || !$rstFee || !$rstFeeO) {
                        $transactionAct->rollBack();
                        $transactionUser->rollBack();
                        return;
                    }
                    $transactionAct->commit();
                    $transactionUser->commit();
                    return;
                case ConstOrderType::SMS:
                    break;
                case ConstOrderType::NORMAL:
                    break;
                default:
                    break;
            }
            $transactionAct->rollBack();
            $transactionUser->rollBack();
            return;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
            $transactionAct->rollBack();
            $transactionUser->rollBack();
            return;
        }
    }
    
    
    /**
     * 根据订单号查订单
     * 
     * @param type $tradeNo 订单号
     */
    public function getByTradeNo($tradeNo)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.trade_no', $tradeNo);
        return $this->find($cr);
    }
    
    
    /**
     * 验证订单
     * 
     * @param type $order
     * @param type $totalFee
     */
    public function validOrder($order, $totalFee)
    {
        if (empty($order)) {
            return FALSE;
        }
        if ($order->total_amount != $totalFee) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    /**
     * 处理订单及支付宝记录
     * 
     * @param type $order
     * @param type $payTradeNo
     * @param type $totalAmount
     * @param type $payBuyerNo
     * @param type $gmtCreateTime
     * @param type $gmtPayment
     */
    public function dealWithOrderAndAliPay($order, $payTradeNo, $totalAmount, $payBuyerNo, $gmtCreateTime, $gmtPayment)
    {
        //写入订单支付记录
        $orderAliPay = OrderPay::model()->get($order->id, ConstPayPlatform::ALIPAY);
        $isNew = FALSE;
        if (empty($orderAliPay)) {
            $orderAliPay = new OrderPay();
            $orderAliPay->order_id = $order->id;
            $orderAliPay->pay_platform = ConstPayPlatform::ALIPAY;
            $orderAliPay->create_time = date('Y-m-d H:i:s');
            $isNew = TRUE;
        }

        $order->status = ConstOrderStatus::HAS_PAY;
        $order->modify_time = date('Y-m-d H:i:s');
        $r = $order->update();
        if (!$r) {
            return FALSE;
        }

        $orderAliPay->pay_trade_no = $payTradeNo;
        $orderAliPay->total_amount = $totalAmount;
        $orderAliPay->pay_buyer_no = $payBuyerNo;
        $orderAliPay->gmt_create_time = $gmtCreateTime;
        $orderAliPay->gmt_payment = $gmtPayment;
        $orderAliPay->modify_time = date('Y-m-d H:i:s');
        $orderAliPay->status = ConstOrderStatus::HAS_PAY;
        $r = FALSE;
        if ($isNew) {
            $r = $orderAliPay->save();
        }  else {
            $r = $orderAliPay->update();
        }
        if (!$r) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    /**
     * 处理订单及微信记录
     * 
     * @param type $order
     * @param type $payTradeNo
     * @param type $totalAmount
     * @param type $payBuyerNo
     * @param type $gmtPayment
     */
    public function dealWithOrderWxPay($order, $payTradeNo, $totalAmount, $payBuyerNo, $gmtPayment)
    {
        $orderWxPay = OrderPay::model()->get($order->id, ConstPayPlatform::WECHATPAY);
        if (empty($orderWxPay)) {
            return FALSE;
        }

        $order->status = ConstOrderStatus::HAS_PAY;
        $order->modify_time = date('Y-m-d H:i:s');
        $r = $order->update();
        if (!$r) {
            return FALSE;
        }

        $orderWxPay->pay_trade_no = $payTradeNo;
        $orderWxPay->total_amount = $totalAmount;
        $orderWxPay->pay_buyer_no = $payBuyerNo;
        $orderWxPay->gmt_payment = date('Y-m-d H:i:s', strtotime($gmtPayment));
        $orderWxPay->modify_time = date('Y-m-d H:i:s');
        $orderWxPay->status = ConstOrderStatus::HAS_PAY;
        $r = $orderWxPay->update();
        if (!$r) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    /**
     * 处理活动报名类型订单业务
     * 
     * @param type $order
     */
    public function notifyActEnroll($order, $detail) 
    {
        if (ConstOrderType::ACT_ENROLL != $order->trade_type) {
            return FALSE;
        }
        $actMore = ActInfoExtend::model()->find('t.product_id=:productId', array('productId' => $detail->product_id));
        if (empty($actMore)) {
            return FALSE;
        }
        //报名情况修改
        $enroll = ActEnroll::model()->get($actMore->act_id, $order->payer_id);
        if (empty($enroll)) {
            return FALSE;
        }
        $enroll->status = ConstCheckStatus::PASS;
        $enroll->modify_time = date('Y-m-d H:i:s');
        $r = $enroll->update();
        if (!$r) {
            return FALSE;
        }
        return TRUE;
    }
    
    
    /**
     * 修改账户余额
     * 
     * @param type $uid 用户id
     * @param type $totalFee 金额(有符号)
     * @param type $transType 流水类型
     * @param type $transDescri 流水描述
     * @param type $outOrderNo 外部订单号
     */
    public function modifyBalance($uid, $targetUid, $totalFee, $transType, $transDescri, $outOrderNo)
    {
        if (empty($uid)) {
            return FALSE;
        }
        //验证用户是否存在
        $user = UserInfo::model()->findByPk($uid);
        if (empty($user)) {
            return FALSE;
        }
        //验证结果是否合法
        $result = $user->account_balance + $totalFee;
        if ($result < 0) {
            return FALSE;
        }
        $user->account_balance = $result;
        //验证是否处理成功
        $r = $user->update();
        if (!$r) {
            return FALSE;
        }
        //记录流水
        Trans::model()->add($transType, $outOrderNo, $uid, $targetUid, $totalFee, $transDescri);
        return TRUE;
    }
    
    
    /**
     * 账单
     * 
     * @param type $uid 用户id
     * @param type $filter 默认全部0,1收入，2支出
     * @param type $type recharge,withdraw_cash，默认all
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function bills($uid, $filter, $type, $page, $size)
    {
        $cr = new CDbCriteria();
        if (0 == $filter) {
            $cru = new CDbCriteria();
            $cru->compare('t.payer_id', $uid, FALSE, 'OR');
            $cru->compare('t.payee_id', $uid, FALSE, 'OR');
            $cr->mergeWith($cru);
        }  elseif (1 == $filter) {
            $cr->compare('t.payee_id', $uid);
            $cr->compare('t.trade_type', '<>' . ConstOrderType::WITHDRAW_CASH);
        }  else {
            $cr->compare('t.payer_id', $uid);
            $cr->compare('t.trade_type', '<>' . ConstOrderType::RECHARGE);
        }
        if ('all' != $type) {
            $cr->compare('trade_type', $type);
        }
        $cr->compare('t.status', '<>' . ConstOrderStatus::DELETE);
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $bills = array();
        foreach ($rst as $v) {
            $bill = array();
            $bill['id'] = $v->id;
            $bill['trade_no'] = $v->trade_no;
            $bill['title'] = $v->subject;
            $detail = OrderDetail::model()->get($v->id);
            $payerFee = empty($detail) ? 0 : $detail->payer_fee;
            $payeeFee = empty($detail) ? 0 : $detail->payee_fee;
            if (ConstOrderType::RECHARGE == $v->trade_type) {
                $bill['total_fee'] = $v->total_amount;
                $bill['fee'] = $payerFee;
            }  elseif (ConstOrderType::WITHDRAW_CASH == $v->trade_type) {
                $bill['total_fee'] = -1 * $v->total_amount;
                $bill['fee'] = $payeeFee;
            }  else {
                if ($uid == $v->payer_id) {
                    $bill['total_fee'] = -1 * $v->total_amount;
                    $bill['fee'] = $payerFee;
                }  else {
                    $bill['total_fee'] = $v->total_amount - $payeeFee;
                    $bill['fee'] = $payeeFee;
                }
            }
            
            $bill['status'] = $v->status;
            $bill['create_time'] = $v->create_time;
            array_push($bills, $bill);
        }
        
        return array(
            'total_num' => $count,
            'bills' => $bills,
        );
    }
    
}
