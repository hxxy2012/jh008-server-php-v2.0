<?php

/**
 * This is the model class for table "push_msg".
 *
 * The followings are the available columns in table 'push_msg':
 * @property string $id
 * @property string $title
 * @property string $descri
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property PushMsgTask[] $pushMsgTasks
 */
class PushMsg extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'push_msg';
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
			array('title', 'length', 'max'=>12),
			array('descri', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, descri, status, create_time', 'safe', 'on'=>'search'),
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
			'pushMsgTasks' => array(self::HAS_MANY, 'PushMsgTask', 'msg_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '推送消息id',
			'title' => '标题',
			'descri' => '内容',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('descri',$this->descri,true);
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
	 * @return PushMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
             
    
    /**
     * 添加推送消息
     * 
     * @param type $model 推送消息数据
     * @param type $title 标题
     * @param type $descri 描述
     */
    public function add($model = NULL, $title = NULL, $descri = NULL, array $customKv) 
    {
        if (empty($model)) {
            $model = new PushMsg();
        }
        $model->title = $title;
        $model->descri = $descri;
        if (empty($customKv)) {
            $customKv = array();
        }
        $model->custom_kv = json_encode($customKv);
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}
