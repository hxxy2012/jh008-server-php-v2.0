<?php

/**
 * This is the model class for table "time_task".
 *
 * The followings are the available columns in table 'time_task':
 * @property string $id
 * @property integer $type
 * @property string $exc_time
 */
class TimeTask extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'time_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, exc_time', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, exc_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '定时任务id',
			'type' => '任务类型',
			'exc_time' => '执行时间',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('exc_time',$this->exc_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TimeTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加定时任务
     * @param type $type
     * @param type $excTime
     */
    public function addTimeTask($type, $excTime)
    {
        $model = $this->find('type=:type and exc_time=:excTime', array(
            ':type' => $type,
            ':excTime' => $excTime
                ));
        if (!empty($model)) {
            return;
        }
        $model = new TimeTask();
        $model->type = $type;
        $model->exc_time = $excTime;
        $model->status = ConstStatus::NORMAL;
        $model->save();
    }


    /**
     * 刷新所有需要执行的定时任务
     */
    public function refreshExcTasks()
    {
        $cr = new CDbCriteria();
        $cr->compare('exc_time', '<=' . date('Y-m-d H:i:s'));
        //-1已删除，0正常，1已执行
        $cr->compare('status', ConstStatus::NORMAL);
        $rst = $this->findAll($cr);
        
        foreach ($rst as $v) {
            switch ($v->type) {
                case ConstTimeTaskType::ACT_TIME_STATUS:
                    TimeLog::model()->timeActTimeStatus();
                    ActInfo::model()->refreshTimeStatus();
                    break;
                case ConstTimeTaskType::TAG_ACT_COUNT:
                    TimeLog::model()->timeTagCount();
                    TagInfo::model()->refreshCount();
                    break;
                case ConstTimeTaskType::INDEX_PAGE_ACT_LIST:
                    TimeLog::model()->timeIndexPageActList();
                    IndexPageActList::model()->refreshAll();
                    break;
                case ConstTimeTaskType::USER_REGIST_COUNT:
                    TimeLog::model()->timeUserRegistCount();
                    UserRegistCount::model()->refreshRegistCount();
                    break;
                case ConstTimeTaskType::PUSH_MSG:
                    TimeLog::model()->timePush();
                    PushMsgInfo::model()->refreshPushs(TRUE);
                    break;
                case ConstTimeTaskType::MSG_TO_ALL_USERS:
                    TimeLog::model()->timeMsg();
                    MsgInfo::model()->refreshMsgToAllUsers();
                    break;
                default:
                    break;
            }
            $v->status = 1;
            $v->update();
        }
    }
    
    
    /**
     * 获取还未执行的定时任务
     */
    public function getNotOver()
    {
        $cr = new CDbCriteria();
        $cr->compare('status', 0);
        $rst = $this->findAll($cr);
        $count = $this->count();
        $tasks = array();
        foreach ($rst as $v) {
            $task = array();
            $task['id'] = $v->id;
            $task['type'] = $v->type;
            $task['exc_time'] = $v->exc_time;
            $task['status'] = $v->status;
            array_push($tasks, $task);
        }
        return array(
            'total_num' => $count,
            'tasks' => $tasks,
        );
    }
    
}
