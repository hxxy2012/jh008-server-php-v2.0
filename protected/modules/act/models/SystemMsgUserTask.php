<?php

/**
 * This is the model class for table "system_msg_user_task".
 *
 * The followings are the available columns in table 'system_msg_user_task':
 * @property string $id
 * @property string $msg_id
 * @property string $user_ids
 * @property string $last_max_user_id
 * @property integer $status
 * @property string $begin_time
 * @property string $end_time
 * @property integer $sum
 *
 * The followings are the available model relations:
 * @property SystemMsg $msg
 * @property UserInfo $lastMaxUser
 */
class SystemMsgUserTask extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system_msg_user_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('msg_id, user_ids', 'required'),
			array('status, sum', 'numerical', 'integerOnly'=>true),
			array('msg_id, last_max_user_id', 'length', 'max'=>10),
			array('begin_time, end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, msg_id, user_ids, last_max_user_id, status, begin_time, end_time, sum', 'safe', 'on'=>'search'),
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
			'msg' => array(self::BELONGS_TO, 'SystemMsg', 'msg_id'),
			'lastMaxUser' => array(self::BELONGS_TO, 'UserInfo', 'last_max_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '系统消息发送任务',
			'msg_id' => '系统消息id',
			'user_ids' => '用户id',
			'last_max_user_id' => '最后最大用户id',
			'status' => '状态',
			'begin_time' => '开始时间',
			'end_time' => '结束时间',
			'sum' => '总数',
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
		$criteria->compare('msg_id',$this->msg_id,true);
		$criteria->compare('user_ids',$this->user_ids,true);
		$criteria->compare('last_max_user_id',$this->last_max_user_id,true);
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
	 * @return SystemMsgUserTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 刷新所有任务
     */
    public function refreshSystemMsgUserTask()
    {
        while ($task = $this->isExistTask()) {
            if (empty($task->last_max_user_id) && empty($task->begin_time) && ConstStatus::NORMAL == $task->status) {
                $task->begin_time = date('Y-m-d H:i:s');
            }
            $this->runTask($task);
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
    private function runTask($task)
    {
        while ($nextUser = $this->isExistUser($task)) {
            $this->sendMsgToUser($task, $nextUser['id']);
        }
        $task->status = 1;
        $task->end_time = date('Y-m-d H:i:s');
        $task->update();
    }
    
    
    /**
     * 是否存在未接收到系统消息的用户
     * 
     * @param type $task 任务
     */
    private function isExistUser($task)
    {
        $needRunUserIds = json_decode($task->user_ids);
        if (empty($needRunUserIds)) {
            return FALSE;
        }
        $cr = new CDbCriteria();
        $cr->compare('t.id', $needRunUserIds);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->compare('t.id', '>' . (empty($task->last_max_user_id) ? 0 : $task->last_max_user_id));
        $cr->order = 't.id asc';
        $nextUser = UserInfo::model()->find($cr);
        //$nextUser = UserInfo::model()->find('t.status=0 and t.id>:lastMaxUserId', array(
        //    ':lastMaxUserId' => empty($task->last_max_user_id) ? 0 : $task->last_max_user_id,
        //));
        return empty($nextUser) ? FALSE : $nextUser;
    }
    
    
    /**
     * 给对应用户添加系统消息
     * 
     * @param type $task 任务
     * @param type $uid 用户id
     */
    public function sendMsgToUser($task, $uid)
    {
        SystemMsgUserMap::model()->add($task->msg_id, $uid);
        //标注现在已经执行的进度
        $task->last_max_user_id = $uid;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
}
