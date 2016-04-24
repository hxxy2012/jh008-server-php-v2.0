<?php

/**
 * This is the model class for table "friend_dynamic_task".
 *
 * The followings are the available columns in table 'friend_dynamic_task':
 * @property string $id
 * @property string $dynamic_id
 * @property string $dynamic_time
 * @property string $last_max_fans_id
 * @property integer $status
 * @property string $begin_time
 * @property string $end_time
 * @property integer $sum
 *
 * The followings are the available model relations:
 * @property UserDynamic $dynamic
 * @property UserInfo $lastMaxFans
 */
class FriendDynamicTask extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'friend_dynamic_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dynamic_id, dynamic_time', 'required'),
			array('status, sum', 'numerical', 'integerOnly'=>true),
			array('dynamic_id, last_max_fans_id', 'length', 'max'=>10),
			array('begin_time, end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, dynamic_id, dynamic_time, last_max_fans_id, status, begin_time, end_time, sum', 'safe', 'on'=>'search'),
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
			'dynamic' => array(self::BELONGS_TO, 'UserDynamic', 'dynamic_id'),
			'lastMaxFans' => array(self::BELONGS_TO, 'UserInfo', 'last_max_fans_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '好友动态任务关联id',
			'dynamic_id' => '动态id',
			'dynamic_time' => 'Dynamic Time',
			'last_max_fans_id' => 'Last Max Fans',
			'status' => 'Status',
			'begin_time' => 'Begin Time',
			'end_time' => 'End Time',
			'sum' => 'Sum',
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
		$criteria->compare('dynamic_id',$this->dynamic_id,true);
		$criteria->compare('dynamic_time',$this->dynamic_time,true);
		$criteria->compare('last_max_fans_id',$this->last_max_fans_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('begin_time',$this->begin_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('sum',$this->sum);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FriendDynamicTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加好友动态任务
     * 
     * @param type $dynamic_id
     * @param type $dynamic_time
     */
    public function add($dynamic_id, $dynamic_time)
    {
        $cr = new CDbCriteria();
        $cr->compare('dynamic_id', $dynamic_id);
        $model = $this->find($cr);
        if (!empty($model)) {
            return FALSE;
        }
        
        $model = new FriendDynamicTask();
        $model->dynamic_id = $dynamic_id;
        $model->dynamic_time = $dynamic_time;
        $model->last_max_fans_id = NULL;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 刷新所有任务
     */
    public function refreshDynamicToFansTask()
    {
        while ($task = $this->isExistTask()) {
            if (empty($task->last_max_fans_id) && empty($task->begin_time) && ConstStatus::NORMAL == $task->status) {
                $task->begin_time = date('Y-m-d H:i:s');
            }

            $dynamic = UserDynamic::model()->dynamic($task->dynamic_id);
            //动态已被删除，对应任务也删除
            if (empty($dynamic)) {
                $task->status = ConstStatus::DELETE;
                $task->update();
                continue;
            }
            //发送者已不存在，对应任务也删除
            $authorUser = UserInfo::model()->profile(NULL, $dynamic['author_id']);
            if (empty($authorUser)) {
                $task->status = ConstStatus::DELETE;
                $task->update();
                continue;
            }
            
            $this->runTask($task, $authorUser['id']);
        }
    }

    
    /**
     * 是否存在未执行的任务
     */
    private function isExistTask()
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->order = 't.id asc';
        
        $task = $this->find($cr);
        return empty($task) ? FALSE : $task;
    }
    
    
    /**
     * 执行任务
     * 
     * @param type $task 任务
     */
    private function runTask($task, $authorUserId)
    {
        while ($nextFans = $this->isExistFans($task, $authorUserId)) {
            $this->addDynamicWithFans($task, $nextFans['id']);
        }
        $task->status = 1;
        $task->end_time = date('Y-m-d H:i:s');
        $task->update();
    }
    
    
    /**
     * 是否存在未添加的粉丝
     * 
     * @param type $task 任务
     */
    private function isExistFans($task, $authorUserId)
    {
        $nextFans = UserFans::model()->lastFans($authorUserId, empty($task->last_max_fans_id) ? 0 : $task->last_max_fans_id);
        
        return empty($nextFans) ? FALSE : $nextFans;
    }
    
    
    /**
     * 给对应粉丝添加动态关联
     * 
     * @param type $task 任务
     * @param type $user 对应粉丝
     */
    public function addDynamicWithFans($task, $nextFansId)
    {
        //向粉丝写入动态关联
        FriendDynamic::model()->add(NULL, $nextFansId, $task->dynamic_id);
        
        //标注现在已经执行的进度
        $task->last_max_fans_id = $nextFansId;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
}
