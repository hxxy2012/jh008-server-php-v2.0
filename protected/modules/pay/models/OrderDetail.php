<?php

/**
 * This is the model class for table "order_detail".
 *
 * The followings are the available columns in table 'order_detail':
 * @property string $id
 * @property string $order_id
 * @property string $product_id
 * @property double $unit_price
 * @property integer $number
 * @property double $payer_fee
 * @property double $payee_fee
 * @property double $total_amount
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property Order $order
 * @property Product $product
 */
class OrderDetail extends PayModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, unit_price, number, total_amount, create_time, modify_time', 'required'),
			array('number', 'numerical', 'integerOnly'=>true),
			array('unit_price, payer_fee, payee_fee, total_amount', 'numerical'),
			array('order_id, product_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, product_id, unit_price, number, payer_fee, payee_fee, total_amount, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '订单明细id',
			'order_id' => '订单id',
			'product_id' => '商品id',
			'unit_price' => '单价',
			'number' => '数量',
			'payer_fee' => '付款方手续费',
			'payee_fee' => '收款方手续费',
			'total_amount' => '总金额',
			'create_time' => '创建时间',
			'modify_time' => '更新时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 订单详情信息
     * 
     * @param type $orderId 订单id
     */
    public function profile($orderId)
    {
        $model = $this->find('t.order_id', array('order_id' => $orderId));
        //product_id    unit_price   number
        if (empty($model)) {
            return NULL;
        }
        
        return array(
            'product_id' => $model->product_id,
            'unit_price' => $model->unit_price,
            'number' => $model->number,
        );
    }
    
    
    /**
     * 获取model
     * 
     * @param type $orderId 订单号
     */
    public function get($orderId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.order_id', $orderId);
        return $this->find($cr);
    }
    
}
