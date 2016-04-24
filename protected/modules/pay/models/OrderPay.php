<?php

/**
 * This is the model class for table "order_pay".
 *
 * The followings are the available columns in table 'order_pay':
 * @property string $id
 * @property string $order_id
 * @property integer $pay_platform
 * @property string $pre_order
 * @property string $pay_trade_no
 * @property double $total_amount
 * @property string $pay_buyer_no
 * @property string $gmt_create_time
 * @property string $gmt_payment
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property Order $order
 */
class OrderPay extends PayModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order_pay';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, create_time, modify_time', 'required'),
			array('pay_platform, status', 'numerical', 'integerOnly'=>true),
			array('total_amount', 'numerical'),
			array('order_id', 'length', 'max'=>10),
			array('pay_trade_no, pay_buyer_no', 'length', 'max'=>255),
			array('pre_order, gmt_create_time, gmt_payment', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, pay_platform, pre_order, pay_trade_no, total_amount, pay_buyer_no, gmt_create_time, gmt_payment, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '订单的支付信息id',
			'order_id' => '订单id',
			'pay_platform' => '支付平台',
			'pre_order' => '预支付信息',
			'pay_trade_no' => '支付平台交易号',
			'total_amount' => '交易总额',
			'pay_buyer_no' => '付款方平台账号',
			'gmt_create_time' => '交易创建时间',
			'gmt_payment' => '交易付款时间',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '更新时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderPay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 查询微信预支付信息
     * 
     * @param type $orderId 订单id
     */
    public function wxpayPreInfo($orderId)
    {
        $model = $this->find('t.order_id=:orderId and status=:status and pay_platform=:payPlatform', 
                array(
                    ':orderId' => $orderId,
                    ':status' => ConstStatus::NORMAL,
                    ':payPlatform' => ConstPayPlatform::WECHATPAY,
                )
                );
        if (empty($model)) {
            return NULL;
        }
        return array(
            'wx' => empty($model->pre_order) ? NULL : json_decode($model->pre_order),
        );
    }
    
    
    /**
     * 写入微信预支付信息
     * 
     * @param type $orderId 订单id
     * @param array $pre 预支付信息
     */
    public function upWxpayPreInfo($orderId, array $pre) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.order_id', $orderId);
        $cr->compare('t.pay_platform', ConstPayPlatform::WECHATPAY);
        $model = $this->find($cr);
        
        $isNew = FALSE;
        if (empty($model)) {
            $model = new OrderPay();
            $model->order_id = $orderId;
            $model->pay_platform = ConstPayPlatform::WECHATPAY;
            $model->create_time = date('Y-m-d H:i:s');
            $isNew = TRUE;
        }
        $model->status = ConstOrderStatus::WAIT_PAY;
        $model->gmt_create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        $model->pre_order = json_encode($pre);
        if ($isNew) {
            return $model->save();
        }
        return $model->update();
    }
    
    
    /**
     * 根据订单id和支付平台获取支付信息
     * 
     * @param type $orderId 订单id
     * @param type $payPlatform 支付平台
     */
    public function get($orderId, $payPlatform)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.order_id', $orderId);
        $cr->compare('t.pay_platform', $payPlatform);
        return $this->find($cr);
    }
    
}
