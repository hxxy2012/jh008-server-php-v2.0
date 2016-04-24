<?php

/**
 * This is the model class for table "org_log".
 *
 * The followings are the available columns in table 'org_log':
 * @property string $id
 * @property string $u_id
 * @property string $model_class
 * @property string $model_behavior
 * @property integer $model_pk
 * @property string $model_attributes_old
 * @property string $model_attributes_new
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 */
class OrgLog extends OrgModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'org_log';
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
			array('u_id', 'length', 'max'=>10),
			array('model_class', 'length', 'max'=>50),
			array('model_behavior', 'length', 'max'=>16),
			array('model_attributes_old, model_attributes_new', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, model_class, model_behavior, model_pk, model_attributes_old, model_attributes_new, status, create_time', 'safe', 'on'=>'search'),
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
			'id' => '社团web日志id',
			'u_id' => '用户id',
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
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrgLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
