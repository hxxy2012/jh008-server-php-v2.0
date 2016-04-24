<?php

/**
 * This is the model class for table "act_info_baidu_synchro_task".
 *
 * The followings are the available columns in table 'act_info_baidu_synchro_task':
 * @property string $id
 * @property string $last_max_act_id
 * @property integer $status
 * @property string $begin_time
 * @property string $end_time
 * @property integer $sum
 *
 * The followings are the available model relations:
 * @property ActInfo $lastMaxAct
 */
class ActInfoBaiduSynchroTask extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_info_baidu_synchro_task';
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
			array('last_max_act_id', 'length', 'max'=>10),
			array('begin_time, end_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, last_max_act_id, status, begin_time, end_time, sum', 'safe', 'on'=>'search'),
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
			'lastMaxAct' => array(self::BELONGS_TO, 'ActInfo', 'last_max_act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '好友动态任务关联id',
			'last_max_act_id' => '最后处理的最大的活动id',
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
		$criteria->compare('last_max_act_id',$this->last_max_act_id,true);
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
	 * @return ActIntoBaiduSynchroTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动同步至lbs云任务
     */
    public function add()
    {
        //如果还存在未执行完的任务则不添加
        if ($this->isExistTask()) {
            return FALSE;
        }
        
        $model = new ActInfoBaiduSynchroTask();
        $model->last_max_act_id = NULL;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
    
    /**
     * 刷新所有任务
     */
    public function refreshBaiduLbsSynchroTask()
    {
        $this->add();
        while ($task = $this->isExistTask()) {
            if (empty($task->last_max_act_id) && empty($task->begin_time) && ConstStatus::NORMAL == $task->status) {
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
        while ($nextAct = $this->isExistAct($task)) {
            $this->synchroToBaiduLbs($task, $nextAct['id']);
        }
        $task->status = 1;
        $task->end_time = date('Y-m-d H:i:s');
        $task->update();
    }
    
    
    /**
     * 是否存在未同步的活动信息
     * 
     * @param type $task 任务
     */
    private function isExistAct($task)
    {
        $nextAct = ActInfo::model()->find('t.id>:lastMaxActId', array(
            ':lastMaxActId' => empty($task->last_max_act_id) ? 0 : $task->last_max_act_id,
        ));
        
        return empty($nextAct) ? FALSE : $nextAct;
    }
    
    
    /**
     * 将对应活动同步至百度lbs云
     * 
     * @param type $task 任务
     * @param type $actId 活动id
     */
    public function synchroToBaiduLbs($task, $actId)
    {
        $model = ActInfo::model()->findByPk($actId);
        
        $lbsRst = Yii::app()->baiduLBS->detailPoi($model->id);
        $lbsPoi = $lbsRst['poi'];
        if ($model->status != ConstActStatus::PUBLISHING || $model->t_status == ConstActTimeStatus::OVER) {
            //删除百度lbs云信息
            if (!empty($lbsPoi)) {
                Yii::app()->baiduLBS->deletePoi($model->id);
            }
        }  else {
            //将信息更新至百度lbs云
            if (empty($lbsPoi)) {
                Yii::app()->baiduLBS->createPoi(
                        $model->title, 
                        $model->addr_name, 
                        NULL, 
                        $model->lat, 
                        $model->lon, 
                        3,
                        $this->getCustomParam($model)
                        );
            }  else {
                Yii::app()->baiduLBS->updatePoi(
                        $model->id,
                        $model->title, 
                        $model->addr_name, 
                        NULL, 
                        $model->lat, 
                        $model->lon, 
                        3,
                        $this->getCustomParam($model)
                        );
            }
        }
        
        //标注现在已经执行的进度
        $task->last_max_act_id = $actId;
        $task->sum = $task->sum + 1;
        $task->update();
    }
    
    
    //======================= baiduLBS处理 =======================
    
    /**
     * 取得自定义字段数组
     * 
     * @param type $model 活动数据
     */
    public function getCustomParam($model)
    {
        $act = array(
            'act_id' => $model->id,
            'act_title' => $model->title,
            'act_intro' => $model->intro,
            'act_city_id' => $model->city_id,
            'act_tag_id' => $model->tag_id,
            'act_cost' => $model->cost,
            'act_lon' => $model->lon,
            'act_lat' => $model->lat,
            'act_addr_city' => $model->addr_city,
            'act_addr_area' => $model->addr_area,
            'act_addr_road' => $model->addr_road,
            'act_addr_num' => $model->addr_num,
            'act_addr_name' => $model->addr_name,
            'act_addr_route' => $model->addr_route,
            'act_contact_way' => $model->contact_way,
            'act_b_time' => $model->b_time,
            'act_e_time' => $model->e_time,
            'act_t_status' => $model->t_status,
            'act_t_status_rule' => $model->t_status_rule,
            'act_detail' => $model->detail,
            //'act_detail_all' => $model->detail_all,
            'act_can_enroll' => $model->can_enroll,
            'act_h_img_id' => $model->h_img_id,
            'act_status' => $model->status,
            'act_create_time' => $model->create_time,
            'act_update_time' => $model->update_time,
            'act_publish_time' => $model->publish_time,
            'act_lov_base_num' => $model->lov_base_num,
            'act_share_base_num' => $model->share_base_num,
            //ios用于检索的字段（ios特供：针对ios的sdk的bug）
            //'ios_search' => 'act',
        );
        if (1 == $model->t_status_rule) {
            $timeRuleM = ActTimeStatusRule::model()->findWeek($model->id);
            $weekRules = (empty($timeRuleM) || empty($timeRuleM->filter)) ? NULL : json_encode($timeRuleM->filter);
            $act['act_week_rules'] = $weekRules;
        }
        return $act;
    }
    
}
