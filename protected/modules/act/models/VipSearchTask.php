<?php

/**
 * This is the model class for table "vip_search_task".
 *
 * The followings are the available columns in table 'vip_search_task':
 * @property string $id
 * @property string $last_max_user_id
 * @property integer $status
 * @property string $begin_time
 * @property string $end_time
 * @property integer $sum
 *
 * The followings are the available model relations:
 * @property UserInfo $lastMaxUser
 */
class VipSearchTask extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vip_search_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, sum', 'numerical', 'integerOnly'=>true),
			array('last_max_user_id', 'length', 'max'=>10),
			array('begin_time, end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, last_max_user_id, status, begin_time, end_time, sum', 'safe', 'on'=>'search'),
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
			'lastMaxUser' => array(self::BELONGS_TO, 'UserInfo', 'last_max_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '好友动态任务关联id',
			'last_max_user_id' => '最后处理的最大的用户id',
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
	 * @return VipSearchTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加vip搜索项完善任务
     */
    public function add()
    {
        //如果还存在未执行完的任务则不添加
        if ($this->isExistTask()) {
            return FALSE;
        }
        
        $model = new VipSearchTask();
        $model->last_max_user_id = NULL;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 刷新所有任务
     */
    public function refreshVipSearchTask()
    {
        $this->add();
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
            $this->updateUserSearchInfo($task, $nextUser['id']);
        }
        $task->status = 1;
        $task->end_time = date('Y-m-d H:i:s');
        $task->update();
    }
    
    
    /**
     * 是否存在未更新的用户信息
     * 
     * @param type $task 任务
     */
    private function isExistUser($task)
    {
        $nextUser = UserInfo::model()->find('t.status=0 and t.id>:lastMaxUserId', array(
            ':lastMaxUserId' => empty($task->last_max_user_id) ? 0 : $task->last_max_user_id,
        ));
        
        return empty($nextUser) ? FALSE : $nextUser;
    }
    
    
    /**
     * 给对应达人更新搜索信息
     * 
     * @param type $task 任务
     * @param type $uid 用户id
     */
    public function updateUserSearchInfo($task, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.vip_id', $uid);
        $model = VipSearch::model()->find($cr);
        
        //如果没有将此用户设置为达人的城市，则不需要达人搜索信息
        $cityIds = UserCityMap::model()->cityIds($uid);
        if (empty($cityIds)) {
            if (!empty($model) && ConstStatus::DELETE != $model->status) {
                $model->status = ConstStatus::DELETE;
                $model->update();
            }
            
            //标注现在已经执行的进度
            $task->last_max_user_id = $uid;
            $task->sum = $task->sum + 1;
            return $task->update();
        }
        
        if (empty($model)) {
            $model = new VipSearch();
            $model->vip_id = $uid;
            $model->status = ConstStatus::DELETE;
            $model->modify_time = date('Y-m-d H:i:s');
            $model->save();
        }
        
        //城市搜索项city_k
        $city_k = 0;
        foreach ($cityIds as $v) {
            $city_k = $city_k | (BinaryTool::setOne(0, $v));
        }
        $model->city_k = $city_k;
        //类别标签搜索项act_tag_k
        $tag_k = 0;
        $tagIds = UserVipTagMap::model()->tagIds($uid);
        foreach ($tagIds as $v) {
            $tag_k = $tag_k | (BinaryTool::setOne(0, $v));
        }
        $model->tag_k = $tag_k;
        //性别搜索项sex_k
        $extendInfo = UserInfoExtend::model()->profile($uid);
        $model->sex_k = $extendInfo['sex'];
        //达人标签搜索项user_tag_k
        $user_tag_k = 0;
        $userTagIds = UserTagMap::model()->tagIds($uid);
        foreach ($userTagIds as $v) {
            $user_tag_k = $user_tag_k | (BinaryTool::setOne(0, $v));
        }
        $model->user_tag_k = $user_tag_k;
        //关键字搜索项key_words_k
        $model->key_words_k = $extendInfo['nick_name'];
        $model->status = ConstStatus::NORMAL;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->update();
        
        //标注现在已经执行的进度
        $task->last_max_user_id = $uid;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
}
