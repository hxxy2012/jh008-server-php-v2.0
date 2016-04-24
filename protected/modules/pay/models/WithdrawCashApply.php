<?php

/**
 * This is the model class for table "withdraw_cash_apply".
 *
 * The followings are the available columns in table 'withdraw_cash_apply':
 * @property string $id
 * @property string $order_id
 * @property double $total_fee
 * @property string $way
 * @property string $out_account
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property Order $order
 */
class WithdrawCashApply extends PayModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'withdraw_cash_apply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, total_fee, way, out_account, create_time, modify_time', 'required'),
			array('total_fee', 'numerical'),
			array('order_id', 'length', 'max'=>10),
			array('way', 'length', 'max'=>255),
			array('out_account', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, total_fee, way, out_account, create_time, modify_time', 'safe', 'on'=>'search'),
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
			'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '提现申请记录id',
			'order_id' => '订单id',
			'total_fee' => '金额',
			'way' => '途径',
			'out_account' => '外部账号',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WithdrawCashApply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 提现信息
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
