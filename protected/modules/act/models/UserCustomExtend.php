<?php

/**
 * This is the model class for table "user_custom_extend".
 *
 * The followings are the available columns in table 'user_custom_extend':
 * @property string $id
 * @property string $subject
 * @property string $hint
 * @property string $descri
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property CustomExtActMap[] $customExtActMaps
 * @property CustomExtActUserVal[] $customExtActUserVals
 * @property CustomExtBusiMap[] $customExtBusiMaps
 * @property CustomExtUserVal[] $customExtUserVals
 */
class UserCustomExtend extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_custom_extend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subject, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('subject, hint, descri', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subject, hint, descri, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'customExtActMaps' => array(self::HAS_MANY, 'CustomExtActMap', 'c_id'),
			//'customExtActUserVals' => array(self::HAS_MANY, 'CustomExtActUserVal', 'c_id'),
			//'customExtBusiMaps' => array(self::HAS_MANY, 'CustomExtBusiMap', 'c_id'),
			//'customExtUserVals' => array(self::HAS_MANY, 'CustomExtUserVal', 'c_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户自定义扩展信息列',
			'subject' => '名称',
			'hint' => '输入描述',
			'descri' => '描述',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserCustomExtend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 自定义字段信息
     * 
     * @param type $id
     * @param type $model
     */
    public function profile($id, $model = NULL) 
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'subject' => $model->subject,
            'hint' => $model->hint,
            'descri' => $model->descri,
        );
        
    }
    
}
