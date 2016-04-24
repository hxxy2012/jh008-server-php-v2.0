<?php

/**
 * This is the model class for table "pay_order".
 *
 * The followings are the available columns in table 'pay_order':
 * @property string $id
 * @property string $trade_no
 * @property string $subject
 * @property string $body
 * @property double $total_fee
 * @property integer $status
 * @property string $u_id
 * @property integer $pay_platform
 * @property string $pay_trade_no
 * @property string $pay_buyer_no
 * @property string $gmt_create_time
 * @property string $gmt_payment
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 */
class PayOrder extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pay_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, gmt_create_time', 'required'),
			array('status, pay_platform', 'numerical', 'integerOnly'=>true),
			array('total_fee', 'numerical'),
			array('trade_no, pay_trade_no, pay_buyer_no', 'length', 'max'=>64),
			array('subject', 'length', 'max'=>128),
			array('body', 'length', 'max'=>512),
			array('u_id', 'length', 'max'=>10),
			array('gmt_payment', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, trade_no, subject, body, total_fee, status, u_id, pay_platform, pay_trade_no, pay_buyer_no, gmt_create_time, gmt_payment', 'safe', 'on'=>'search'),
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
			'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '支付订单id',
			'trade_no' => '平台订单号',
			'subject' => '商品名称',
			'body' => '商品描述',
			'total_fee' => '交易金额',
			'status' => '交易状态：-1删除，0未付款，1成功付款，2付款异常，3已退款',
			'u_id' => '用户id',
			'pay_platform' => '支付平台：1支付宝，2微信',
			'pay_trade_no' => '支付平台交易号',
			'pay_buyer_no' => '付款方账号',
			'gmt_create_time' => '交易创建时间',
			'gmt_payment' => '交易付款时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PayOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 创建订单
     * 
     * @param type $model 数据
     * @param type $uid 用户id
     * @param type $totalFee 总价
     * @param type $subject 商品名称
     * @param type $body 商品描述
     */
    public function add($model = NULL, $uid = NULL, $totalFee = NULL, $subject = NULL, $body = NULL)
    {
        if (empty($model)) {
            $model = new PayOrder();
        }
        if (!empty($uid)) {
            $model->u_id = $uid;
        }
        if (!empty($totalFee)) {
            $model->total_fee = $totalFee;
        }
        if (!empty($subject)) {
            $model->subject = $subject;
        }
        if (!empty($body)) {
            $model->body = $body;
        }
        $model->trade_no = Yii::app()->pay->buildUniqueNo();
        $model->gmt_create_time = date('Y-m-d H:i:s');
        $model->status = 0;
        return $model->save();
    }

    //已删除-1
    //const DELETE = -1;
    //未支付
    //const NOT_PAY = 0;
    //支付成功
    //const PAY_SUCCESS = 1;
    //支付异常
    //const PAY_EXCEPTION = 2;
    //已退款
    //const PAY_REFUND = 3;
    
    /**
     * 支付成功
     * 
     * @param type $tradeNo
     * @param type $totalFee
     * @param type $payPlatform
     * @param type $payTradeNo
     * @param type $payBuyerNo
     */
    public function pay($tradeNo, $totalFee, $payPlatform, $payTradeNo, $payBuyerNo, $gmtPayment) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.trade_no', $tradeNo);
        $cr->compare('t.status', 0);
        $order = $this->find($cr);
        if (empty($order)) {
            return TRUE;
        }
        if ($order->total_fee != $totalFee) {
            $order->status = 2;
            return $order->update();
        }
        $order->pay_platform = $payPlatform;
        $order->pay_trade_no = $payTradeNo;
        $order->pay_buyer_no = $payBuyerNo;
        $order->gmtPayment = $gmtPayment;
        $order->status = 1;
        return $order->update();
    }
    
}
