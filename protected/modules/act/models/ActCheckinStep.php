<?php

/**
 * This is the model class for table "act_checkin_step".
 *
 * The followings are the available columns in table 'act_checkin_step':
 * @property string $id
 * @property string $act_id
 * @property string $subject
 * @property string $rgb_hex
 * @property integer $need_sure
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property ActCheckinUserMap[] $actCheckinUserMaps
 */
class ActCheckinStep extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_checkin_step';
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
			array('need_sure, status', 'numerical', 'integerOnly'=>true),
			array('act_id', 'length', 'max'=>10),
			array('subject', 'length', 'max'=>64),
			array('rgb_hex', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, subject, rgb_hex, need_sure, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'actCheckinUserMaps' => array(self::HAS_MANY, 'ActCheckinUserMap', 'step_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动签到环节id',
			'act_id' => '活动id',
			'subject' => '签到环节名称',
			'rgb_hex' => '颜色RGB值的十六进制',
			'need_sure' => '需要确认',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActCheckinStep the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 此签到基本数据
     * 
     * @param type $id 签到id
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
            'rgb_hex' => $model->rgb_hex,
            'need_sure' => $model->need_sure,
            'order_limit' => 1,
            'has_prize' => 0,
            'status' => $model->status,
        );
    }
    
    
    /**
     * 查看活动id
     * 
     * @param type $stepId 签到id
     */
    public function actId($stepId)
    {
        $model = $this->findByPk($stepId);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return $model->act_id;
    }
    
    
    /**
     * 下一批需要签到的信息
     * 
     * @param type $actId 活动id
     * @param type $stepId 签到id
     */
    public function nextCheckin($actId, $stepId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        if (!empty($stepId)) {
            $cr->compare('t.id', '>' . $stepId);
        }
        return $this->find($cr);
    }
    
}
