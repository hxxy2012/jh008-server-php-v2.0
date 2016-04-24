<?php

/**
 * This is the model class for table "system_msg".
 *
 * The followings are the available columns in table 'system_msg':
 * @property string $id
 * @property string $content
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property SystemMsgUserMap[] $systemMsgUserMaps
 * @property SystemMsgUserTask[] $systemMsgUserTasks
 */
class SystemMsg extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system_msg';
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
			array('status', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>240),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, content, status, create_time', 'safe', 'on'=>'search'),
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
			'systemMsgUserMaps' => array(self::HAS_MANY, 'SystemMsgUserMap', 'msg_id'),
			'systemMsgUserTasks' => array(self::HAS_MANY, 'SystemMsgUserTask', 'msg_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '系统消息id',
			'content' => '内容',
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
		$criteria->compare('content',$this->content,true);
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
	 * @return SystemMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 系统消息
     * 
     * @param type $model 系统消息数据
     * @param type $msgId 消息id
     */
    public function profile($model = NULL, $msgId)
    {
        if (empty($model)) {
            $model = $this->findByPk($msgId);
        }
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'content' => $model->content,
            'status' => $model->status,
            'create_time' => $model->create_time,
        );
    }
    
}
