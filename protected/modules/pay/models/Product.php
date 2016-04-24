<?php

/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property string $id
 * @property string $subject
 * @property string $body
 * @property double $unit_price
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 */
class Product extends PayModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('unit_price', 'numerical'),
			array('subject', 'length', 'max'=>255),
			array('body', 'length', 'max'=>512),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subject, body, unit_price, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '商品id',
			'subject' => '名称',
			'body' => '描述',
			'unit_price' => '单价',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '更新时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Product the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 价格
     * 
     * @param type $productId
     */
    public function price($productId)
    {
        if (empty($productId)) {
            return 0;
        }
        $model = $this->findByPk($productId);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return 0;
        }
        return empty($model->unit_price) ? 0 : $model->unit_price;
    }
    
    
    /**
     * 商品信息
     * 
     * @param type $id
     */
    public function profile($id) 
    {
        if (empty($id)) {
            return NULL;
        }
        $model = $this->findByPk($id);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'subject' => $model->subject,
            'body' => $model->body,
            'unit_price' => $model->unit_price,
        );
    }
    
    
    /**
     * 查找收款方id
     * 
     * @param type $id 商品id
     */
    public function getPayeeId($id)
    {
        $actModel = ActInfoExtend::model()->find('t.product_id=:id', array(':id' => $id));
        $act = ActInfo::model()->findByPk($actModel->act_id);
        if (!empty($act) && !empty($act->org_id)) {
            $orgModel = OrgInfo::model()->findByPk($act->org_id);
            if (!empty($orgModel)) {
                return $orgModel->own_id;
            }
        }
    }
    
}
