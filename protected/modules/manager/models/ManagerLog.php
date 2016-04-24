<?php

/**
 * This is the model class for table "manager_log".
 *
 * The followings are the available columns in table 'manager_log':
 * @property string $id
 * @property string $m_id
 * @property string $model_class
 * @property string $model_behavior
 * @property integer $model_pk
 * @property string $model_attributes_old
 * @property string $model_attributes_new
 * @property string $create_time
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ManagerInfo $m
 */
class ManagerLog extends ManagerModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'manager_log';
	}

    public function behaviors()
    {
        return array();
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
			array('id, m_id, model_class, model_behavior, model_pk, model_attributes_old, model_attributes_new, create_time, status', 'safe', 'on'=>'search'),
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
			'm' => array(self::BELONGS_TO, 'ManagerInfo', 'm_id'),
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
			'create_time' => '创建时间',
			'status' => '状态',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ManagerLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
