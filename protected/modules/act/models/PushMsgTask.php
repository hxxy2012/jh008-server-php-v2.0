<?php

/**
 * This is the model class for table "push_msg_task".
 *
 * The followings are the available columns in table 'push_msg_task':
 * @property string $id
 * @property string $msg_id
 * @property string $to_type
 * @property string $to_with_id
 * @property string $ori_u_id
 * @property string $last_max_user_id
 * @property integer $status
 * @property string $begin_time
 * @property string $end_time
 * @property integer $sum
 *
 * The followings are the available model relations:
 * @property PushMsg $msg
 * @property UserInfo $lastMaxUser
 */
class PushMsgTask extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'push_msg_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('msg_id, to_type', 'required'),
			array('status, sum', 'numerical', 'integerOnly'=>true),
			array('msg_id, to_type, to_with_id, ori_u_id, last_max_user_id', 'length', 'max'=>10),
			array('begin_time, end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, msg_id, to_type, to_with_id, ori_u_id, last_max_user_id, status, begin_time, end_time, sum', 'safe', 'on'=>'search'),
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
			'msg' => array(self::BELONGS_TO, 'PushMsg', 'msg_id'),
			'lastMaxUser' => array(self::BELONGS_TO, 'UserInfo', 'last_max_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '推送消息发送任务',
			'msg_id' => '推送消息id',
			'to_type' => '发送类型',
			'to_with_id' => '发送类型相关id',
			'ori_u_id' => '源用户id',
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
		$criteria->compare('to_type',$this->to_type,true);
		$criteria->compare('to_with_id',$this->to_with_id,true);
		$criteria->compare('ori_u_id',$this->ori_u_id,true);
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
	 * @return PushMsgTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加push消息发送任务
     */
    public function add($toType, $toWithId, $oriUid, $title, $descri, array $customKv)
    {
        $msgModel = new PushMsg();
        $rst = PushMsg::model()->add($msgModel, $title, $descri, $customKv);
        if (!$rst) {
            return FALSE;
        }
        
        $model = new PushMsgTask();
        $model->msg_id = $msgModel->id;
        $model->to_type = $toType;
        $model->to_with_id = $toWithId;
        $model->ori_u_id = $oriUid;
        $model->last_max_user_id = NULL;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 刷新所有任务
     */
    public function refreshPushMsgTask()
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
            $this->pushMsgToUser($task, $nextUser['id']);
        }
        $task->status = 1;
        $task->end_time = date('Y-m-d H:i:s');
        $task->update();
    }
    
    
    /**
     * 是否存在未推送的用户
     * 
     * @param type $task 任务
     */
    private function isExistUser($task)
    {
        switch ($task->to_type) {
            case ConstPushMsgTaskType::TO_All:
                $this->sendMsgToAll($task, ConstPushMsgPlatform::TO_ALL, NULL);
                break;
            case ConstPushMsgTaskType::TO_USER:
                $this->sendMsgToUser($task, $task->to_with_id, NULL);
                break;
            case ConstPushMsgTaskType::TO_ANDROID:
                $this->sendMsgToAll($task, ConstPushMsgPlatform::TO_ANDROID, NULL);
                break;
            case ConstPushMsgTaskType::TO_IOS:
                $this->sendMsgToAll($task, ConstPushMsgPlatform::TO_IOS, NULL);
                break;
            case ConstPushMsgTaskType::TO_USER_FANS:
                $nextFans = UserFans::model()->lastFans($task->to_with_id, empty($task->last_max_user_id) ? 0 : $task->last_max_user_id);
                return empty($nextFans) ? FALSE : $nextFans;
            case ConstPushMsgTaskType::TO_ACT_CITY_ALL:
                break;
            case ConstPushMsgTaskType::TO_ACT_ENROLL_USERS:
                $nextEnroll = ActEnroll::model()->getNextEnrollUser($task->to_with_id, empty($task->last_max_user_id) ? 0 : $task->last_max_user_id);
                if (!$nextEnroll) {
                    return FALSE;
                }
                $user = UserInfo::model()->findByPk($nextEnroll['u_id']);
                return empty($user) ? FALSE : $user;
            case ConstPushMsgTaskType::TO_ACT_CHECKIN_USERS:
                break;
            case ConstPushMsgTaskType::TO_NEWS_CITY_ALL:
                $news = NewsInfo::model()->findByPk($task->to_with_id);
                if (empty($news)) {
                    break;
                }
                $cr = new CDbCriteria();
                $cr->compare('t.status', ConstStatus::NORMAL);
                $cr->compare('t.id', '>' . empty($task->last_max_user_id) ? 0 : $task->last_max_user_id);
                //$cr->compare('t.city_id', $news->city_id);
                $nextUser = UserInfo::model()->find($cr);
                return empty($nextUser) ? FALSE : $nextUser;
            case ConstPushMsgTaskType::TO_DYNAMIC_USERS:
                break;
            case ConstPushMsgTaskType::TO_DYNAMIC_COMMENT_USERS:
                $dynamicComment = DynamicComment::model()->findByPk($task->to_with_id);
                if (empty($dynamicComment)) {
                    break;
                }
                $dynamic = UserDynamic::model()->findByPk($dynamicComment->dynamic_id);
                if (empty($dynamic) || empty($dynamic->author_id)) {
                    break;
                }
                //先验证是否推送给动态的发布者
                if (empty($task->last_max_user_id)) {
                    if ($task->ori_id != $dynamic->author_id) {
                        $nextUser = UserInfo::model()->find($dynamic->author_id);
                        return empty($nextUser) ? FALSE : $nextUser;
                    }
                }
                //验证是否推送给动态评论的回复者
                if (empty($dynamicComment->at_id)) {
                    break;
                }
                if ($task->ori_id == $dynamicComment->at_id) {
                    break;
                }
                $nextUser = UserInfo::model()->find($dynamicComment->at_id);
                return empty($nextUser) ? FALSE : $nextUser;
            default:
                break;
        }
        return FALSE;
    }
    
    
    /**
     * 给对应用户推送消息
     * 
     * @param type $task 任务
     * @param type $uid 用户id
     */
    public function sendMsgToUser($task, $uid)
    {
        $model = PushMsg::model()->findByPk($task->msg_id);
        $user = UserInfoExtend::model()->get($uid);
        if (empty($model) || empty($user) || empty($user->last_login_platform) || empty($user->baidu_user_id) || empty($user->baidu_channel_id)) {
            return FALSE;
        }
        
        Yii::app()->baiduPush->pushMsg(
                $user->last_login_platform, 
                ConstPushMsgType::TO_USER, 
                $user->baidu_user_id, 
                $user->baidu_channel_id, 
                NULL, 
                $model->title, 
                $model->descri, 
                $model->custom_kv, 
                $task->msg_id);
        
        //标注现在已经执行的进度
        $task->last_max_fans_id = $uid;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
    
    /**
     * 给用户群推送消息
     * 
     * @param type $task 任务
     * @param type $pushMsgPlatform 平台
     * @param type $customKv 自定义
     */
    public function sendMsgToAll($task, $pushMsgPlatform, $customKv)
    {
        Yii::app()->baiduPush->pushMsg(
                $pushMsgPlatform, 
                ConstPushMsgType::TO_All, 
                NULL, 
                NULL, 
                NULL, 
                $task->title, 
                $task->descri, 
                $customKv, 
                $task->msg_id);
        
        //标注现在已经执行的进度
        $task->last_max_fans_id = 0;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
}
