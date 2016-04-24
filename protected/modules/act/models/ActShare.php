<?php

/**
 * This is the model class for table "act_share".
 *
 * The followings are the available columns in table 'act_share':
 * @property string $id
 * @property string $act_id
 * @property string $u_id
 * @property string $share_type
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property UserInfo $u
 */
class ActShare extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_share';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, create_time', 'required'),
			array('act_id, u_id, share_type', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, u_id, share_type, create_time', 'safe', 'on'=>'search'),
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
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动分享关联id',
			'act_id' => '活动id',
			'u_id' => '分享者用户id',
			'share_type' => '分享类型：1微信，2朋友圈，3新浪微博，4qq',
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
		$criteria->compare('act_id',$this->act_id,true);
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('share_type',$this->share_type,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActShare the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取活动被分享次数
     * 
     * @param type $actId 活动id
     */
    public function sharedNum($actId) 
    {
        return $this->count('act_id=:actId', array(':actId' => $actId));
    }
    
    
    /**
     * 添加分享
     * @param type $actId 活动id
     * @param type $uid 用户id
     * @param type $shareType 分享类型
     */
    public function addShare($actId, $uid = NULL, $shareType = NULL)
    {
        $model = new ActShare();
        $model->act_id = $actId;
        $model->u_id = $uid;
        $model->share_type = $shareType;
        $model->create_time = date("Y-m-d H:i:s", time());
        return $model->save();
    }
    
}
