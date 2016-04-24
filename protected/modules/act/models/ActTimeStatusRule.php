<?php

/**
 * This is the model class for table "act_time_status_rule".
 *
 * The followings are the available columns in table 'act_time_status_rule':
 * @property string $id
 * @property string $act_id
 * @property string $type
 * @property string $filter
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 */
class ActTimeStatusRule extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_time_status_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, type', 'length', 'max'=>10),
			array('filter', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, type, filter, status', 'safe', 'on'=>'search'),
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
			'id' => '活动时间状态规则id',
			'act_id' => '活动id',
			'type' => '类型：1每周',
			'filter' => '规则表',
			'status' => '状态',
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
		$criteria->compare('act_id',$this->act_id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('filter',$this->filter,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActTimeStatusRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加每周规则
     */
    public function addWeek($actId, $weekArr)
    {
        $model = $this->find('act_id=:actId and type=:type', array(
            ':actId' => $actId,
            ':type' => 1,
        ));
        if (empty($model)) {
            $model = new ActTimeStatusRule();
            $model->act_id = $actId;
            $model->type = 1;
            $model->filter = json_encode($weekArr);
            $model->status = ConstStatus::NORMAL;
            return $model->save();
        }  else {
            $model->filter = json_encode($weekArr);
            $model->status = ConstStatus::NORMAL;
            return $model->update();
        }
    }
    
    
    /**
     * 取消新规则
     * @param type $actId
     * @return type
     */
    public function delWeek($actId) {
        $model = $this->find('act_id=:actId and type=:type', array(
            ':actId' => $actId,
            ':type' => 1,
        ));
        if (!empty($model)) {
            $model->filter = NULL;
            $model->status = ConstStatus::DELETE;
            return $model->update();
        }
    }


    /**
     * 找回活动的每周规则
     * @param type $actId
     */
    public function findWeek($actId)
    {
        return $this->find('act_id=:actId and type=:type and status=:status', array(
            ':actId' => $actId,
            ':type' => 1,
            ':status' => ConstStatus::NORMAL
        ));
    }
    
}
