<?php

/**
 * This is the model class for table "act_group".
 *
 * The followings are the available columns in table 'act_group':
 * @property string $id
 * @property string $act_id
 * @property string $name
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 */
class ActGroup extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, name, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'act' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动分组id',
			'act_id' => '活动id',
			'name' => '分组名称',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 分组信息
     * 
     * @param type $groupId 分组id
     * @param type $model 分组model
     */
    public function profile($groupId, $model = NULL) 
    {
        if (empty($model)) {
            $model = $this->findByPk($groupId);
        }
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'subject' => $model->name,
            'create_time' => $model->create_time,
            'people_num' => ActEnroll::model()->countGroupUserNum($model->id),
        );
    }
    
}
