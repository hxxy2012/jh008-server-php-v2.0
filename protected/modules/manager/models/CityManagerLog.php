<?php

/**
 * This is the model class for table "city_manager_log".
 *
 * The followings are the available columns in table 'city_manager_log':
 * @property string $id
 * @property string $m_id
 * @property string $model_class
 * @property string $model_behavior
 * @property integer $model_pk
 * @property string $model_attributes_old
 * @property string $model_attributes_new
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property CityManager $m
 */
class CityManagerLog extends ManagerModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'city_manager_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('model_pk, status', 'numerical', 'integerOnly'=>true),
			array('m_id', 'length', 'max'=>10),
			array('model_class', 'length', 'max'=>50),
			array('model_behavior', 'length', 'max'=>16),
			array('model_attributes_old, model_attributes_new', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, m_id, model_class, model_behavior, model_pk, model_attributes_old, model_attributes_new, status, create_time', 'safe', 'on'=>'search'),
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
			'm' => array(self::BELONGS_TO, 'CityManager', 'm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '管理员日志id',
			'm_id' => '管理员id',
			'model_class' => '数据表',
			'model_behavior' => '操作',
			'model_pk' => '主键',
			'model_attributes_old' => '旧数据',
			'model_attributes_new' => '新数据',
			'status' => '状态',
			'create_time' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('m_id',$this->m_id,true);
		$criteria->compare('model_class',$this->model_class,true);
		$criteria->compare('model_behavior',$this->model_behavior,true);
		$criteria->compare('model_pk',$this->model_pk);
		$criteria->compare('model_attributes_old',$this->model_attributes_old,true);
		$criteria->compare('model_attributes_new',$this->model_attributes_new,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CityManagerLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
